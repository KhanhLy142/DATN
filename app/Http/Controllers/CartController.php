<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();

        $availableDiscounts = Discount::valid()
            ->whereIn('applies_to', ['order', 'shipping'])
            ->orderBy('applies_to', 'asc')
            ->orderBy('min_order_amount', 'asc')
            ->take(10)
            ->get();

        return view('user.checkout.cart', compact('cartItems', 'availableDiscounts'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $code = strtoupper(trim($request->code));
        $subtotal = (float)$request->subtotal;

        $discount = Discount::where('code', $code)
            ->whereIn('applies_to', ['order', 'shipping'])
            ->valid()
            ->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn!'
            ]);
        }

        if ($subtotal < $discount->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng tối thiểu ' . number_format($discount->min_order_amount, 0, ',', '.') . 'đ để áp dụng mã này!'
            ]);
        }

        $discountAmount = 0;
        $shippingDiscount = 0;

        if ($discount->applies_to === 'shipping') {
            $shippingDiscount = $discount->discount_value;
        } else {
            if ($discount->discount_type === 'fixed') {
                $discountAmount = $discount->discount_value;
            } elseif ($discount->discount_type === 'percent') {
                $discountAmount = $subtotal * ($discount->discount_value / 100);
                if (isset($discount->max_discount_amount) && $discount->max_discount_amount && $discountAmount > $discount->max_discount_amount) {
                    $discountAmount = $discount->max_discount_amount;
                }
            }
        }

        Session::put('applied_discount', [
            'code' => $discount->code,
            'discount_amount' => $discountAmount,
            'shipping_discount' => $shippingDiscount,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'applies_to' => $discount->applies_to,
            'description' => $discount->description,
            'min_order_threshold' => $discount->min_order_amount,
            'max_discount_amount' => $discount->max_discount_amount ?? null
        ]);

        return response()->json([
            'success' => true,
            'discount_amount' => $discountAmount,
            'shipping_discount' => $shippingDiscount,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'description' => $discount->description ?: $this->getDiscountDisplayValue($discount),
            'min_order_threshold' => $discount->min_order_amount
        ]);
    }

    public function removeCoupon()
    {
        Session::forget('applied_discount');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá!'
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = null;
        $quantity = $request->quantity ?? 1;

        if ($request->variant_id) {
            $variant = ProductVariant::findOrFail($request->variant_id);

            if ($variant->product_id != $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biến thể không thuộc về sản phẩm này!'
                ], 400);
            }

            if ($variant->stock_quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biến thể không đủ tồn kho!'
                ], 400);
            }
        } else {

            if ($product->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không đủ tồn kho!'
                ], 400);
            }
        }


        list($customerId, $sessionId) = $this->getCustomerAndSession();


        $existingCart = Cart::where('product_id', $product->id)
            ->where('variant_id', $request->variant_id)
            ->forCustomerOrSession($customerId, $sessionId)
            ->first();


        $originalPrice = $variant ? $variant->price : $product->base_price;
        $finalPrice = $this->calculateFinalPrice($product, $variant, $originalPrice);

        if ($existingCart) {

            $newQuantity = $existingCart->quantity + $quantity;

            $stockLimit = $variant ? $variant->stock_quantity : $product->stock;
            if ($stockLimit < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho!'
                ], 400);
            }

            $existingCart->update([
                'quantity' => $newQuantity,
                'price' => $finalPrice
            ]);
        } else {

            Cart::create([
                'customer_id' => $customerId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'quantity' => $quantity,
                'price' => $finalPrice
            ]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'cart_count' => $cartCount
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $cart = $this->findCartItem($request->cart_id);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
            ], 404);
        }

        $stockLimit = $cart->variant ? $cart->variant->stock_quantity : $cart->product->stock;

        if ($stockLimit < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng vượt quá tồn kho!'
            ], 400);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng!',
            'cart_summary' => $this->getCartSummary()
        ]);
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer'
        ]);

        $cart = $this->findCartItem($request->cart_id);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
            'cart_count' => $this->getCartCount(),
            'cart_summary' => $this->getCartSummary()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $cart = $this->findCartItem($id);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
            ], 404);
        }

        $stockLimit = $cart->variant ? $cart->variant->stock_quantity : $cart->product->stock;

        if ($stockLimit < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng vượt quá tồn kho!'
            ], 400);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng!',
            'cart_summary' => $this->getCartSummary()
        ]);
    }

    public function remove($id)
    {
        $cart = $this->findCartItem($id);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
            'cart_count' => $this->getCartCount(),
            'cart_summary' => $this->getCartSummary()
        ]);
    }

    public function clear()
    {
        list($customerId, $sessionId) = $this->getCustomerAndSession();

        Cart::forCustomerOrSession($customerId, $sessionId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng!'
        ]);
    }

    public function getCount()
    {
        try {
            $count = $this->getCartCount();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Lỗi khi lấy số lượng giỏ hàng'
            ]);
        }
    }

    public function mergeSessionToCustomer()
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return;
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            return;
        }

        $sessionId = Session::getId();

        $sessionCarts = Cart::forSession($sessionId)->get();

        foreach ($sessionCarts as $sessionCart) {

            $existingCart = Cart::forCustomer($customer->id)
                ->where('product_id', $sessionCart->product_id)
                ->where('variant_id', $sessionCart->variant_id)
                ->first();

            if ($existingCart) {

                $existingCart->update([
                    'quantity' => $existingCart->quantity + $sessionCart->quantity
                ]);
                $sessionCart->delete();
            } else {
                $sessionCart->update([
                    'customer_id' => $customer->id,
                    'session_id' => null
                ]);
            }
        }
    }

    private function getCustomerAndSession()
    {
        $user = Auth::guard('customer')->user();
        $customerId = null;
        $sessionId = Session::getId();

        if ($user) {
            $customer = Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $customerId = $customer->id;
                $sessionId = null;
            }
        }

        return [$customerId, $sessionId];
    }

    private function getCartItems()
    {
        list($customerId, $sessionId) = $this->getCustomerAndSession();

        return Cart::with(['product', 'variant'])
            ->forCustomerOrSession($customerId, $sessionId)
            ->get();
    }

    private function findCartItem($id)
    {
        list($customerId, $sessionId) = $this->getCustomerAndSession();

        return Cart::where('id', $id)
            ->forCustomerOrSession($customerId, $sessionId)
            ->first();
    }

    private function getCartCount()
    {
        list($customerId, $sessionId) = $this->getCustomerAndSession();

        return Cart::forCustomerOrSession($customerId, $sessionId)->sum('quantity');
    }

    private function getCartSummary()
    {
        $cartItems = $this->getCartItems();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $itemCount = $cartItems->sum('quantity');

        return [
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
            'formatted_subtotal' => number_format($subtotal) . '₫'
        ];
    }

    private function calculateFinalPrice($product, $variant = null, $originalPrice = null)
    {
        if (!$originalPrice) {
            $originalPrice = $variant ? $variant->price : $product->base_price;
        }

        $productSaleDiscount = Discount::valid()
            ->where('applies_to', 'product')
            ->whereHas('products', function($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->first();

        if ($productSaleDiscount) {
            $discountAmount = $this->calculateDiscountAmount($productSaleDiscount, $originalPrice);
            return max(0, $originalPrice - $discountAmount);
        }

        return $originalPrice;
    }

    private function calculateDiscountAmount($discount, $price)
    {
        if ($discount->discount_type === 'fixed') {
            return $discount->discount_value;
        } elseif ($discount->discount_type === 'percent') {
            $discountAmount = $price * ($discount->discount_value / 100);
            if ($discount->max_discount_amount && $discountAmount > $discount->max_discount_amount) {
                return $discount->max_discount_amount;
            }
            return $discountAmount;
        }
        return 0;
    }

    private function getDiscountDisplayValue($discount)
    {
        if ($discount->discount_type === 'fixed') {
            return 'Giảm ' . number_format($discount->discount_value, 0, ',', '.') . 'đ';
        } elseif ($discount->discount_type === 'percent') {
            $text = 'Giảm ' . $discount->discount_value . '%';
            if ($discount->max_discount_amount) {
                $text .= ' (tối đa ' . number_format($discount->max_discount_amount, 0, ',', '.') . 'đ)';
            }
            return $text;
        }
        return $discount->description ?: 'Mã giảm giá';
    }
}
