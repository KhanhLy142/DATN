<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\ProductImportItem;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Inventory::count(),
            'in_stock' => Inventory::where('quantity', '>', 10)->count(),
            'low_stock' => Inventory::whereBetween('quantity', [1, 10])->count(),
            'out_stock' => Inventory::where('quantity', 0)->count(),
        ];


        $query = Inventory::with(['product' => function($query) {
            $query->with(['category', 'brand']);
        }]);


        if (request('search')) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%'.request('search').'%')
                    ->orWhere('sku', 'like', '%'.request('search').'%');
            });
        }

        if (request('status')) {
            switch(request('status')) {
                case 'in_stock':
                    $query->where('quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('quantity', [1, 10]);
                    break;
                case 'out_stock':
                    $query->where('quantity', 0);
                    break;
            }
        }

        $inventories = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('admin.inventory.index', compact('inventories', 'stats'));
    }

    public function getStats()
    {
        try {
            $stats = [
                'total' => Inventory::count(),
                'in_stock' => Inventory::where('quantity', '>', 10)->count(),
                'low_stock' => Inventory::whereBetween('quantity', [1, 10])->count(),
                'out_stock' => Inventory::where('quantity', 0)->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể lấy thống kê'], 500);
        }
    }

    public function syncInventory()
    {
        try {
            DB::beginTransaction();

            $products = Product::where('status', 1)->with('variants')->get();
            $syncedCount = 0;
            $createdCount = 0;
            $updatedCount = 0;

            foreach ($products as $product) {
                $totalStock = 0;

                if ($product->variants()->count() > 0) {
                    $totalStock = $product->variants()->sum('stock_quantity');
                } else {
                    $totalStock = $product->stock ?? 0;
                }

                $inventory = Inventory::where('product_id', $product->id)->first();

                if ($inventory) {
                    if ($inventory->quantity != $totalStock) {
                        $inventory->update(['quantity' => $totalStock]);
                        $updatedCount++;
                    }
                } else {
                    Inventory::create([
                        'product_id' => $product->id,
                        'quantity' => $totalStock
                    ]);
                    $createdCount++;
                }

                if ($product->stock != $totalStock) {
                    $product->update(['stock' => $totalStock]);
                }

                $syncedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "✅ Đồng bộ hoàn thành! Xử lý {$syncedCount} sản phẩm, tạo mới {$createdCount}, cập nhật {$updatedCount}",
                'stats' => [
                    'synced' => $syncedCount,
                    'created' => $createdCount,
                    'updated' => $updatedCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory sync error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();
        $products = Product::where('status', 1)
            ->with(['variants' => function($query) {
                $query->where('status', 1);
            }])
            ->get();

        if ($suppliers->isEmpty()) {
            return redirect()->route('admin.inventory.index')
                ->with('warning', 'Chưa có nhà cung cấp nào hoạt động. Vui lòng thêm nhà cung cấp trước khi nhập hàng.');
        }

        if ($products->isEmpty()) {
            return redirect()->route('admin.inventory.index')
                ->with('warning', 'Chưa có sản phẩm nào hoạt động. Vui lòng thêm sản phẩm trước khi nhập hàng.');
        }

        return view('admin.inventory.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp',
            'supplier_id.exists' => 'Nhà cung cấp không hợp lệ',
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm',
            'items.min' => 'Phải có ít nhất một sản phẩm để nhập',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm',
            'items.*.product_id.exists' => 'Sản phẩm không hợp lệ',
            'items.*.quantity.required' => 'Vui lòng nhập số lượng',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            'items.*.unit_price.required' => 'Vui lòng nhập đơn giá',
            'items.*.unit_price.min' => 'Đơn giá không được âm',
        ]);

        DB::beginTransaction();
        try {
            $totalCost = 0;
            $totalQuantity = 0;
            foreach ($request->items as $item) {
                $totalCost += $item['quantity'] * $item['unit_price'];
                $totalQuantity += $item['quantity'];
            }

            $import = ProductImport::create([
                'supplier_id' => $request->supplier_id,
                'total_cost' => $totalCost,
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                $importItemData = [
                    'import_id' => $import->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ];

                if (!empty($item['variant_id'])) {
                    $importItemData['variant_id'] = $item['variant_id'];
                }

                ProductImportItem::create($importItemData);

                $this->updateInventoryAndVariants($item);
            }

            DB::commit();

            $supplierName = Supplier::find($request->supplier_id)->name;
            $message = "✅ Nhập hàng thành công từ nhà cung cấp: {$supplierName}";
            $message .= " | Tổng {$totalQuantity} sản phẩm";
            $message .= " | Tổng chi phí: " . number_format($totalCost, 0, ',', '.') . " VNĐ";

            return redirect()->route('admin.inventory.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Inventory import error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }


    private function updateInventoryAndVariants($item)
    {
        $product = Product::find($item['product_id']);

        $inventory = Inventory::where('product_id', $item['product_id'])->first();
        if ($inventory) {
            $inventory->quantity += $item['quantity'];
            $inventory->save();
        } else {
            Inventory::create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            ]);
        }

        if (!empty($item['variant_id'])) {
            $variant = ProductVariant::find($item['variant_id']);
            if ($variant && $variant->product_id == $item['product_id']) {
                $variant->increment('stock_quantity', $item['quantity']);
            }
        } else {
            $variants = $product->variants()->where('status', 1)->get();

            if ($variants->count() > 0) {
                $quantityPerVariant = floor($item['quantity'] / $variants->count());
                $remainder = $item['quantity'] % $variants->count();

                foreach ($variants as $index => $variant) {
                    $addQuantity = $quantityPerVariant;
                    if ($index < $remainder) {
                        $addQuantity += 1;
                    }
                    $variant->increment('stock_quantity', $addQuantity);
                }
            }
        }

        $totalVariantStock = $product->variants()->sum('stock_quantity');
        $product->update(['stock' => $totalVariantStock > 0 ? $totalVariantStock : $item['quantity']]);
    }


    public function lowStock()
    {
        $lowStockProducts = Inventory::with(['product'])
            ->where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->get();

        if ($lowStockProducts->isEmpty()) {
            return redirect()->route('admin.inventory.index')
                ->with('info', 'Tuyệt vời! Hiện tại không có sản phẩm nào sắp hết hàng.');
        }

        return view('admin.inventory.low-stock', compact('lowStockProducts'));
    }


    public function importHistory()
    {
        $imports = ProductImport::with(['supplier', 'items.product', 'items.variant'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.history', compact('imports'));
    }

    public function importDetail($id)
    {
        try {
            $import = ProductImport::with(['supplier', 'items.product', 'items.variant'])
                ->findOrFail($id);

            return view('admin.inventory.detail', compact('import'));
        } catch (\Exception $e) {
            return redirect()->route('admin.inventory.history')
                ->with('error', 'Không tìm thấy phiếu nhập hàng.');
        }
    }

    public function getProductVariants($productId)
    {
        try {
            $variants = ProductVariant::where('product_id', $productId)
                ->where('status', 1)
                ->select('id', 'variant_name', 'color', 'volume', 'scent', 'price', 'stock_quantity')
                ->get();

            $formattedVariants = $variants->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'variant_name' => $variant->variant_name,
                    'color' => $variant->color,
                    'volume' => $variant->volume,
                    'scent' => $variant->scent,
                    'price' => $variant->price,
                    'stock_quantity' => $variant->stock_quantity,
                    'display_name' => $variant->variant_name ?:
                        collect([$variant->color, $variant->volume, $variant->scent])
                            ->filter()
                            ->implode(' - ')
                ];
            });

            return response()->json($formattedVariants);
        } catch (\Exception $e) {
            Log::error('Error loading variants for product ' . $productId . ': ' . $e->getMessage());
            return response()->json(['error' => 'Không thể tải biến thể sản phẩm'], 500);
        }
    }

    public function getProductsBySupplier($supplierId)
    {
        try {
            $supplier = Supplier::where('id', $supplierId)->where('status', 1)->first();
            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhà cung cấp không tồn tại hoặc không hoạt động',
                    'products' => []
                ], 404);
            }

            $brandIds = Brand::where('supplier_id', $supplierId)
                ->where('status', 1)
                ->pluck('id');

            if ($brandIds->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nhà cung cấp này chưa có thương hiệu nào',
                    'products' => [],
                    'total' => 0
                ]);
            }

            $products = Product::whereIn('brand_id', $brandIds)
                ->where('status', 1)
                ->with(['brand', 'category'])
                ->select('id', 'name', 'sku', 'brand_id', 'category_id', 'base_price', 'stock')
                ->orderBy('name')
                ->get();

            $formattedProducts = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'brand_name' => $product->brand->name ?? 'N/A',
                    'category_name' => $product->category->name ?? 'N/A',
                    'base_price' => $product->base_price,
                    'current_stock' => $product->stock ?? 0,
                    'display_name' => $product->name . ' - ' . $product->sku . ' (' . ($product->brand->name ?? 'N/A') . ')'
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $formattedProducts,
                'total' => $products->count(),
                'supplier_name' => $supplier->name
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading products for supplier ' . $supplierId . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách sản phẩm: ' . $e->getMessage(),
                'products' => []
            ], 500);
        }
    }

    public function getBrandsBySupplier($supplierId)
    {
        try {
            $supplier = Supplier::where('id', $supplierId)->where('status', 1)->first();
            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhà cung cấp không tồn tại hoặc không hoạt động',
                    'brands' => []
                ], 404);
            }

            $brands = Brand::where('supplier_id', $supplierId)
                ->where('status', 1)
                ->select('id', 'name', 'country', 'description')
                ->orderBy('name')
                ->get();

            $brandsData = $brands->map(function($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'country' => $brand->country,
                    'description' => $brand->description,
                    'display_name' => $brand->name . ($brand->country ? ' (' . $brand->country . ')' : '')
                ];
            });

            return response()->json([
                'success' => true,
                'brands' => $brandsData,
                'total' => $brands->count(),
                'supplier_name' => $supplier->name
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading brands for supplier ' . $supplierId . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách thương hiệu: ' . $e->getMessage(),
                'brands' => []
            ], 500);
        }
    }


    public function getSupplierInfo($supplierId)
    {
        try {
            $supplier = Supplier::with(['brands' => function($query) {
                $query->where('status', 1)->with(['products' => function($q) {
                    $q->where('status', 1)->select('id', 'name', 'sku', 'brand_id', 'base_price', 'stock');
                }]);
            }])->where('id', $supplierId)->where('status', 1)->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhà cung cấp không tồn tại hoặc không hoạt động'
                ], 404);
            }

            $totalProducts = 0;
            $totalBrands = $supplier->brands->count();

            foreach ($supplier->brands as $brand) {
                $totalProducts += $brand->products->count();
            }

            return response()->json([
                'success' => true,
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'email' => $supplier->email,
                    'phone' => $supplier->phone,
                    'address' => $supplier->address,
                    'total_brands' => $totalBrands,
                    'total_products' => $totalProducts
                ],
                'brands' => $supplier->brands->map(function($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'country' => $brand->country,
                        'products_count' => $brand->products->count()
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading supplier info ' . $supplierId . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin nhà cung cấp'
            ], 500);
        }
    }
}
