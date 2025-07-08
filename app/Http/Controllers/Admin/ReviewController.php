<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'customer']);

        if ($request->filled('product_id')) {
            $query->byProduct($request->product_id);
        }

        if ($request->filled('rating')) {
            $query->byRating($request->rating);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $reviews = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $products = Product::whereHas('reviews')->get();

        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    public function reply($id)
    {
        $review = Review::with(['product', 'customer'])->findOrFail($id);
        return view('admin.reviews.reply', compact('review'));
    }

    public function storeReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000'
        ]);

        $review = Review::findOrFail($id);
        $review->reply = $request->reply;
        $review->save();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Phản hồi đã được gửi thành công!');
    }

    public function toggleStatus($id)
    {
        $review = Review::findOrFail($id);
        $review->status = !$review->status;
        $review->save();

        $message = $review->status ? 'Đánh giá đã được hiển thị' : 'Đánh giá đã được ẩn';

        return redirect()->route('admin.reviews.index')
            ->with('success', $message);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được xóa thành công!');
    }

    public function apiIndex(Request $request)
    {
        $query = Review::with(['product', 'customer'])
            ->where('status', 1);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    public function getByProduct($productId)
    {
        $reviews = Review::with(['customer'])
            ->where('product_id', $productId)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }
}
