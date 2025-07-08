<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->orderBy('parent_id')->orderBy('name')->paginate(10);
        $parentCategories = Category::whereNull('parent_id')->where('status', 1)->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->where('status', 1)->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $validated['status'] = $request->has('status') ? 1 : 0;

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->where('id', '!=', $id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::findOrFail($id);

        if ($validated['parent_id'] == $id) {
            return back()->withErrors(['parent_id' => 'Không thể chọn chính danh mục này làm danh mục cha']);
        }

        $validated['status'] = $request->has('status') ? 1 : 0;

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function show($id)
    {
        $category = Category::with('parent', 'children')->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục có danh mục con!');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}
