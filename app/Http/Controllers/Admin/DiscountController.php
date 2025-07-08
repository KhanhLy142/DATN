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

    public function index(Request $request): View
    {
        $query = Discount::with('products');

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

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

        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        }

        if ($request->filled('applies_to')) {
            $query->where('applies_to', $request->applies_to);
        }

        $discounts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        return view('admin.discounts.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'applies_to' => 'required|in:order,product,shipping',
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'products' => 'array',
            'products.*' => 'exists:products,id'
        ]);

        if ($validated['discount_type'] === 'percent' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Giá trị giảm giá phần trăm không được vượt quá 100%'])
                ->withInput();
        }

        if (in_array($validated['applies_to'], ['order', 'shipping']) && !empty($validated['products'])) {
            return back()->withErrors(['products' => 'Mã giảm giá đơn hàng và miễn phí ship không được chọn sản phẩm cụ thể'])
                ->withInput();
        }

        if ($validated['applies_to'] === 'product' && empty($validated['products'])) {
            return back()->withErrors(['products' => 'Sale sản phẩm phải chọn ít nhất 1 sản phẩm'])
                ->withInput();
        }

        $discount = Discount::create([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_order_amount' => $validated['min_order_amount'],
            'applies_to' => $validated['applies_to'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active')
        ]);

        if ($validated['applies_to'] === 'product' && !empty($validated['products'])) {
            $discount->products()->attach($validated['products']);
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Tạo mã giảm giá thành công!');
    }

    public function show(Discount $discount): View
    {
        $discount->load('products');
        return view('admin.discounts.show', compact('discount'));
    }

    public function edit(Discount $discount): View
    {
        $products = Product::orderBy('name')->get();
        $discount->load('products');
        return view('admin.discounts.edit', compact('discount', 'products'));
    }

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
            'min_order_amount' => 'required|numeric|min:0',
            'applies_to' => 'required|in:order,product,shipping',
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'products' => 'array',
            'products.*' => 'exists:products,id'
        ]);

        if ($validated['discount_type'] === 'percent' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Giá trị giảm giá phần trăm không được vượt quá 100%'])
                ->withInput();
        }

        if (in_array($validated['applies_to'], ['order', 'shipping']) && !empty($validated['products'])) {
            return back()->withErrors(['products' => 'Mã giảm giá đơn hàng và miễn phí ship không được chọn sản phẩm cụ thể'])
                ->withInput();
        }

        if ($validated['applies_to'] === 'product' && empty($validated['products'])) {
            return back()->withErrors(['products' => 'Sale sản phẩm phải chọn ít nhất 1 sản phẩm'])
                ->withInput();
        }

        $discount->update([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_order_amount' => $validated['min_order_amount'],
            'applies_to' => $validated['applies_to'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active')
        ]);

        if ($validated['applies_to'] === 'product') {
            if (isset($validated['products'])) {
                $discount->products()->sync($validated['products']);
            } else {
                $discount->products()->detach();
            }
        } else {
            $discount->products()->detach();
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $discount->products()->detach();

        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Xóa mã giảm giá thành công!');
    }

    public function toggleStatus(Discount $discount): RedirectResponse
    {
        $discount->update([
            'is_active' => !$discount->is_active
        ]);

        $status = $discount->is_active ? 'kích hoạt' : 'vô hiệu hóa';

        return redirect()->back()
            ->with('success', "Đã {$status} mã giảm giá thành công!");
    }

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

        if ($request->product_id && $discount->applies_to === 'product') {
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
                'applies_to' => $discount->applies_to,
                'description' => $discount->description,
                'display_value' => $discount->display_value
            ]
        ]);
    }

    public function report(Request $request): View
    {
        $query = Discount::with('products');

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $discounts = $query->get();
        $stats = [
            'total' => $discounts->count(),
            'active' => $discounts->where('is_active', true)->count(),
            'expired' => $discounts->filter(function($discount) {
                return $discount->end_date < now();
            })->count(),
            'upcoming' => $discounts->filter(function($discount) {
                return $discount->start_date > now();
            })->count(),
            'coupons' => $discounts->where('applies_to', 'order')->count(),
            'product_sales' => $discounts->where('applies_to', 'product')->count(),
            'shipping_discounts' => $discounts->where('applies_to', 'shipping')->count()
        ];

        return view('admin.discounts.report', compact('discounts', 'stats'));
    }
}
