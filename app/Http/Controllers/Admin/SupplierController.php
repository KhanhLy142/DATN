<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search') && !empty(trim($request->search))) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->orderBy('name')->paginate(15);

        $suppliers->appends($request->query());

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'boolean'
        ], [
            'name.required' => 'Tên nhà cung cấp là bắt buộc',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại',
            'email.email' => 'Email không đúng định dạng'
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Nhà cung cấp đã được tạo thành công!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('brands');
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'boolean'
        ], [
            'name.required' => 'Tên nhà cung cấp là bắt buộc',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại',
            'email.email' => 'Email không đúng định dạng'
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Nhà cung cấp đã được cập nhật thành công!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->brands()->count() > 0) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Không thể xóa nhà cung cấp vì còn có thương hiệu liên quan!');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Nhà cung cấp đã được xóa thành công!');
    }

    public function toggleStatus(Supplier $supplier): JsonResponse
    {
        $supplier->update(['status' => !$supplier->status]);

        return response()->json([
            'success' => true,
            'status' => $supplier->status,
            'message' => 'Trạng thái đã được cập nhật thành công!'
        ]);
    }
}
