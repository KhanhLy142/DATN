<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\User;
use App\Models\Customer;
use App\Models\Inventory;
use App\Services\GHNService;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $ghnService;
    protected $vnpayService;

    public function __construct(GHNService $ghnService, VNPayService $vnpayService)
    {
        $this->ghnService = $ghnService;
        $this->vnpayService = $vnpayService;
    }


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

    private function getCurrentCustomer()
    {
        Log::info('=== getCurrentCustomer START ===');

        $user = null;
        $guardUsed = null;

        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();
            $guardUsed = 'customer';
            Log::info('User found via CUSTOMER guard', [
                'user_id' => $user->id,
                'user_type' => $user->user_type ?? 'unknown',
                'user_email' => $user->email
            ]);
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $guardUsed = 'web';
            Log::info('User found via WEB guard', [
                'user_id' => $user->id,
                'user_type' => $user->user_type ?? 'unknown',
                'user_email' => $user->email
            ]);
        } elseif (Auth::check()) {
            $user = Auth::user();
            $guardUsed = 'default';
            Log::info('User found via DEFAULT auth', [
                'user_id' => $user->id,
                'user_type' => $user->user_type ?? 'unknown',
                'user_email' => $user->email
            ]);
        }

        if (!$user) {
            Log::error('NO USER FOUND in any guard', [
                'customer_guard' => Auth::guard('customer')->check(),
                'web_guard' => Auth::guard('web')->check(),
                'default_auth' => Auth::check(),
                'session_data' => session()->all()
            ]);
            throw new \Exception('User not authenticated in any guard');
        }

        if (!$user->isCustomer()) {
            Log::warning('User is not a customer', [
                'user_id' => $user->id,
                'user_type' => $user->user_type ?? 'null',
                'guard_used' => $guardUsed
            ]);

            if (isset($user->user_type) && $user->user_type !== 'customer') {
                throw new \Exception('User is not a customer, user_type: ' . $user->user_type);
            }
        }

        $customer = \App\Models\Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            Log::info('Customer record not found, attempting to create', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->name
            ]);

            try {
                $customer = \App\Models\Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]);

                Log::info('Customer record created successfully', [
                    'customer_id' => $customer->id,
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create customer record', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Could not create customer record: ' . $e->getMessage());
            }
        }

        Log::info('getCurrentCustomer SUCCESS', [
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'guard_used' => $guardUsed,
            'customer_email' => $customer->email
        ]);

        return $customer;
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'province_id' => 'required|integer',
            'district_id' => 'required|integer',
            'ward_code' => 'required|string',
            'shipping_method' => 'required|in:standard,express'
        ]);

        try {
            $customer = $this->getCurrentCustomer();

            $cartItems = Cart::with(['product', 'variant'])
                ->where('customer_id', $customer->id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng trống'
                ]);
            }

            $totalWeight = $cartItems->sum(function($item) {
                $weight = $item->product->weight ?? 100;
                return $weight * $item->quantity;
            });

            $orderValue = $cartItems->sum(function($item) {
                return $item->price * $item->quantity;
            });

            $ghnData = [
                'to_province_id' => $request->province_id,
                'to_district_id' => $request->district_id,
                'to_ward_code' => $request->ward_code,
                'weight' => max($totalWeight, 500),
                'length' => 30,
                'width' => 20,
                'height' => 15,
                'insurance_value' => min($orderValue, 5000000),
                'service_type_id' => $request->shipping_method === 'express' ? 1 : 2
            ];

            $shippingResult = $this->ghnService->calculateShippingFee($ghnData);

            if (!$shippingResult['success']) {
                $baseFee = $request->shipping_method === 'express' ? 50000 : 30000;

                return response()->json([
                    'success' => true,
                    'shipping_fee' => $baseFee,
                    'original_fee' => $baseFee,
                    'shipping_discount' => 0,
                    'is_free_shipping' => false,
                    'service_id' => $request->shipping_method === 'express' ? 1 : 2,
                    'estimated_delivery' => $this->getEstimatedDelivery($request->shipping_method),
                    'test_mode' => true,
                    'message' => 'Sử dụng phí cố định do API GHN không khả dụng'
                ]);
            }

            $appliedDiscount = session('applied_discount');
            $originalFee = $shippingResult['fee'];
            $shippingDiscount = 0;
            $finalFee = $originalFee;

            if ($appliedDiscount) {
                $discount = $appliedDiscount['discount_amount'] ?? 0;
                $shippingDiscount = $appliedDiscount['shipping_discount'] ?? 0;
            }

            return response()->json([
                'success' => true,
                'shipping_fee' => $finalFee,
                'original_fee' => $originalFee,
                'shipping_discount' => $shippingDiscount,
                'is_free_shipping' => $finalFee == 0,
                'service_id' => $shippingResult['service_id'],
                'estimated_delivery' => $this->getEstimatedDelivery($request->shipping_method),
                'test_mode' => $this->ghnService->isTestMode()
            ]);

        } catch (\Exception $e) {
            Log::error('Calculate shipping error: ' . $e->getMessage());
            $baseFee = $request->shipping_method === 'express' ? 50000 : 30000;

            return response()->json([
                'success' => true,
                'shipping_fee' => $baseFee,
                'original_fee' => $baseFee,
                'shipping_discount' => 0,
                'is_free_shipping' => false,
                'service_id' => $request->shipping_method === 'express' ? 1 : 2,
                'estimated_delivery' => $this->getEstimatedDelivery($request->shipping_method),
                'test_mode' => true,
                'message' => 'Sử dụng phí cố định do có lỗi xảy ra'
            ]);
        }
    }

    public function checkout()
    {
        Log::info('=== CHECKOUT DEBUG ===', [
            'customer_guard_check' => Auth::guard('customer')->check(),
            'customer_guard_user' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->id : null,
            'web_guard_check' => Auth::guard('web')->check(),
            'default_auth_check' => Auth::check(),
            'session_customer_info' => session('customer_info'),
        ]);
        $customer = $this->getCurrentCustomer();

        $cartItems = Cart::with(['product', 'variant'])
            ->where('customer_id', $customer->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $shippingFee = 30000;
        $appliedDiscount = session('applied_discount');
        $discount = 0;
        $shippingDiscount = 0;

        if ($appliedDiscount) {
            $discount = $appliedDiscount['discount_amount'] ?? 0;
            $shippingDiscount = $appliedDiscount['shipping_discount'] ?? 0;
        }

        $finalShippingFee = max(0, $shippingFee - $shippingDiscount);
        $total = $subtotal + $finalShippingFee - $discount;
        $testMode = $this->ghnService->isTestMode();

        return view('user.checkout.checkout', compact(
            'cartItems',
            'subtotal',
            'shippingFee',
            'finalShippingFee',
            'discount',
            'total',
            'appliedDiscount',
            'testMode'
        ));
    }

    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|in:standard,express',
            'payment_method' => 'required|in:cod,vnpay,bank_transfer',
            'ghn_province_id' => 'required|integer',
            'ghn_district_id' => 'required|integer',
            'ghn_ward_code' => 'required|string',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'note' => 'nullable|string|max:500'
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'ghn_province_id.required' => 'Vui lòng chọn tỉnh/thành phố',
            'ghn_district_id.required' => 'Vui lòng chọn quận/huyện',
            'ghn_ward_code.required' => 'Vui lòng chọn phường/xã',
            'shipping_method.required' => 'Vui lòng chọn phương thức vận chuyển',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin đặt hàng!');
        }

        DB::beginTransaction();

        try {
            $customer = $this->getCurrentCustomer();

            $cartItems = Cart::with(['product', 'variant'])
                ->where('customer_id', $customer->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $shippingFee = $this->calculateRealShippingFee($request, $cartItems);

            $appliedDiscount = session('applied_discount');
            $discount = 0;
            $shippingDiscount = 0;

            if ($appliedDiscount) {
                $discount = $appliedDiscount['discount_amount'] ?? 0;
                $shippingDiscount = $appliedDiscount['shipping_discount'] ?? 0;
            }

            $finalShippingFee = max(0, $shippingFee - $shippingDiscount);
            $totalAmount = $subtotal + $finalShippingFee - $discount;

            $orderMetadata = [
                '__customer_note__' => $request->note ?? null,
                '__version__' => '1.0',
                'shipping_info' => [
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'full_address' => $this->buildFullAddress($request),
                    'shipping_method' => $request->shipping_method,
                    'shipping_fee' => $finalShippingFee,
                    'original_shipping_fee' => $shippingFee,
                    'shipping_discount' => $shippingDiscount
                ],
                'ghn_data' => [
                    'province_id' => $request->ghn_province_id,
                    'district_id' => $request->ghn_district_id,
                    'ward_code' => $request->ghn_ward_code,
                    'province_name' => $request->province,
                    'district_name' => $request->district,
                    'ward_name' => $request->ward,
                    'test_mode' => $this->ghnService->isTestMode(),
                    'is_ghn_created' => false,
                    'tracking_number' => null
                ],
                'coupon_info' => $appliedDiscount,
                'created_at' => now()->toISOString()
            ];

            $trackingNumber = 'ORD' . date('Ymd') . strtoupper(Str::random(6));

            $order = Order::create([
                'tracking_number' => $trackingNumber,
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'note' => json_encode($orderMetadata)
            ]);

            foreach ($cartItems as $cartItem) {
                $availableStock = $cartItem->variant
                    ? $cartItem->variant->stock_quantity
                    : $cartItem->product->stock;

                if ($availableStock < $cartItem->quantity) {
                    throw new \Exception("Sản phẩm {$cartItem->product->name} không đủ tồn kho");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price
                ]);
            }

            $this->updateInventoryForOrder($order->orderItems()->with('product')->get(), 'decrease');

            if (config('services.ghn.auto_create_order', false)) {
                $ghnResult = $this->createGHNOrder($order, $request, $cartItems, $orderMetadata);

                if ($ghnResult['success']) {
                    $order->update(['tracking_number' => $ghnResult['tracking_number']]);
                    $orderMetadata['ghn_data']['is_ghn_created'] = true;
                    $orderMetadata['ghn_data']['tracking_number'] = $ghnResult['tracking_number'];
                    $orderMetadata['ghn_data']['ghn_order_code'] = $ghnResult['order_code'];
                    $orderMetadata['ghn_data']['is_real'] = $ghnResult['is_real'];
                    $orderMetadata['ghn_data']['expected_delivery'] = $ghnResult['expected_delivery_time'];
                    $order->update(['note' => json_encode($orderMetadata)]);
                }
            }

            if (class_exists('App\Models\Shipping')) {
                Shipping::create([
                    'order_id' => $order->id,
                    'shipping_address' => $this->buildFullAddress($request),
                    'shipping_method' => $request->shipping_method,
                    'shipping_status' => 'pending',
                    'shipping_fee' => $finalShippingFee,
                    'tracking_code' => $order->tracking_number,
                    'ghn_province_id' => $request->ghn_province_id,
                    'ghn_district_id' => $request->ghn_district_id,
                    'ghn_ward_code' => $request->ghn_ward_code,
                    'province_name' => $request->province,
                    'district_name' => $request->district,
                    'ward_name' => $request->ward
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_note' => $appliedDiscount ? "Mã giảm giá: {$appliedDiscount['code']}" : null
            ]);

            Cart::where('customer_id', $customer->id)->delete();
            Session::forget('applied_discount');

            DB::commit();

            return $this->handlePaymentMethod($order, $request->payment_method);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Place Order Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function buildFullAddress($request)
    {
        $parts = array_filter([
            $request->shipping_address,
            $request->ward,
            $request->district,
            $request->province
        ]);

        return implode(', ', $parts);
    }

    private function calculateRealShippingFee($request, $cartItems)
    {
        try {
            $totalWeight = $cartItems->sum(function($item) {
                return ($item->product->weight ?? 100) * $item->quantity;
            });

            $orderValue = $cartItems->sum(function($item) {
                return $item->price * $item->quantity;
            });

            $ghnData = [
                'to_province_id' => $request->ghn_province_id,
                'to_district_id' => $request->ghn_district_id,
                'to_ward_code' => $request->ghn_ward_code,
                'weight' => max($totalWeight, 500),
                'length' => 30,
                'width' => 20,
                'height' => 15,
                'insurance_value' => min($orderValue, 5000000),
                'service_type_id' => $request->shipping_method === 'express' ? 1 : 2
            ];

            $result = $this->ghnService->calculateShippingFee($ghnData);

            if (!$result['success']) {
                return $request->shipping_method === 'express' ? 50000 : 30000;
            }

            return $result['fee'];

        } catch (\Exception $e) {
            Log::error('Calculate real shipping fee error: ' . $e->getMessage());
            return $request->shipping_method === 'express' ? 50000 : 30000;
        }
    }

    private function createGHNOrder($order, $request, $cartItems, &$metadata)
    {
        try {
            $items = $cartItems->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'weight' => ($item->product->weight ?? 100) * $item->quantity,
                ];
            })->toArray();

            $ghnOrderData = [
                'payment_type_id' => $request->payment_method === 'cod' ? 2 : 1,
                'note' => $request->note ?? '',
                'required_note' => 'KHONGCHOXEMHANG',
                'client_order_code' => $order->tracking_number,
                'to_name' => $request->customer_name,
                'to_phone' => $request->customer_phone,
                'to_address' => $request->shipping_address,
                'to_ward_code' => $request->ghn_ward_code,
                'to_district_id' => $request->ghn_district_id,
                'cod_amount' => $request->payment_method === 'cod' ? $order->total_amount : 0,
                'content' => 'Sản phẩm mỹ phẩm',
                'weight' => max($cartItems->sum(function($item) {
                    return ($item->product->weight ?? 100) * $item->quantity;
                }), 500),
                'length' => 30,
                'width' => 20,
                'height' => 15,
                'insurance_value' => min($order->total_amount, 5000000),
                'service_type_id' => $request->shipping_method === 'express' ? 1 : 2,
                'items' => $items
            ];

            return $this->ghnService->createOrder($ghnOrderData);

        } catch (\Exception $e) {
            Log::error('Create GHN Order Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi tạo đơn GHN: ' . $e->getMessage()
            ];
        }
    }

    private function getEstimatedDelivery($shippingMethod)
    {
        $days = $shippingMethod === 'express' ? 1 : 3;
        return now()->addDays($days)->format('d/m/Y');
    }

    private function handlePaymentMethod($order, $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'cod':
                return redirect()->route('order.success', $order->id)
                    ->with('success', 'Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.');
            case 'bank_transfer':
                return redirect()->route('order.bank-transfer', $order->id);
            case 'vnpay':
                return redirect()->route('order.vnpay', $order->id);
            default:
                return redirect()->route('order.success', $order->id);
        }
    }

    public function vnpay($orderId)
    {
        try {
            $customer = $this->getCurrentCustomer();

            $order = Order::with(['payment'])
                ->where('customer_id', $customer->id)
                ->findOrFail($orderId);

            if ($order->payment->payment_method !== 'vnpay') {
                return redirect()->route('order.success', $orderId)
                    ->with('info', 'Đơn hàng này không sử dụng phương thức thanh toán VNPay.');
            }

            if ($order->payment->payment_status === 'completed') {
                return redirect()->route('order.success', $orderId)
                    ->with('info', 'Đơn hàng này đã được thanh toán.');
            }

            if ($order->status === 'cancelled') {
                return redirect()->route('order.checkout')
                    ->with('error', 'Đơn hàng này đã bị hủy. Vui lòng đặt hàng mới.');
            }

            if (!$this->vnpayService->isConfigured()) {
                Log::error('VNPay not configured properly', $this->vnpayService->getConfigInfo());
                return redirect()->route('order.checkout')
                    ->with('error', 'Hệ thống thanh toán VNPay chưa được cấu hình. Vui lòng chọn phương thức thanh toán khác.');
            }

            $userIP = request()->ip();

            if (in_array($userIP, ['127.0.0.1', '::1', '0.0.0.0', 'localhost'])) {
                $userIP = '123.16.64.1';
            }

            $paymentResult = $this->vnpayService->createPaymentUrl($order, $userIP);

            if ($paymentResult['success']) {
                Log::info('✅ VNPay payment initiated successfully', [
                    'order_id' => $order->id,
                    'tracking_number' => $order->tracking_number,
                    'amount' => $order->total_amount,
                    'customer_id' => $customer->id,
                    'ip_address' => $userIP,
                    'payment_url_created' => true
                ]);

                $order->payment->update([
                    'payment_note' => 'VNPay: Đã tạo URL thanh toán - ' . now()->format('d/m/Y H:i')
                ]);

                return redirect($paymentResult['payment_url']);
            } else {
                Log::error('❌ VNPay payment creation failed', [
                    'order_id' => $order->id,
                    'error' => $paymentResult['message'],
                    'config_check' => $this->vnpayService->getConfigInfo()
                ]);

                return redirect()->route('order.checkout')
                    ->with('error', 'Lỗi tạo thanh toán VNPay: ' . $paymentResult['message']);
            }

        } catch (\Exception $e) {
            Log::error('❌ VNPay payment error: ' . $e->getMessage(), [
                'order_id' => $orderId ?? null,
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->route('order.checkout')
                ->with('error', 'Có lỗi xảy ra khi tạo thanh toán VNPay. Vui lòng thử lại.');
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            Log::info('=== VNPAY RETURN DEBUG START ===', [
                'request_data' => $request->all(),
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'has_session' => $request->hasSession(),
                'session_id' => $request->session()->getId()
            ]);

            $result = $this->vnpayService->processReturnData($request->all());

            Log::info('VNPay Service Result:', [
                'result' => $result,
                'success' => $result['success'] ?? false,
                'order_tracking' => $result['order_tracking'] ?? 'N/A'
            ]);

            $order = null;
            if (isset($result['order_tracking'])) {
                $order = Order::where('tracking_number', $result['order_tracking'])
                    ->with(['payment', 'customer'])
                    ->first();

                Log::info('Order Search Result:', [
                    'tracking_number' => $result['order_tracking'],
                    'order_found' => $order ? true : false,
                    'order_id' => $order ? $order->id : null,
                    'order_customer_id' => $order ? $order->customer_id : null,
                    'order_status' => $order ? $order->status : null,
                    'payment_status' => $order && $order->payment ? $order->payment->payment_status : null
                ]);
            }

            if (!$order) {
                Log::error('VNPay Return: Order not found', [
                    'tracking' => $result['order_tracking'] ?? 'N/A',
                    'all_orders_count' => Order::count(),
                    'recent_orders' => Order::latest()->limit(5)->pluck('tracking_number')->toArray()
                ]);

                return redirect()->route('products.index')
                    ->with('error', '❌ Không tìm thấy đơn hàng. Vui lòng liên hệ hỗ trợ: 1900 123 456')
                    ->with('vnpay_error', true);
            }

            $currentCustomer = null;
            $isAuthenticatedCustomer = false;

            try {
                $currentCustomer = $this->getCurrentCustomer();

                if ($order->customer_id === $currentCustomer->id) {
                    $isAuthenticatedCustomer = true;
                    Log::info('Authenticated customer matches order owner');
                } else {
                    Log::warning('Authenticated customer does not match order owner', [
                        'order_customer_id' => $order->customer_id,
                        'current_customer_id' => $currentCustomer->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::info('No authenticated customer (this is OK for VNPay return)', [
                    'error' => $e->getMessage()
                ]);
            }

            if ($order->payment) {
                $order->payment->saveVNPayResponse($result['data'] ?? $request->all());
                Log::info('Payment response saved to database');
            }

            if ($result['success']) {
                Log::info('🎉 VNPay payment successful for order: ' . $order->id);

                if ($order->status === 'pending') {
                    $order->update(['status' => 'processing']);
                    Log::info('Order status updated to processing after VNPay success', ['order_id' => $order->id]);
                }

                if ($order->payment && $order->payment->payment_status === 'pending') {
                    $order->payment->update(['payment_status' => 'completed']);
                    Log::info('Payment status updated to completed', ['order_id' => $order->id]);
                }

                if ($isAuthenticatedCustomer) {
                    $redirectUrl = route('order.success', $order->id);
                    Log::info('Redirecting authenticated customer to success page:', [
                        'redirect_url' => $redirectUrl,
                        'order_id' => $order->id
                    ]);

                    return redirect($redirectUrl)
                        ->with('success', '🎉 Thanh toán VNPay thành công! Đơn hàng của bạn đã được xác nhận và sẽ được xử lý ngay.')
                        ->with('payment_confirmed', '1')
                        ->with('vnpay_success', true);
                } else {
                    Log::info('Redirecting unauthenticated user to products with success message');

                    $successMessage = "🎉 <strong>Thanh toán VNPay thành công!</strong><br>" .
                        "✅ Đơn hàng #{$order->id} đã được xác nhận<br>" .
                        "💰 Số tiền: " . number_format($order->total_amount, 0, ',', '.') . "₫<br>" .
                        "📦 Đơn hàng sẽ được xử lý và giao đến bạn sớm nhất";

                    $infoMessage = "💡 <strong>Để theo dõi đơn hàng:</strong><br>" .
                        "• <a href='" . route('login') . "' class='text-decoration-none fw-bold'>Đăng nhập</a> bằng tài khoản đã đặt hàng<br>" .
                        "• Hoặc liên hệ hotline <strong>1900 123 456</strong> với mã đơn hàng #{$order->id}";

                    return redirect()->route('products.index')
                        ->with('success', $successMessage)
                        ->with('info', $infoMessage)
                        ->with('vnpay_success', true)
                        ->with('order_info', [
                            'id' => $order->id,
                            'tracking_number' => $order->tracking_number,
                            'amount' => $order->total_amount
                        ]);
                }

            } else {
                $errorMessage = $result['message'] ?? 'Thanh toán không thành công';
                $vnpResponseCode = $request->get('vnp_ResponseCode', '');

                Log::warning('VNPay payment failed', [
                    'order_id' => $order->id,
                    'response_code' => $vnpResponseCode,
                    'message' => $errorMessage,
                    'full_result' => $result
                ]);

                switch ($vnpResponseCode) {
                    case '24':
                        $userMessage = '❌ <strong>Bạn đã hủy giao dịch</strong><br>Đơn hàng vẫn được giữ, bạn có thể thử thanh toán lại.';
                        break;
                    case '11':
                        $userMessage = '⏰ <strong>Giao dịch đã hết thời gian chờ</strong><br>Vui lòng thử lại.';
                        break;
                    case '51':
                        $userMessage = '💳 <strong>Tài khoản không đủ số dư</strong><br>Vui lòng kiểm tra và thử lại.';
                        break;
                    case '12':
                        $userMessage = '🔒 <strong>Thẻ/Tài khoản đã bị khóa</strong><br>Vui lòng liên hệ ngân hàng.';
                        break;
                    case '75':
                        $userMessage = '🔧 <strong>Ngân hàng đang bảo trì</strong><br>Vui lòng thử lại sau hoặc chọn phương thức khác.';
                        break;
                    default:
                        $userMessage = '❌ <strong>Thanh toán không thành công</strong><br>' . $errorMessage;
                }

                if ($isAuthenticatedCustomer) {
                    $failRedirectUrl = route('orders.show', $order->id);
                    Log::info('Redirecting authenticated customer to order detail (failed payment):', [
                        'redirect_url' => $failRedirectUrl,
                        'order_id' => $order->id
                    ]);

                    $retryInfo = "💡 <strong>Bạn có thể:</strong><br>" .
                        "• Thử thanh toán VNPay lại<br>" .
                        "• Chuyển sang phương thức chuyển khoản<br>" .
                        "• Liên hệ hỗ trợ: <strong>1900 123 456</strong>";

                    return redirect($failRedirectUrl)
                        ->with('error', $userMessage)
                        ->with('info', $retryInfo)
                        ->with('vnpay_failed', true);
                } else {
                    Log::info('Redirecting unauthenticated user to products (failed payment)');

                    $retryInfo = "💡 <strong>Để thử thanh toán lại:</strong><br>" .
                        "• <a href='" . route('login') . "' class='text-decoration-none fw-bold'>Đăng nhập</a> và vào 'Đơn hàng của tôi'<br>" .
                        "• Hoặc liên hệ hỗ trợ: <strong>1900 123 456</strong> với mã đơn #{$order->id}";

                    return redirect()->route('products.index')
                        ->with('error', $userMessage)
                        ->with('info', $retryInfo)
                        ->with('vnpay_failed', true)
                        ->with('order_info', [
                            'id' => $order->id,
                            'tracking_number' => $order->tracking_number,
                            'amount' => $order->total_amount
                        ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('❌ VNPay Return Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('products.index')
                ->with('error', '❌ <strong>Có lỗi xảy ra khi xử lý thanh toán</strong><br>Vui lòng liên hệ hỗ trợ: <strong>1900 123 456</strong>')
                ->with('vnpay_error', true);
        }
    }

    public function vnpayIPN(Request $request)
    {
        try {
            Log::info('VNPay IPN received:', $request->all());

            $result = $this->vnpayService->processIPN($request->all());

            if (!$result['success']) {
                Log::error('VNPay IPN processing failed:', $result);
                return response()->json(['RspCode' => $result['response_code'], 'Message' => $result['message']]);
            }

            $order = Order::where('tracking_number', $result['order_tracking'])->first();

            if (!$order) {
                Log::error('VNPay IPN: Order not found - ' . $result['order_tracking']);
                return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
            }

            $order->payment->saveVNPayResponse($result['data']);

            Log::info('VNPay IPN: Payment updated for order ' . $order->id);

            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);

        } catch (\Exception $e) {
            Log::error('VNPay IPN Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString()
            ]);
            return response()->json(['RspCode' => '99', 'Message' => 'Unknown error']);
        }
    }

    public function getOrderMetadata($order)
    {
        try {
            return json_decode($order->note, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function trackGHNOrder($orderId)
    {
        $customer = $this->getCurrentCustomer();

        $order = Order::where('customer_id', $customer->id)
            ->findOrFail($orderId);

        if (!$order->tracking_number) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa có mã vận đơn'
            ]);
        }

        $trackingResult = $this->ghnService->trackOrder($order->tracking_number);

        return response()->json($trackingResult);
    }

    public function success($orderId)
    {
        $customer = $this->getCurrentCustomer();
        $order = Order::with(['orderItems.product', 'payment'])
            ->where('customer_id', $customer->id)
            ->findOrFail($orderId);

        return view('user.checkout.success', compact('order'));
    }

    public function bankTransfer($orderId)
    {
        $customer = $this->getCurrentCustomer();

        $order = Order::with(['orderItems.product', 'payment'])
            ->where('customer_id', $customer->id)
            ->findOrFail($orderId);

        if ($order->payment->payment_method !== 'bank_transfer') {
            return redirect()->route('order.success', $orderId);
        }

        $bankInfo = [
            'account_name' => 'PHAM THI NAM',
            'account_number' => '10487314555',
            'bank_name' => 'VietinBank',
            'branch' => 'CN CHUONG DUONG - HOI SO',
            'amount' => $order->total_amount,
            'transfer_note' => "DH{$order->tracking_number}"
        ];

        return view('user.checkout.bank-transfer', compact('order', 'bankInfo'));
    }

    public function index(Request $request)
    {
        Log::info('=== ORDER INDEX DEBUG ===', [
            'customer_guard_check' => Auth::guard('customer')->check(),
            'customer_guard_user' => Auth::guard('customer')->user(),
            'session_all' => session()->all(),
        ]);

        try {
            $customer = $this->getCurrentCustomer();

            Log::info('Customer found:', [
                'customer_id' => $customer->id,
                'customer_user_id' => $customer->user_id,
                'customer_name' => $customer->name
            ]);

            $status = $request->get('status');
            $search = $request->get('search');

            $query = Order::with(['orderItems.product', 'orderItems.variant', 'payment', 'shipping'])
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc');

            $totalOrders = Order::where('customer_id', $customer->id)->count();
            Log::info('Total orders for customer:', ['count' => $totalOrders]);

            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%");
                });
            }

            $orders = $query->paginate(10);

            Log::info('Orders query result:', [
                'orders_count' => $orders->count(),
                'current_page' => $orders->currentPage(),
                'total' => $orders->total()
            ]);

            $orderCounts = [
                'pending' => Order::where('customer_id', $customer->id)->where('status', 'pending')->count(),
                'processing' => Order::where('customer_id', $customer->id)->where('status', 'processing')->count(),
                'shipped' => Order::where('customer_id', $customer->id)->where('status', 'shipped')->count(),
                'completed' => Order::where('customer_id', $customer->id)->where('status', 'completed')->count(),
                'cancelled' => Order::where('customer_id', $customer->id)->where('status', 'cancelled')->count(),
            ];

            return view('user.orders.index', compact('orders', 'orderCounts', 'status', 'search'));

        } catch (\Exception $e) {
            Log::error('Order index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để xem đơn hàng. Lỗi: ' . $e->getMessage());
        }
    }
    public function show($orderId)
    {
        try {
            Log::info('=== ORDER SHOW DEBUG START ===', [
                'order_id' => $orderId,
                'customer_guard_check' => Auth::guard('customer')->check(),
                'customer_guard_user' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->toArray() : null,
                'web_guard_check' => Auth::guard('web')->check(),
                'web_guard_user' => Auth::guard('web')->user() ? Auth::guard('web')->user()->toArray() : null,
                'staff_guard_check' => Auth::guard('staff')->check(),
                'staff_guard_user' => Auth::guard('staff')->user() ? Auth::guard('staff')->user()->toArray() : null,
                'session_customer_info' => session('customer_info'),
                'session_all' => session()->all(),
            ]);

            $customer = null;
            $customerError = null;

            try {
                $customer = $this->getCurrentCustomer();
                Log::info('getCurrentCustomer SUCCESS', [
                    'customer' => $customer ? $customer->toArray() : null
                ]);
            } catch (\Exception $e) {
                $customerError = $e->getMessage();
                Log::error('getCurrentCustomer FAILED', [
                    'error' => $customerError,
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $orderExists = Order::where('id', $orderId)->exists();
            $orderData = Order::where('id', $orderId)->first();

            Log::info('ORDER EXISTENCE CHECK', [
                'order_exists' => $orderExists,
                'order_data' => $orderData ? $orderData->toArray() : null
            ]);

            if (!$customer) {
                Log::warning('No customer found, trying alternative approach');

                $user = null;
                if (Auth::guard('customer')->check()) {
                    $user = Auth::guard('customer')->user();
                    $customer = \App\Models\Customer::where('user_id', $user->id)->first();
                    Log::info('Found customer via customer guard', ['customer' => $customer ? $customer->toArray() : null]);
                } elseif (Auth::guard('web')->check()) {
                    $user = Auth::guard('web')->user();
                    $customer = \App\Models\Customer::where('user_id', $user->id)->first();
                    Log::info('Found customer via web guard', ['customer' => $customer ? $customer->toArray() : null]);
                }
            }

            if ($customer && $orderExists) {
                $hasPermission = $orderData->customer_id === $customer->id;
                Log::info('PERMISSION CHECK', [
                    'order_customer_id' => $orderData->customer_id,
                    'current_customer_id' => $customer->id,
                    'has_permission' => $hasPermission
                ]);

                if (!$hasPermission) {
                    Log::error('PERMISSION DENIED - Customer ID mismatch');
                    return redirect()->route('orders.index')
                        ->with('error', 'Đơn hàng này không thuộc về bạn. Customer ID: ' . $customer->id . ', Order Customer ID: ' . $orderData->customer_id);
                }
            }

            if (!$customer) {
                Log::error('FINAL ERROR: No customer found after all attempts', [
                    'customer_error' => $customerError,
                    'available_guards' => [
                        'customer' => Auth::guard('customer')->check(),
                        'web' => Auth::guard('web')->check(),
                        'staff' => Auth::guard('staff')->check()
                    ]
                ]);

                return redirect()->route('login')
                    ->with('error', 'Vui lòng đăng nhập lại. Debug: ' . ($customerError ?? 'No customer found'));
            }

            $order = Order::where('id', $orderId)
                ->where('customer_id', $customer->id)
                ->with([
                    'orderItems.product',
                    'orderItems.variant',
                    'payment',
                    'shipping',
                    'customer',
                ])
                ->first();

            if (!$order) {
                Log::error('ORDER NOT FOUND OR NO PERMISSION', [
                    'order_id' => $orderId,
                    'customer_id' => $customer->id,
                    'order_exists_for_customer' => Order::where('id', $orderId)->where('customer_id', $customer->id)->exists(),
                    'order_exists_global' => Order::where('id', $orderId)->exists()
                ]);

                return redirect()->route('orders.index')
                    ->with('error', 'Không tìm thấy đơn hàng #' . $orderId . ' hoặc bạn không có quyền truy cập.');
            }

            $metadata = $this->getOrderMetadata($order);

            Log::info('ORDER SHOW SUCCESS', [
                'order_id' => $order->id,
                'customer_id' => $customer->id
            ]);

            return view('user.orders.detail', compact('order', 'metadata'));

        } catch (\Exception $e) {
            Log::error('ORDER SHOW EXCEPTION', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('orders.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function cancel($orderId)
    {
        try {
            $customer = $this->getCurrentCustomer();
            $order = Order::where('customer_id', $customer->id)
                ->where('id', $orderId)
                ->with(['payment', 'shipping'])
                ->firstOrFail();

            if (!$order->canBeCancelled()) {
                return redirect()->back()->with('error', 'Không thể hủy đơn hàng này!');
            }

            DB::beginTransaction();
            try {
                if (method_exists($this, 'updateInventoryForOrder')) {
                    $this->updateInventoryForOrder($order->orderItems()->with('product')->get(), 'increase');
                }

                $order->update(['status' => 'cancelled']);

                if ($order->payment) {
                    $paymentStatus = 'failed';

                    if ($order->payment->payment_status === 'completed') {
                        $paymentStatus = 'refunded';
                    }

                    $order->payment->update([
                        'payment_status' => $paymentStatus,
                        'payment_note' => 'Đơn hàng bị hủy - ' . now()->format('d/m/Y H:i')
                    ]);
                }

                if ($order->shipping) {
                    $shippingStatus = 'returned';

                    if (in_array($order->shipping->shipping_status, ['pending', 'confirmed'])) {
                        $shippingStatus = 'pending';
                    }

                    $order->shipping->update([
                        'shipping_status' => $shippingStatus,
                        'shipping_note' => 'Đơn hàng bị hủy bởi khách hàng - ' . now()->format('d/m/Y H:i')
                    ]);
                }

                DB::commit();

                Log::info('Order cancelled successfully', [
                    'order_id' => $order->id,
                    'order_status' => 'cancelled',
                    'payment_status' => $order->payment ? $order->payment->payment_status : 'N/A',
                    'shipping_status' => $order->shipping ? $order->shipping->shipping_status : 'N/A'
                ]);

                return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');

            } catch (\Exception $e) {
                DB::rollback();

                Log::error('Cancel Order Error', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine()
                ]);

                return redirect()->back()->with('error', 'Lỗi khi hủy đơn hàng: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error('Cancel Order General Error', [
                'order_id' => $orderId ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Có lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function fixCancelledOrders()
    {
        try {
            $stuckOrders = Order::with(['payment', 'shipping'])
                ->where('status', 'cancelled')
                ->where(function($query) {
                    $query->whereHas('payment', function($q) {
                        $q->where('payment_status', 'pending');
                    })
                        ->orWhereHas('shipping', function($q) {
                            $q->whereNotIn('shipping_status', ['pending', 'returned']);
                        });
                })
                ->get();

            $fixedCount = 0;
            foreach ($stuckOrders as $order) {
                DB::beginTransaction();
                try {
                    if ($order->payment && $order->payment->payment_status === 'pending') {
                        $order->payment->update([
                            'payment_status' => 'failed',
                            'payment_note' => 'Auto-fix: Đơn hàng đã bị hủy - ' . now()->format('d/m/Y H:i')
                        ]);
                    }

                    if ($order->shipping && !in_array($order->shipping->shipping_status, ['pending', 'returned'])) {
                        $order->shipping->update([
                            'shipping_status' => 'returned',
                            'shipping_note' => 'Auto-fix: Đơn hàng đã bị hủy - ' . now()->format('d/m/Y H:i')
                        ]);
                    }

                    $fixedCount++;
                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to fix cancelled order ' . $order->id . ': ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Đã sửa {$fixedCount} đơn hàng bị stuck",
                'total_found' => $stuckOrders->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bankTransferInfo($orderId)
    {
        $customer = $this->getCurrentCustomer();

        $order = Order::where('customer_id', $customer->id)
            ->where('id', $orderId)
            ->with('payment')
            ->firstOrFail();

        if ($order->payment->payment_method !== 'bank_transfer') {
            return redirect()->route('orders.show', $orderId);
        }

        if ($order->payment->payment_status === 'completed') {
            return redirect()->route('order.success', $orderId)
                ->with('success', '🎉 Chuyển khoản đã được xác nhận! Đơn hàng của bạn đang được xử lý.');
        }

        $bankInfo = [
            'account_name' => 'PHAM THI NAM',
            'account_number' => '10487314555',
            'bank_name' => 'VietinBank',
            'branch' => 'CN CHUONG DUONG - HOI SO',
            'amount' => $order->total_amount,
            'transfer_note' => "DH{$order->tracking_number}"
        ];

        return view('user.orders.bank-transfer', compact('order', 'bankInfo'));
    }

    public function checkPaymentStatus($orderId)
    {
        try {
            $customer = $this->getCurrentCustomer();

            $order = Order::where('customer_id', $customer->id)
                ->where('id', $orderId)
                ->with('payment')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment->payment_status,
                'order_status' => $order->status,
                'payment_method' => $order->payment->payment_method,
                'is_paid' => $order->payment->payment_status === 'completed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái'
            ], 500);
        }
    }

    public function resendVNPay($orderId)
    {
        try {
            $customer = $this->getCurrentCustomer();

            $order = Order::where('customer_id', $customer->id)
                ->where('id', $orderId)
                ->with('payment')
                ->firstOrFail();

            if ($order->payment->payment_method !== 'vnpay' || $order->payment->payment_status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tạo lại link thanh toán cho đơn hàng này'
                ]);
            }

            return response()->json([
                'success' => true,
                'redirect_url' => route('order.vnpay', $orderId)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function recheckPayment($orderId)
    {
        try {
            $customer = $this->getCurrentCustomer();

            $order = Order::where('customer_id', $customer->id)
                ->where('id', $orderId)
                ->with('payment')
                ->firstOrFail();

            $order->payment->refresh();

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment->payment_status,
                'order_status' => $order->status,
                'is_paid' => $order->payment->payment_status === 'completed',
                'message' => $order->payment->payment_status === 'completed'
                    ? 'Thanh toán đã được xác nhận!'
                    : 'Thanh toán chưa được xác nhận'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}
