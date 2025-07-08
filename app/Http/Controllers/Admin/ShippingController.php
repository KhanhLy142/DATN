<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipping::with(['order.customer', 'order.payment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('shipping_address', 'like', "%{$search}%")
                    ->orWhere('tracking_code', 'like', "%{$search}%");
            });
        }


        if ($request->filled('shipping_method')) {
            $query->where('shipping_method', $request->shipping_method);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $request->shipping_status);
        }

        if ($request->filled('province')) {
            $query->where('province_name', 'like', "%{$request->province}%");
        }


        if ($request->filled('need_payment_confirmation') && $request->need_payment_confirmation == '1') {
            $query->whereHas('order.payment', function($q) {
                $q->whereIn('payment_method', ['vnpay', 'bank_transfer'])
                    ->where('payment_status', 'pending');
            });
        }

        $query->orderBy('created_at', 'desc');


        $shippings = $query->paginate(15)->withQueryString();

        $stats = [
            'pending' => Shipping::where('shipping_status', 'pending')->count(),
            'confirmed' => Shipping::where('shipping_status', 'confirmed')->count(),
            'shipping' => Shipping::where('shipping_status', 'shipping')->count(),
            'delivered' => Shipping::where('shipping_status', 'delivered')->count(),
        ];

        $stats['need_payment_confirmation'] = Shipping::whereHas('order.payment', function($q) {
            $q->whereIn('payment_method', ['vnpay', 'bank_transfer'])
                ->where('payment_status', 'pending');
        })->count();

        return view('admin.shippings.index', compact('shippings', 'stats'));
    }

    public function show(Shipping $shipping)
    {
        $shipping->load(['order.customer', 'order.payment']);

        return view('admin.shippings.show', compact('shipping'));
    }


    public function edit(Shipping $shipping)
    {
        $shipping->load(['order.customer', 'order.payment']);

        return view('admin.shippings.edit', compact('shipping'));
    }

    public function update(Request $request, Shipping $shipping)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|in:standard,express',
            'shipping_status' => 'required|in:pending,confirmed,shipping,delivered',
            'province_name' => 'nullable|string|max:255',
            'shipping_fee' => 'nullable|numeric|min:0',
            'shipping_note' => 'nullable|string|max:1000',
            'tracking_code' => 'nullable|string|max:100|unique:shippings,tracking_code,' . $shipping->id
        ], [
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'shipping_method.required' => 'Vui lòng chọn phương thức vận chuyển',
            'shipping_status.required' => 'Vui lòng chọn trạng thái vận chuyển',
            'tracking_code.unique' => 'Mã vận đơn đã tồn tại'
        ]);

        try {
            DB::beginTransaction();

            $this->validateShippingUpdate($shipping, $request->shipping_status);

            $oldStatus = $shipping->shipping_status;
            Log::info('Updating shipping status from ' . $oldStatus . ' to ' . $request->shipping_status);

            $updateData = [
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'shipping_status' => $request->shipping_status,
                'shipping_fee' => $request->shipping_fee ?? 0,
                'shipping_note' => $request->shipping_note,
                'tracking_code' => $request->tracking_code
            ];

            if ($request->filled('province_name')) {
                $updateData['province_name'] = $request->province_name;
            }

            $shipping->update($updateData);
            Log::info('Shipping updated successfully');

            if ($oldStatus !== $request->shipping_status) {
                $this->updateOrderStatusBasedOnShipping($shipping, $request->shipping_status);
            }

            DB::commit();

            $successMessage = 'Cập nhật thông tin vận chuyển thành công!';
            if ($request->shipping_status === 'delivered' && $shipping->order && $shipping->order->payment && $shipping->order->payment->payment_method == 'cod') {
                $successMessage .= ' Đơn hàng COD đã được tự động hoàn thành.';
            }

            return redirect()->route('admin.shippings.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating shipping: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function validateShippingUpdate($shipping, $newShippingStatus)
    {
        if (in_array($newShippingStatus, ['confirmed', 'shipping', 'delivered'])) {
            $order = $shipping->order;

            if ($order && $order->payment) {
                if (in_array($order->payment->payment_method, ['vnpay', 'bank_transfer'])) {
                    $order->payment->refresh();

                    if ($order->payment->payment_status !== 'completed') {
                        $paymentMethodName = $order->payment->payment_method === 'vnpay' ? 'VNPay' : 'Chuyển khoản ngân hàng';

                        throw new \Exception("❌ Không thể cập nhật trạng thái vận chuyển!\n\n" .
                            "🔍 Chi tiết: Đơn hàng này sử dụng phương thức thanh toán {$paymentMethodName} " .
                            "nhưng chưa được thanh toán (Trạng thái: {$order->payment->payment_status}).\n\n" .
                            "✅ Vui lòng xác nhận thanh toán trước khi tiến hành vận chuyển.");
                    }
                }
            }
        }

        return true;
    }

    private function updateOrderStatusBasedOnShipping($shipping, $newShippingStatus)
    {
        if (!$shipping->order) {
            Log::warning('No order found for shipping ID: ' . $shipping->id);
            return;
        }

        $order = $shipping->order;
        $oldOrderStatus = $order->status;

        Log::info('Processing order status update for order ID: ' . $order->id);
        Log::info('Current order status: ' . $oldOrderStatus);
        Log::info('New shipping status: ' . $newShippingStatus);

        switch ($newShippingStatus) {
            case 'confirmed':
                if ($order->status == 'pending') {
                    $order->updateQuietly(['status' => 'processing']);
                    Log::info('Order status updated from pending to processing');
                }
                break;

            case 'shipping':
                if (in_array($order->status, ['pending', 'processing'])) {
                    $order->updateQuietly(['status' => 'shipped']);
                    Log::info('Order status updated to shipped');
                }
                break;

            case 'delivered':

                $shouldCompleteOrder = false;

                if ($order->payment) {
                    if ($order->payment->payment_method === 'cod') {
                        $order->payment->updateQuietly([
                            'payment_status' => 'completed',
                            'payment_note' => 'Tự động hoàn thành thanh toán COD khi giao hàng - ' . now()->format('d/m/Y H:i')
                        ]);
                        Log::info('COD payment auto-completed');
                        $shouldCompleteOrder = true;

                    } elseif (in_array($order->payment->payment_method, ['vnpay', 'bank_transfer'])) {
                        if ($order->payment->payment_status === 'completed') {
                            $shouldCompleteOrder = true;
                            Log::info('VNPay/Bank Transfer already paid, completing order');
                        } else {
                            Log::info('VNPay/Bank Transfer not paid yet, order remains as shipped');
                        }
                    }
                } else {
                    $shouldCompleteOrder = true;
                }

                if ($shouldCompleteOrder) {
                    $order->updateQuietly(['status' => 'completed']);
                    Log::info('Order status updated to completed');
                } else {
                    $order->updateQuietly(['status' => 'shipped']);
                    Log::info('Order status updated to shipped (payment pending)');
                }
                break;
        }

        $order->refresh();
        Log::info('Final order status: ' . $order->status);
    }

    public function markAsShipped(Request $request, Shipping $shipping)
    {
        try {
            DB::beginTransaction();

            $newStatus = '';

            switch ($shipping->shipping_status) {
                case 'pending':
                    $newStatus = 'confirmed';
                    break;
                case 'confirmed':
                    $newStatus = 'shipping';
                    if (empty($shipping->tracking_code)) {
                        $shipping->update([
                            'tracking_code' => 'TRK' . strtoupper(uniqid())
                        ]);
                    }
                    break;
                default:
                    return redirect()->back()
                        ->with('error', 'Không thể chuyển trạng thái từ ' . $shipping->shipping_status);
            }

            $this->validateShippingUpdate($shipping, $newStatus);

            $shipping->update(['shipping_status' => $newStatus]);
            Log::info('Shipping status updated to: ' . $newStatus);

            $this->updateOrderStatusBasedOnShipping($shipping, $newStatus);

            DB::commit();

            $statusText = [
                'confirmed' => 'đã xác nhận',
                'shipping' => 'đang giao hàng'
            ];

            return redirect()->back()
                ->with('success', 'Đã cập nhật trạng thái vận chuyển thành "' . ($statusText[$newStatus] ?? $newStatus) . '"');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in markAsShipped: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function markAsDelivered(Shipping $shipping)
    {
        if ($shipping->shipping_status !== 'shipping') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể đánh dấu đã giao cho đơn hàng đang được vận chuyển!');
        }

        try {
            DB::beginTransaction();

            Log::info('Starting markAsDelivered for shipping ID: ' . $shipping->id);

            try {
                $this->validateShippingUpdate($shipping, 'delivered');
            } catch (\Exception $e) {
                Log::warning('Validation warning for delivered status: ' . $e->getMessage());
            }

            $shipping->update(['shipping_status' => 'delivered']);
            Log::info('Shipping status updated to delivered');

            $this->updateOrderStatusBasedOnShipping($shipping, 'delivered');

            DB::commit();
            Log::info('Transaction committed successfully');

            $shipping->refresh();
            $order = $shipping->order;
            $order->refresh();

            Log::info('Final check - Order status: ' . $order->status);
            if ($order->payment) {
                $order->payment->refresh();
                Log::info('Final check - Payment status: ' . $order->payment->payment_status);
            }

            $successMessage = 'Đã đánh dấu giao hàng thành công!';
            if ($order->payment && $order->payment->payment_method == 'cod') {
                $successMessage .= ' Đơn hàng COD đã được tự động hoàn thành.';
            } elseif ($order->status === 'completed') {
                $successMessage .= ' Đơn hàng đã hoàn thành.';
            } elseif ($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer']) && $order->payment->payment_status !== 'completed') {
                $successMessage .= ' ⚠️ Lưu ý: Đơn hàng chưa thanh toán, trạng thái vẫn là "Đang giao hàng".';
            }

            return redirect()->back()
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in markAsDelivered: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function generateTrackingCode(Shipping $shipping)
    {
        try {
            $trackingCode = 'TRK' . strtoupper(uniqid());
            $shipping->update(['tracking_code' => $trackingCode]);

            return response()->json([
                'success' => true,
                'tracking_code' => $trackingCode,
                'message' => 'Tạo mã vận đơn thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkPaymentStatus(Shipping $shipping)
    {
        try {
            $order = $shipping->order;

            if (!$order || !$order->payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin thanh toán'
                ]);
            }

            $payment = $order->payment;
            $payment->refresh();

            $canShip = true;
            $message = 'Đơn hàng có thể vận chuyển';

            if (in_array($payment->payment_method, ['vnpay', 'bank_transfer'])) {
                if ($payment->payment_status !== 'completed') {
                    $canShip = false;
                    $message = 'Đơn hàng chưa được thanh toán, không thể vận chuyển';
                }
            }

            return response()->json([
                'success' => true,
                'can_ship' => $canShip,
                'message' => $message,
                'payment_status' => $payment->payment_status,
                'payment_method' => $payment->payment_method
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function statistics()
    {
        $stats = [
            'pending' => Shipping::where('shipping_status', 'pending')->count(),
            'confirmed' => Shipping::where('shipping_status', 'confirmed')->count(),
            'shipping' => Shipping::where('shipping_status', 'shipping')->count(),
            'delivered' => Shipping::where('shipping_status', 'delivered')->count(),
        ];

        $methodStats = [
            'standard' => Shipping::where('shipping_method', 'standard')->count(),
            'express' => Shipping::where('shipping_method', 'express')->count(),
        ];

        $monthlyStats = Shipping::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(shipping_fee) as total_fee
            ')
            ->whereYear('created_at', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        return view('admin.shippings.statistics', compact('stats', 'methodStats', 'monthlyStats'));
    }


    public function getByOrder($orderId)
    {
        $shipping = Shipping::with(['order.payment'])
            ->where('order_id', $orderId)
            ->first();

        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin vận chuyển'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shipping
        ]);
    }
}
