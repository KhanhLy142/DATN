<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private function validatePaymentStatusUpdate($payment, $newStatus)
    {
        if ($payment->payment_method === 'cod' && $newStatus === 'completed') {
            if ($payment->order && $payment->order->shipping) {
                if ($payment->order->shipping->shipping_status !== 'delivered') {
                    throw new \Exception('Đơn hàng COD chưa được giao hàng, không thể đánh dấu đã thanh toán!');
                }
            } else {
                throw new \Exception('Đơn hàng COD chưa có thông tin vận chuyển hoặc chưa được giao hàng!');
            }
        }

        return true;
    }

    public function index(Request $request)
    {
        $query = Payment::with('order');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('order_id', $search);
                }

                elseif (str_starts_with($search, '#') && is_numeric(substr($search, 1))) {
                    $orderId = substr($search, 1);
                    $q->where('order_id', $orderId);
                }
                else {
                    $q->where('vnpay_transaction_id', 'like', "%{$search}%")
                        ->orWhere('payment_note', 'like', "%{$search}%");

                    if (!is_numeric($search)) {
                        $q->orWhere('order_id', 'like', "%{$search}%");
                    }
                }
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        if ($request->filled('need_confirmation') && $request->need_confirmation == '1') {
            $query->where('payment_method', 'bank_transfer')
                ->where('payment_status', 'pending');
        }


        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalAmount = Payment::where(function($q) use ($request) {
            if ($request->filled('search')) {
                $search = trim($request->search);

                $q->where(function($subQ) use ($search) {
                    if (is_numeric($search)) {
                        $subQ->where('order_id', $search);
                    } elseif (str_starts_with($search, '#') && is_numeric(substr($search, 1))) {
                        $orderId = substr($search, 1);
                        $subQ->where('order_id', $orderId);
                    } else {
                        $subQ->where('vnpay_transaction_id', 'like', "%{$search}%")
                            ->orWhere('payment_note', 'like', "%{$search}%");

                        if (!is_numeric($search)) {
                            $subQ->orWhere('order_id', 'like', "%{$search}%");
                        }
                    }
                });
            }

            if ($request->filled('payment_method')) {
                $q->where('payment_method', $request->payment_method);
            }

            if ($request->filled('payment_status')) {
                $q->where('payment_status', $request->payment_status);
            }

            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('need_confirmation') && $request->need_confirmation == '1') {
                $q->where('payment_method', 'bank_transfer')
                    ->where('payment_status', 'pending');
            }
        })->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalAmount'));
    }


    public function show(Payment $payment)
    {
        $payment->load('order');

        return view('admin.payments.show', compact('payment'));
    }


    public function edit(Payment $payment)
    {
        if (!$payment->canBeUpdated()) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Chỉ có thể cập nhật giao dịch đang chờ xử lý');
        }

        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'payment_note' => 'nullable|string|max:500',
            'vnpay_transaction_id' => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $payment) {
                $this->validatePaymentStatusUpdate($payment, $request->payment_status);

                $updateData = [
                    'payment_status' => $request->payment_status,
                    'payment_note' => $request->payment_note
                ];

                if ($payment->payment_method === 'vnpay' && $request->filled('vnpay_transaction_id')) {
                    $updateData['vnpay_transaction_id'] = $request->vnpay_transaction_id;
                }

                $payment->update($updateData);

                if ($payment->order) {
                    switch ($request->payment_status) {
                        case 'completed':
                            if (in_array($payment->payment_method, ['vnpay', 'bank_transfer'])) {
                                if ($payment->order->status === 'pending') {
                                    $payment->order->update(['status' => 'processing']);
                                }
                            }
                            break;
                        case 'failed':
                            $payment->order->update(['status' => 'cancelled']);
                            break;
                    }
                }
            });

            return redirect()->route('admin.payments.index')
                ->with('success', 'Cập nhật trạng thái thanh toán thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:1|max:' . $payment->amount,
            'refund_reason' => 'required|string|max:500'
        ]);

        if (!$payment->canBeRefunded()) {
            return redirect()->back()
                ->with('error', 'Giao dịch này không thể hoàn tiền');
        }

        try {
            DB::transaction(function () use ($request, $payment) {
                Payment::create([
                    'order_id' => $payment->order_id,
                    'amount' => -$request->refund_amount,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => 'refunded',
                    'payment_note' => 'Hoàn tiền: ' . $request->refund_reason,
                    'vnpay_transaction_id' => $payment->vnpay_transaction_id
                ]);

                if ($request->refund_amount == $payment->amount) {
                    $payment->markAsRefunded($request->refund_reason);
                }
            });

            return redirect()->route('admin.payments.index')
                ->with('success', 'Hoàn tiền thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi hoàn tiền: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded'
        ]);

        try {
            $this->validatePaymentStatusUpdate($payment, $request->status);

            $payment->update(['payment_status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmBankTransfer(Payment $payment)
    {
        if ($payment->payment_method !== 'bank_transfer') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể xác nhận chuyển khoản ngân hàng');
        }

        if ($payment->payment_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được xử lý');
        }

        try {
            DB::transaction(function () use ($payment) {
                $payment->update([
                    'payment_status' => 'completed',
                    'payment_note' => 'Admin xác nhận đã nhận chuyển khoản - ' . now()->format('d/m/Y H:i')
                ]);

                if ($payment->order && $payment->order->status === 'pending') {
                    $payment->order->update(['status' => 'processing']);
                }
            });

            return redirect()->back()
                ->with('success', 'Đã xác nhận thanh toán thành công! Đơn hàng chuyển sang trạng thái xử lý.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xác nhận: ' . $e->getMessage());
        }
    }

    public function confirmVNPay(Payment $payment)
    {
        if ($payment->payment_method !== 'vnpay') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể xác nhận thanh toán VNPay');
        }

        if ($payment->payment_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được xử lý');
        }

        try {
            DB::transaction(function () use ($payment) {
                $payment->update([
                    'payment_status' => 'completed',
                    'payment_note' => 'Admin xác nhận thanh toán VNPay thành công - ' . now()->format('d/m/Y H:i')
                ]);

                if ($payment->order && $payment->order->status === 'pending') {
                    $payment->order->update(['status' => 'processing']);
                }
            });

            return redirect()->back()
                ->with('success', 'Đã xác nhận thanh toán VNPay thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xác nhận: ' . $e->getMessage());
        }
    }

    public function receipt(Payment $payment)
    {
        $payment->load('order');

        return view('admin.payments.receipt', compact('payment'));
    }
}
