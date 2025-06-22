<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::with('supplier');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('supplier_id') && $request->supplier_id !== '') {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $brands = $query->orderBy('name')->paginate(15);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.brands.index', compact('brands', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        return view('admin.brands.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'country' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'boolean'
        ], [
            'name.required' => 'Tên thương hiệu là bắt buộc',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp',
            'supplier_id.exists' => 'Nhà cung cấp không hợp lệ',
            'logo.image' => 'File phải là hình ảnh',
            'logo.max' => 'Kích thước file không được vượt quá 2MB'
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Thương hiệu đã được tạo thành công!');
    }

    public function show(Brand $brand)
    {
        $brand->load(['supplier', 'products']);
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        return view('admin.brands.edit', compact('brand', 'suppliers'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'country' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'boolean'
        ], [
            'name.required' => 'Tên thương hiệu là bắt buộc',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp',
            'supplier_id.exists' => 'Nhà cung cấp không hợp lệ',
            'logo.image' => 'File phải là hình ảnh',
            'logo.max' => 'Kích thước file không được vượt quá 2MB'
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Thương hiệu đã được cập nhật thành công!');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Không thể xóa thương hiệu vì còn có sản phẩm liên quan!');
        }

        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Thương hiệu đã được xóa thành công!');
    }

    public function toggleStatus(Brand $brand): JsonResponse
    {
        $brand->update(['status' => !$brand->status]);

        return response()->json([
            'success' => true,
            'status' => $brand->status,
            'message' => 'Trạng thái đã được cập nhật thành công!'
        ]);
    }

    public function getBySupplier(Request $request): JsonResponse
    {
        $supplierId = $request->get('supplier_id');

        $brands = Brand::where('supplier_id', $supplierId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($brands);
    }
}
