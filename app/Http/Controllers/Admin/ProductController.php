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
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Generate SKU nếu không có
        if (empty($validated['sku'])) {
            $validated['sku'] = 'PRD-' . strtoupper(uniqid());
        }

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request);
        }

        $validated['status'] = $request->has('status') ? 1 : 0;

        // Đặt stock mặc định nếu không có
        if (!isset($validated['stock'])) {
            $validated['stock'] = 0;
        }

        try {
            $product = Product::create($validated);

            // Thêm các biến thể
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

                // Cập nhật tổng stock từ variants nếu có biến thể
                if ($totalVariantStock > 0) {
                    $product->update(['stock' => $totalVariantStock]);
                }
            }

            // ✅ FIX: Sử dụng route admin.products.index thay vì products.index
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
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'variants'    => 'nullable|array',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.status' => 'nullable|boolean',
        ]);

        try {
            $product = Product::findOrFail($id);

            // Generate SKU nếu không có
            if (empty($validated['sku'])) {
                $validated['sku'] = 'PRD-' . strtoupper(uniqid());
            }

            // Xử lý upload ảnh
            if ($request->hasFile('image')) {
                $validated['image'] = $this->uploadImage($request, $product->image);
            } else {
                unset($validated['image']); // Không update ảnh nếu không upload
            }

            $validated['status'] = $request->has('status') ? 1 : 0;

            // Đặt stock mặc định nếu không có
            if (!isset($validated['stock'])) {
                $validated['stock'] = 0;
            }

            // Update thông tin cơ bản của sản phẩm
            $product->update($validated);

            // Xử lý cập nhật biến thể
            if ($request->has('variants')) {
                $this->updateProductVariants($product, $request->variants);
            }

            // ✅ FIX: Sử dụng route admin.products.index thay vì products.index
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm "' . $product->name . '" đã được cập nhật thành công!');

        } catch (\Exception $e) {
            \Log::error('Product update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function updateProductVariants($product, $variants)
    {
        // Lấy danh sách ID biến thể hiện tại
        $existingVariantIds = $product->variants()->pluck('id')->toArray();
        $updatedVariantIds = [];
        $totalVariantStock = 0;

        foreach ($variants as $variantData) {
            // Chỉ xử lý nếu có tên biến thể và giá
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
                    // Cập nhật biến thể hiện có
                    $variant = $product->variants()->find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantAttributes);
                        $updatedVariantIds[] = $variant->id;
                    }
                } else {
                    // Tạo biến thể mới
                    $newVariant = $product->variants()->create($variantAttributes);
                    $updatedVariantIds[] = $newVariant->id;
                }
            }
        }

        // Xóa các biến thể không còn trong danh sách cập nhật
        $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
        if (!empty($variantsToDelete)) {
            $product->variants()->whereIn('id', $variantsToDelete)->delete();
        }

        // Cập nhật tổng stock từ variants
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

            // Xoá ảnh cũ nếu có
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // Xoá biến thể và sản phẩm (cascade sẽ tự động xoá)
            $product->delete();

            // ✅ FIX: Sử dụng route admin.products.index thay vì products.index
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm "' . $productName . '" đã được xóa thành công!');

        } catch (\Exception $e) {
            // ✅ FIX: Sử dụng route admin.products.index thay vì products.index
            return redirect()->route('admin.products.index')
                ->with('error', 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage());
        }
    }

    private function uploadImage(Request $request, $oldPath = null)
    {
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($oldPath && file_exists(public_path($oldPath))) {
                unlink(public_path($oldPath));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Tạo thư mục nếu chưa có
            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0755, true);
            }

            $image->move(public_path('uploads/products'), $imageName);

            return 'uploads/products/' . $imageName;
        }

        return $oldPath;
    }
}
