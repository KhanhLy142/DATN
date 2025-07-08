<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private function updateInventoryForOrder($orderItems, $operation = 'decrease')
    {
        foreach ($orderItems as $item) {
            $product = is_object($item) ? $item->product : Product::find($item['product_id']);
            $quantity = is_object($item) ? $item->quantity : $item['quantity'];
            $variantId = is_object($item) ? $item->variant_id : ($item['variant_id'] ?? null);

            $inventory = Inventory::where('product_id', $product->id)->first();

            if ($inventory) {
                if ($operation === 'decrease') {
                    $inventory->decrement('quantity', $quantity);
                } else {
                    $inventory->increment('quantity', $quantity);
                }
            } else {

                if ($operation === 'increase') {
                    Inventory::create([
                        'product_id' => $product->id,
                        'quantity' => $quantity
                    ]);
                }
            }

            if ($variantId) {
                $variant = $product->variants()->find($variantId);
                if ($variant) {
                    if ($operation === 'decrease') {
                        $variant->decrement('stock_quantity', $quantity);
                    } else {
                        $variant->increment('stock_quantity', $quantity);
                    }
                }
            } else {
                $variantCount = $product->variants()->count();
                if ($variantCount > 0) {
                    $quantityPerVariant = floor($quantity / $variantCount);
                    $remainder = $quantity % $variantCount;

                    $variants = $product->variants;
                    foreach ($variants as $index => $variant) {
                        $changeQuantity = $quantityPerVariant;
                        if ($index < $remainder) {
                            $changeQuantity += 1;
                        }

                        if ($operation === 'decrease') {
                            $variant->decrement('stock_quantity', $changeQuantity);
                        } else {
                            $variant->increment('stock_quantity', $changeQuantity);
                        }
                    }
                }
            }

            $totalVariantStock = $product->variants()->sum('stock_quantity');
            $inventoryStock = $inventory ? $inventory->quantity : 0;
            $newStock = max($totalVariantStock, $inventoryStock);

            $product->update(['stock' => $newStock]);
        }
    }

    private function getOrderMetadata($order)
    {
        try {
            $decoded = json_decode($order->note, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            return [];
        }
    }
    private function getCustomerNote($order)
    {
        $metadata = $this->getOrderMetadata($order);
        return $metadata['__customer_note__'] ?? '';
    }

    private function updateCustomerNote($order, $customerNote)
    {
        $metadata = $this->getOrderMetadata($order);
        $metadata['__customer_note__'] = $customerNote;
        $metadata['__updated_at__'] = now()->toISOString();
        $metadata['__admin_updated__'] = true;

        return json_encode($metadata);
    }

    private function validateOrderStatusUpdate($order, $newStatus)
    {
        if (in_array($newStatus, ['processing', 'shipped'])) {
            if ($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer'])) {
                $order->payment->refresh();

                if ($order->payment->payment_status !== 'completed') {
                    throw new \Exception('Đơn hàng thanh toán bằng ' .
                        ($order->payment->payment_method === 'vnpay' ? 'VNPay' : 'Chuyển khoản') .
                        ' chưa được thanh toán, không thể chuyển sang trạng thái vận chuyển!');
                }
            }
        }

        return true;
    }

    private function syncOrderStatus($order)
    {
        Log::info('Syncing order status for order: ' . $order->id);

        $order->refresh();
        $order->load(['payment', 'shipping']);

        $shouldComplete = false;

        if ($order->shipping && $order->shipping->shipping_status === 'delivered') {
            Log::info('Shipping is delivered, checking payment...');

            if ($order->payment) {
                if ($order->payment->payment_method === 'cod') {
                    if ($order->payment->payment_status !== 'completed') {
                        $order->payment->update([
                            'payment_status' => 'completed',
                            'payment_note' => 'Tự động hoàn thành thanh toán COD khi giao hàng - ' . now()->format('d/m/Y H:i')
                        ]);
                        Log::info('COD payment marked as completed');
                    }
                    $shouldComplete = true;
                }
                elseif (in_array($order->payment->payment_method, ['vnpay', 'bank_transfer'])) {
                    if ($order->payment->payment_status === 'completed') {
                        $shouldComplete = true;
                    }
                }
            }
        }

        if ($shouldComplete && $order->status !== 'completed') {
            $order->update(['status' => 'completed']);
            Log::info('Order status updated to completed');

            return true;
        }

        Log::info('Order status sync complete. Final status: ' . $order->status);
        return false;
    }


    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product', 'payment', 'shipping']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {

                if (is_numeric($search)) {
                    $q->where('id', $search);
                }

                elseif (str_starts_with($search, '#') && is_numeric(substr($search, 1))) {
                    $orderId = substr($search, 1);
                    $q->where('id', $orderId);
                }

                elseif (str_contains(strtolower($search), 'trk')) {
                    $q->where('tracking_number', 'like', "%{$search}%");
                }

                else {
                    $q->whereHas('customer', function($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });

                    $q->orWhere('tracking_number', 'like', "%{$search}%");
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.product', 'payment', 'shipping']);
        $metadata = $this->getOrderMetadata($order);
        $customerNote = $this->getCustomerNote($order);

        return view('admin.orders.show', compact('order', 'metadata', 'customerNote'));
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'orderItems.product', 'payment', 'shipping']);
        $customerNote = $this->getCustomerNote($order);

        return view('admin.orders.edit', compact('order', 'customerNote'));
    }

    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'shipping_address' => 'sometimes|required|string',
            'shipping_method' => 'sometimes|required|in:standard,express',
            'ghn_province_id' => 'nullable|integer',
            'ghn_district_id' => 'nullable|integer',
            'ghn_ward_code' => 'nullable|string|max:20',
            'province_name' => 'nullable|string|max:100',
            'district_name' => 'nullable|string|max:100',
            'ward_name' => 'nullable|string|max:100',
            'customer_note' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            Log::info('Updating order ' . $order->id . ' from status ' . $order->status . ' to ' . $request->status);

            $this->validateOrderStatusUpdate($order, $request->status);

            $updatedNote = $this->updateCustomerNote($order, $request->customer_note ?? '');

            $order->update([
                'status' => $request->status,
                'note' => $updatedNote
            ]);

            if ($order->shipping) {
                $shippingUpdates = [];

                if ($request->filled('shipping_address')) {
                    $shippingUpdates['shipping_address'] = $request->shipping_address;
                }

                if ($request->filled('shipping_method')) {
                    $shippingUpdates['shipping_method'] = $request->shipping_method;
                    $shippingUpdates['shipping_fee'] = $request->shipping_method === 'express' ? 30000 : 0;
                }

                if ($request->filled('ghn_province_id')) {
                    $shippingUpdates['ghn_province_id'] = $request->ghn_province_id;
                }
                if ($request->filled('ghn_district_id')) {
                    $shippingUpdates['ghn_district_id'] = $request->ghn_district_id;
                }
                if ($request->filled('ghn_ward_code')) {
                    $shippingUpdates['ghn_ward_code'] = $request->ghn_ward_code;
                }
                if ($request->filled('province_name')) {
                    $shippingUpdates['province_name'] = $request->province_name;
                }
                if ($request->filled('district_name')) {
                    $shippingUpdates['district_name'] = $request->district_name;
                }
                if ($request->filled('ward_name')) {
                    $shippingUpdates['ward_name'] = $request->ward_name;
                }

                $shippingStatus = $order->shipping->shipping_status;
                switch ($request->status) {
                    case 'processing':
                        if ($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer'])) {
                            if ($order->payment->payment_status === 'completed') {
                                $shippingStatus = 'confirmed';
                            }
                        } else {
                            $shippingStatus = 'confirmed';
                        }
                        break;
                    case 'shipped':
                        $shippingStatus = 'shipping';
                        if (empty($order->shipping->tracking_code)) {
                            $shippingUpdates['tracking_code'] = 'TRK' . strtoupper(uniqid());
                        }
                        break;
                    case 'completed':
                        $shippingStatus = 'delivered';
                        break;
                    case 'cancelled':
                        $shippingStatus = 'pending';
                        break;
                }
                $shippingUpdates['shipping_status'] = $shippingStatus;

                if (!empty($shippingUpdates)) {
                    $order->shipping->update($shippingUpdates);
                }
            }

            if ($order->payment) {
                $paymentStatus = $order->payment->payment_status;

                switch ($request->status) {
                    case 'completed':
                        $this->syncOrderStatus($order);
                        break;
                    case 'cancelled':
                        $paymentStatus = 'failed';
                        $order->payment->update(['payment_status' => $paymentStatus]);
                        break;
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function cancel(Order $order)
    {
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()
                ->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xử lý hoặc đang xử lý!');
        }

        DB::beginTransaction();

        try {
            $this->updateInventoryForOrder($order->orderItems()->with('product')->get(), 'increase');

            $order->update(['status' => 'cancelled']);

            if ($order->payment) {
                $paymentStatus = 'failed';
                $paymentNote = 'Đơn hàng bị hủy bởi Admin - ' . now()->format('d/m/Y H:i');

                if ($order->payment->payment_status === 'completed') {
                    $paymentStatus = 'refunded';
                    $paymentNote = 'Hoàn tiền do hủy đơn hàng - ' . now()->format('d/m/Y H:i');
                }

                $order->payment->update([
                    'payment_status' => $paymentStatus,
                    'payment_note' => $paymentNote
                ]);
            }

            if ($order->shipping) {
                $shippingStatus = 'returned';
                $shippingNote = 'Đơn hàng bị hủy bởi Admin - ' . now()->format('d/m/Y H:i');

                if (in_array($order->shipping->shipping_status, ['pending', 'confirmed'])) {
                    $shippingStatus = 'pending';
                }
                elseif ($order->shipping->shipping_status === 'delivered') {
                    $shippingStatus = 'delivered';
                    $shippingNote = 'Đã giao hàng trước khi hủy - ' . now()->format('d/m/Y H:i');
                }

                $order->shipping->update([
                    'shipping_status' => $shippingStatus,
                    'shipping_note' => $shippingNote
                ]);
            }

            DB::commit();

            Log::info('Admin cancelled order successfully', [
                'order_id' => $order->id,
                'order_status' => 'cancelled',
                'payment_status' => $order->payment ? $order->payment->payment_status : 'N/A',
                'shipping_status' => $order->shipping ? $order->shipping->shipping_status : 'N/A'
            ]);

            return redirect()->back()
                ->with('success', 'Đơn hàng đã được hủy thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin cancel order error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function massFixCancelledOrders()
    {
        try {
            $stuckOrders = Order::with(['payment', 'shipping'])
                ->where('status', 'cancelled')
                ->where(function($query) {
                    $query->whereHas('payment', function($q) {
                        $q->where('payment_status', 'pending');
                    })
                        ->orWhereHas('shipping', function($q) {
                            $q->whereNotIn('shipping_status', ['pending', 'returned', 'delivered']);
                        });
                })
                ->get();

            $fixedCount = 0;
            $errors = [];

            foreach ($stuckOrders as $order) {
                DB::beginTransaction();
                try {
                    if ($order->payment && $order->payment->payment_status === 'pending') {
                        $order->payment->update([
                            'payment_status' => 'failed',
                            'payment_note' => 'Mass-fix: Đơn hàng đã bị hủy - ' . now()->format('d/m/Y H:i')
                        ]);
                    }

                    if ($order->shipping) {
                        $currentStatus = $order->shipping->shipping_status;

                        if (!in_array($currentStatus, ['pending', 'returned', 'delivered'])) {
                            $newStatus = in_array($currentStatus, ['pending', 'confirmed']) ? 'pending' : 'returned';

                            $order->shipping->update([
                                'shipping_status' => $newStatus,
                                'shipping_note' => "Mass-fix: Từ {$currentStatus} về {$newStatus} do đơn hàng bị hủy - " . now()->format('d/m/Y H:i')
                            ]);
                        }
                    }

                    $fixedCount++;
                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    $errors[] = "Order #{$order->id}: " . $e->getMessage();
                }
            }

            $message = "Đã sửa {$fixedCount}/{$stuckOrders->count()} đơn hàng bị stuck.";
            if (!empty($errors)) {
                $message .= " Lỗi: " . implode(', ', array_slice($errors, 0, 3));
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function markAsDelivered(Order $order)
    {
        if ($order->status !== 'shipped') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể đánh dấu đã giao cho đơn hàng đang được vận chuyển!');
        }

        DB::beginTransaction();

        try {
            Log::info('Manual mark as delivered for order: ' . $order->id);

            if ($order->shipping) {
                $order->shipping->update(['shipping_status' => 'delivered']);
            }

            $this->syncOrderStatus($order);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Đơn hàng đã được đánh dấu là đã giao thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in markAsDelivered: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function fixStuckOrders()
    {
        try {
            $stuckOrders = Order::with(['payment', 'shipping'])
                ->where(function($query) {
                    $query->where('status', '!=', 'completed')
                        ->whereHas('shipping', function($q) {
                            $q->where('shipping_status', 'delivered');
                        });
                })
                ->get();

            $fixedCount = 0;
            foreach ($stuckOrders as $order) {
                DB::beginTransaction();
                try {
                    if ($this->syncOrderStatus($order)) {
                        $fixedCount++;
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to fix order ' . $order->id . ': ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Đã sửa {$fixedCount} đơn hàng bị stuck"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceUpdateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled'
        ]);

        DB::beginTransaction();

        try {
            $order->update(['status' => $request->status]);

            if ($order->payment) {
                switch ($request->status) {
                    case 'completed':
                        $order->payment->update(['payment_status' => 'completed']);
                        break;
                    case 'cancelled':
                        $order->payment->update(['payment_status' => 'failed']);
                        break;
                }
            }

            if ($order->shipping) {
                switch ($request->status) {
                    case 'processing':
                        $order->shipping->update(['shipping_status' => 'confirmed']);
                        break;
                    case 'shipped':
                        $order->shipping->update(['shipping_status' => 'shipping']);
                        break;
                    case 'completed':
                        $order->shipping->update(['shipping_status' => 'delivered']);
                        break;
                    case 'cancelled':
                        $order->shipping->update(['shipping_status' => 'cancelled']);
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã force update trạng thái đơn hàng'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncOrderStatusManually(Order $order)
    {
        DB::beginTransaction();

        try {
            $updated = $this->syncOrderStatus($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'updated' => $updated,
                'message' => $updated ? 'Đã đồng bộ trạng thái đơn hàng' : 'Trạng thái đơn hàng đã đúng'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
