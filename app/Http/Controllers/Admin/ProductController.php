<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'variants']);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('price_min')) {
            $query->where('base_price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('base_price', '<=', $request->price_max);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(10);
        $brands = Brand::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();

        return view('admin.products.index', compact('products', 'brands', 'categories'));
    }

    public function create()
    {
        $brands = Brand::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();

        return view('admin.products.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:100|unique:products,sku',
            'base_price'  => 'required|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'brand_id'    => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if (empty($validated['sku'])) {
            $validated['sku'] = 'PRD-' . strtoupper(uniqid());
        }

        if ($request->hasFile('images')) {
            $validated['image'] = $this->uploadMultipleImages($request->file('images'));
        }

        $validated['status'] = $request->has('status') ? 1 : 0;

        if (!isset($validated['stock'])) {
            $validated['stock'] = 0;
        }

        try {
            $product = Product::create($validated);

            if ($request->has('variants')) {
                $totalVariantStock = 0;
                foreach ($request->variants as $variant) {
                    if (!empty($variant['variant_name']) && !empty($variant['price'])) {
                        $variantStock = $variant['stock_quantity'] ?? 0;
                        $totalVariantStock += $variantStock;

                        $product->variants()->create([
                            'variant_name'   => $variant['variant_name'],
                            'color'          => $variant['color'] ?? null,
                            'volume'         => $variant['volume'] ?? null,
                            'scent'          => $variant['scent'] ?? null,
                            'price'          => $variant['price'],
                            'stock_quantity' => $variantStock,
                            'status'         => 1,
                        ]);
                    }
                }

                if ($totalVariantStock > 0) {
                    $product->update(['stock' => $totalVariantStock]);
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Thêm sản phẩm "' . $product->name . '" thành công!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $brands = Brand::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:100|unique:products,sku,' . $id,
            'base_price'  => 'required|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'brand_id'    => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpg,jpeg,png,gif|max:2048',
            'variants'    => 'nullable|array',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.status' => 'nullable|boolean',
        ]);

        try {
            $product = Product::findOrFail($id);

            if (empty($validated['sku'])) {
                $validated['sku'] = 'PRD-' . strtoupper(uniqid());
            }

            if ($request->hasFile('images')) {
                $validated['image'] = $this->uploadMultipleImages($request->file('images'), $product->image);
            } else {
                unset($validated['image']);
            }

            $validated['status'] = $request->has('status') ? 1 : 0;

            if (!isset($validated['stock'])) {
                $validated['stock'] = 0;
            }

            $product->update($validated);

            if ($request->has('variants')) {
                $this->updateProductVariants($product, $request->variants);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm "' . $product->name . '" đã được cập nhật thành công!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function updateProductVariants($product, $variants)
    {
        $existingVariantIds = $product->variants()->pluck('id')->toArray();
        $updatedVariantIds = [];
        $totalVariantStock = 0;

        foreach ($variants as $variantData) {
            if (!empty($variantData['variant_name']) && !empty($variantData['price'])) {
                $variantStock = $variantData['stock_quantity'] ?? 0;
                $totalVariantStock += $variantStock;

                $variantAttributes = [
                    'variant_name'   => $variantData['variant_name'],
                    'color'          => $variantData['color'] ?? null,
                    'volume'         => $variantData['volume'] ?? null,
                    'scent'          => $variantData['scent'] ?? null,
                    'price'          => $variantData['price'],
                    'stock_quantity' => $variantStock,
                    'status'         => isset($variantData['status']) ? 1 : 0,
                ];

                if (isset($variantData['id']) && !empty($variantData['id'])) {
                    $variant = $product->variants()->find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantAttributes);
                        $updatedVariantIds[] = $variant->id;
                    }
                } else {
                    $newVariant = $product->variants()->create($variantAttributes);
                    $updatedVariantIds[] = $newVariant->id;
                }
            }
        }

        $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
        if (!empty($variantsToDelete)) {
            $product->variants()->whereIn('id', $variantsToDelete)->delete();
        }

        if ($totalVariantStock > 0) {
            $product->update(['stock' => $totalVariantStock]);
        }
    }

    public function show($id)
    {
        $product = Product::with('brand', 'category', 'variants')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name;

            if ($product->image) {
                $this->deleteProductImages($product->image);
            }

            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm "' . $productName . '" đã được xóa thành công!');

        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage());
        }
    }

    private function uploadMultipleImages($images, $oldImageString = null)
    {
        if ($oldImageString) {
            $this->deleteProductImages($oldImageString);
        }

        $imagePaths = [];

        if (!file_exists(public_path('uploads/products'))) {
            mkdir(public_path('uploads/products'), 0755, true);
        }

        foreach ($images as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $imagePaths[] = 'uploads/products/' . $imageName;
        }

        return implode(',', $imagePaths);
    }

    private function deleteProductImages($imageString)
    {
        if (!$imageString) return;

        $imagePaths = explode(',', $imageString);

        foreach ($imagePaths as $path) {
            $path = trim($path);
            if ($path && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }
    }
}
