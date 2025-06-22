<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product', 'payment', 'shipping']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('status', 'active')
            ->orWhere('status', 1)
            ->with('inventory')
            ->get();

        return view('admin.orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,momo', // Đồng bộ với enum trong bảng payments
            'shipping_address' => 'required|string',
            'shipping_method' => 'required|in:standard,express' // Đồng bộ với enum trong bảng shippings
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $productPrice = $product->base_price ?? $product->price ?? 0;

                // Check inventory
                $availableStock = $product->inventory ? $product->inventory->quantity : ($product->stock ?? 0);

                if ($availableStock < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ số lượng trong kho. Còn lại: {$availableStock}");
                }

                $subtotal = $productPrice * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $productPrice,
                    'product' => $product
                ];
            }

            // Calculate shipping fee
            $shippingFee = 0;
            if ($request->shipping_method === 'express') {
                $shippingFee = 30000; // Phí giao hàng nhanh
            }

            $totalAmount += $shippingFee;

            // Create order
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            // Create order items and update inventory
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Update inventory
                if ($item['product']->inventory) {
                    $item['product']->inventory->decrement('quantity', $item['quantity']);
                } elseif (isset($item['product']->stock)) {
                    $item['product']->decrement('stock', $item['quantity']);
                }
            }

            // Create payment record - đồng bộ với bảng payments
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $request->payment_method, // 'cod' hoặc 'momo'
                'payment_status' => 'pending',
                'momo_transaction_id' => null, // Sẽ cập nhật sau khi thanh toán MoMo thành công
                'payment_note' => $request->payment_note ?? null
            ]);

            // Create shipping record - đồng bộ với bảng shippings
            Shipping::create([
                'order_id' => $order->id,
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method, // 'standard' hoặc 'express'
                'shipping_status' => 'pending',
                'province' => $request->province ?? null,
                'shipping_fee' => $shippingFee,
                'shipping_note' => $request->shipping_note ?? null,
                'tracking_code' => null // Sẽ tạo khi bắt đầu giao hàng
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.product', 'payment', 'shipping']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order
     */
    public function edit(Order $order)
    {
        $order->load(['customer', 'orderItems.product', 'payment', 'shipping']);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'shipping_address' => 'sometimes|required|string',
            'shipping_method' => 'sometimes|required|in:standard,express'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Update order status
            $order->update(['status' => $request->status]);

            // Update shipping info if provided
            if ($order->shipping) {
                $shippingUpdates = [];

                if ($request->filled('shipping_address')) {
                    $shippingUpdates['shipping_address'] = $request->shipping_address;
                }

                if ($request->filled('shipping_method')) {
                    $shippingUpdates['shipping_method'] = $request->shipping_method;
                    // Cập nhật phí vận chuyển nếu thay đổi phương thức
                    $shippingUpdates['shipping_fee'] = $request->shipping_method === 'express' ? 30000 : 0;
                }

                // Tự động cập nhật shipping_status dựa trên order status
                $shippingStatus = $order->shipping->shipping_status;
                switch ($request->status) {
                    case 'processing':
                        $shippingStatus = 'confirmed';
                        break;
                    case 'shipped':
                        $shippingStatus = 'shipped';
                        // Tạo tracking code nếu chưa có
                        if (empty($order->shipping->tracking_code)) {
                            $shippingUpdates['tracking_code'] = 'TRK' . strtoupper(uniqid());
                        }
                        break;
                    case 'delivered':
                        $shippingStatus = 'delivered';
                        break;
                    case 'cancelled':
                        $shippingStatus = 'cancelled';
                        break;
                }
                $shippingUpdates['shipping_status'] = $shippingStatus;

                if (!empty($shippingUpdates)) {
                    $order->shipping->update($shippingUpdates);
                }
            }

            // Update payment status based on order status
            if ($order->payment) {
                $paymentStatus = $order->payment->payment_status;

                // Tự động cập nhật payment_status dựa trên order status
                switch ($request->status) {
                    case 'delivered':
                        $paymentStatus = 'completed';
                        break;
                    case 'cancelled':
                        $paymentStatus = 'failed';
                        break;
                }

                $order->payment->update(['payment_status' => $paymentStatus]);
            }

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the order
     */
    public function cancel(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xử lý!');
        }

        DB::beginTransaction();

        try {
            // Restore inventory
            foreach ($order->orderItems as $item) {
                if ($item->product->inventory) {
                    $item->product->inventory->increment('quantity', $item->quantity);
                } elseif (isset($item->product->stock)) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Update order status
            $order->update(['status' => 'cancelled']);

            // Update payment status
            if ($order->payment) {
                $order->payment->update(['payment_status' => 'failed']);
            }

            // Update shipping status
            if ($order->shipping) {
                $order->shipping->update(['shipping_status' => 'cancelled']);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Đơn hàng đã được hủy thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order from storage
     */
    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể xóa đơn hàng đang chờ xử lý!');
        }

        DB::beginTransaction();

        try {
            // Restore inventory
            foreach ($order->orderItems as $item) {
                if ($item->product->inventory) {
                    $item->product->inventory->increment('quantity', $item->quantity);
                } elseif (isset($item->product->stock)) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Delete related records
            $order->orderItems()->delete();
            $order->payment()->delete();
            $order->shipping()->delete();

            // Delete order
            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được xóa thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
