<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with('order');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('payment_method')) {
            $query->byMethod($request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->byStatus($request->payment_status);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        // Get payments with pagination
        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate total amount for current filter
        $totalAmount = $query->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalAmount'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('order');

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        // Chỉ cho phép edit payment đang pending
        if (!$payment->canBeUpdated()) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Chỉ có thể cập nhật giao dịch đang chờ xử lý');
        }

        return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,completed,failed',
            'payment_note' => 'nullable|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($request, $payment) {
                $payment->update([
                    'payment_status' => $request->payment_status,
                    'payment_note' => $request->payment_note
                ]);

                // Cập nhật trạng thái đơn hàng nếu cần
                if ($request->payment_status === 'completed' && $payment->order) {
                    $payment->order->update(['status' => 'processing']);
                } elseif ($request->payment_status === 'failed' && $payment->order) {
                    $payment->order->update(['status' => 'cancelled']);
                }
            });

            // Redirect về trang danh sách thay vì trang chi tiết
            return redirect()->route('admin.payments.index')
                ->with('success', 'Cập nhật trạng thái thanh toán thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Process refund for the specified payment.
     */
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
                // Tạo giao dịch hoàn tiền mới
                Payment::create([
                    'order_id' => $payment->order_id,
                    'amount' => -$request->refund_amount, // Số âm để đánh dấu hoàn tiền
                    'payment_method' => $payment->payment_method,
                    'payment_status' => 'refunded',
                    'payment_note' => 'Hoàn tiền: ' . $request->refund_reason,
                    'momo_transaction_id' => $payment->momo_transaction_id
                ]);

                // Cập nhật giao dịch gốc nếu hoàn tiền toàn bộ
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

    /**
     * Update payment status via AJAX.
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed'
        ]);

        try {
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

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        try {
            // Chỉ cho phép xóa nếu có quyền và payment không phải completed
            if ($payment->payment_status === 'completed') {
                return redirect()->back()
                    ->with('error', 'Không thể xóa giao dịch đã hoàn thành');
            }

            $payment->delete();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Xóa giao dịch thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * Export payments to Excel.
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        // Có thể sử dụng Laravel Excel package

        return redirect()->back()
            ->with('info', 'Tính năng export đang được phát triển');
    }

    /**
     * Generate payment receipt.
     */
    public function receipt(Payment $payment)
    {
        $payment->load('order');

        return view('admin.payments.receipt', compact('payment'));
    }
}
