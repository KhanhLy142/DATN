<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    /**
     * Hiển thị danh sách mã giảm giá
     */
    public function index(Request $request): View
    {
        $query = Discount::with('products');

        // Tìm kiếm theo mã
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'valid':
                    $query->valid();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Lọc theo loại giảm giá
        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        }

        $discounts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Hiển thị form tạo mã giảm giá mới
     */
    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        return view('admin.discounts.create', compact('products'));
    }

    /**
     * Lưu mã giảm giá mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'products' => 'array',
            'products.*' => 'exists:products,id'
        ]);

        // Validation cho discount_value dựa trên loại
        if ($validated['discount_type'] === 'percent' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Giá trị giảm giá phần trăm không được vượt quá 100%'])
                ->withInput();
        }

        $discount = Discount::create([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active')
        ]);

        // Gắn sản phẩm nếu có
        if (!empty($validated['products'])) {
            $discount->products()->attach($validated['products']);
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Tạo mã giảm giá thành công!');
    }

    /**
     * Hiển thị chi tiết mã giảm giá
     */
    public function show(Discount $discount): View
    {
        $discount->load('products');
        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Hiển thị form chỉnh sửa mã giảm giá
     */
    public function edit(Discount $discount): View
    {
        $products = Product::orderBy('name')->get();
        $discount->load('products');
        return view('admin.discounts.edit', compact('discount', 'products'));
    }

    /**
     * Cập nhật mã giảm giá
     */
    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('discounts', 'code')->ignore($discount->id)
            ],
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'products' => 'array',
            'products.*' => 'exists:products,id'
        ]);

        // Validation cho discount_value dựa trên loại
        if ($validated['discount_type'] === 'percent' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Giá trị giảm giá phần trăm không được vượt quá 100%'])
                ->withInput();
        }

        $discount->update([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active')
        ]);

        // Cập nhật quan hệ với sản phẩm
        if (isset($validated['products'])) {
            $discount->products()->sync($validated['products']);
        } else {
            $discount->products()->detach();
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Xóa mã giảm giá
     */
    public function destroy(Discount $discount): RedirectResponse
    {
        // Xóa quan hệ với sản phẩm trước
        $discount->products()->detach();

        // Xóa mã giảm giá
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Xóa mã giảm giá thành công!');
    }

    /**
     * Bật/tắt trạng thái mã giảm giá
     */
    public function toggleStatus(Discount $discount): RedirectResponse
    {
        $discount->update([
            'is_active' => !$discount->is_active
        ]);

        $status = $discount->is_active ? 'kích hoạt' : 'vô hiệu hóa';

        return redirect()->back()
            ->with('success', "Đã {$status} mã giảm giá thành công!");
    }

    /**
     * Kiểm tra mã giảm giá (API cho frontend)
     */
    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'product_id' => 'nullable|exists:products,id'
        ]);

        $discount = Discount::where('code', strtoupper($request->code))
            ->valid()
            ->first();

        if (!$discount) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }

        // Kiểm tra xem mã có áp dụng cho sản phẩm này không
        if ($request->product_id && $discount->products()->count() > 0) {
            $productApplicable = $discount->products()->where('product_id', $request->product_id)->exists();

            if (!$productApplicable) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Mã giảm giá không áp dụng cho sản phẩm này'
                ]);
            }
        }

        return response()->json([
            'valid' => true,
            'discount' => [
                'id' => $discount->id,
                'code' => $discount->code,
                'type' => $discount->discount_type,
                'value' => $discount->discount_value,
                'display_value' => $discount->display_value
            ]
        ]);
    }

    /**
     * Xuất báo cáo mã giảm giá
     */
    public function report(Request $request): View
    {
        $query = Discount::with('products');

        // Lọc theo khoảng thời gian
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $discounts = $query->get();

        // Thống kê
        $stats = [
            'total' => $discounts->count(),
            'active' => $discounts->where('is_active', true)->count(),
            'expired' => $discounts->filter(function($discount) {
                return $discount->end_date < now();
            })->count(),
            'upcoming' => $discounts->filter(function($discount) {
                return $discount->start_date > now();
            })->count()
        ];

        return view('admin.discounts.report', compact('discounts', 'stats'));
    }
}
