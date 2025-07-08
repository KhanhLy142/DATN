<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('customer.auth');
    }

    public function create(Request $request, Product $product)
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin khách hàng. Vui lòng liên hệ admin.');
        }

        $hasPurchased = \App\Models\Order::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->whereHas('orderItems', function($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasPurchased) {
            return redirect()->route('products.show', $product->id)
                ->with('error', 'Bạn cần mua và nhận sản phẩm này trước khi có thể đánh giá.');
        }

        $existingReview = Review::where('product_id', $product->id)
            ->where('customer_id', $customer->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('products.show', $product->id)
                ->with('error', 'Bạn đã đánh giá sản phẩm này rồi!');
        }

        $order = \App\Models\Order::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->whereHas('orderItems', function($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->first();

        return view('user.reviews.create', compact('product', 'order', 'hasPurchased'));
    }

    public function store(Request $request, Product $product)
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin khách hàng. Vui lòng liên hệ admin.');
        }

        Review::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 1
        ]);

        return redirect()->route('products.show', $product->id)
            ->with('success', 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn sẽ giúp khách hàng khác có thêm thông tin hữu ích.');
    }

    public function index()
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $reviews = Review::where('customer_id', $customer->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer || $review->customer_id !== $customer->id) {
            abort(403, 'Bạn không có quyền xem đánh giá này.');
        }

        $review->load('product');
        return view('user.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer || $review->customer_id !== $customer->id) {
            abort(403, 'Bạn không có quyền sửa đánh giá này.');
        }

        $product = $review->product;
        return view('user.reviews.edit', compact('review', 'product'));
    }

    public function update(Request $request, Review $review)
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer || $review->customer_id !== $customer->id) {
            abort(403, 'Bạn không có quyền sửa đánh giá này.');
        }

        return redirect()->route('products.show', $review->product->id)
            ->with('success', 'Đánh giá đã được cập nhật thành công!');
    }

    public function destroy(Review $review)
    {
        $userId = Auth::guard('customer')->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer || $review->customer_id !== $customer->id) {
            abort(403, 'Bạn không có quyền xóa đánh giá này.');
        }

        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Đánh giá đã được xóa thành công!');
    }
}
