<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Hiển thị danh sách đánh giá
     */
    public function index(Request $request)
    {
        $query = Review::with(['product', 'customer']);

        // Lọc theo sản phẩm
        if ($request->filled('product_id')) {
            $query->byProduct($request->product_id);
        }

        // Lọc theo rating
        if ($request->filled('rating')) {
            $query->byRating($request->rating);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Số items mỗi trang (mặc định 10)
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $reviews = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $products = Product::all(); // Để hiển thị trong dropdown filter

        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Hiển thị form phản hồi đánh giá
     */
    public function reply($id)
    {
        $review = Review::with(['product', 'customer'])->findOrFail($id);
        return view('admin.reviews.reply', compact('review'));
    }

    /**
     * Lưu phản hồi đánh giá
     */
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

    /**
     * Ẩn/hiện đánh giá
     */
    public function toggleStatus($id)
    {
        $review = Review::findOrFail($id);
        $review->status = !$review->status;
        $review->save();

        $message = $review->status ? 'Đánh giá đã được hiển thị' : 'Đánh giá đã được ẩn';

        return redirect()->route('admin.reviews.index')
            ->with('success', $message);
    }

    /**
     * Xóa đánh giá
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được xóa thành công!');
    }

    /**
     * API để lấy danh sách đánh giá cho frontend
     */
    public function apiIndex(Request $request)
    {
        $query = Review::with(['product', 'customer'])
            ->where('status', 1); // Chỉ lấy những đánh giá được hiển thị

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * API để lấy đánh giá theo sản phẩm
     */
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
