<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\ProductImportItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Hiển thị danh sách tồn kho
     */
    public function index()
    {
        $inventories = Inventory::with(['product' => function($query) {
            $query->with(['category', 'brand']);
        }])
            ->when(request('search'), function($query) {
                $query->whereHas('product', function($q) {
                    $q->where('name', 'like', '%'.request('search').'%')
                        ->orWhere('sku', 'like', '%'.request('search').'%');
                });
            })
            ->when(request('status'), function($query) {
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
            })
            ->paginate(20);

        return view('admin.inventory.index', compact('inventories'));
    }

    /**
     * Hiển thị form nhập hàng
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();
        $products = Product::where('status', 1)->get();

        // Kiểm tra dữ liệu cần thiết
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

    /**
     * Xử lý nhập hàng
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Tính tổng chi phí
            $totalCost = 0;
            $totalQuantity = 0;
            foreach ($request->items as $item) {
                $totalCost += $item['quantity'] * $item['unit_price'];
                $totalQuantity += $item['quantity'];
            }

            // Tạo phiếu nhập
            $import = ProductImport::create([
                'supplier_id' => $request->supplier_id,
                'total_cost' => $totalCost
            ]);

            // Thêm items và cập nhật inventory
            foreach ($request->items as $item) {
                // Tạo import item
                ProductImportItem::create([
                    'import_id' => $import->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);

                // Cập nhật inventory
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
            }

            DB::commit();

            // Thông báo thành công
            $supplierName = Supplier::find($request->supplier_id)->name;
            $message = "✅ Nhập hàng thành công từ nhà cung cấp: {$supplierName}";
            $message .= " | Tổng {$totalQuantity} sản phẩm";
            $message .= " | Tổng chi phí: " . number_format($totalCost, 0, ',', '.') . " VNĐ";

            return redirect()->route('admin.inventory.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị form điều chỉnh số lượng
     */
    public function edit($id)
    {
        try {
            $inventory = Inventory::with('product')->findOrFail($id);
            return view('admin.inventory.edit', compact('inventory'));
        } catch (\Exception $e) {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'Không tìm thấy sản phẩm cần điều chỉnh.');
        }
    }

    /**
     * Cập nhật số lượng tồn kho
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255'
        ]);

        try {
            $inventory = Inventory::findOrFail($id);
            $oldQuantity = $inventory->quantity;
            $newQuantity = $request->quantity;

            $inventory->update(['quantity' => $newQuantity]);

            // Thông báo chi tiết về thay đổi
            $productName = $inventory->product->name;
            $change = $newQuantity - $oldQuantity;
            $changeText = $change > 0 ? "+{$change}" : "{$change}";

            $message = "✅ Đã cập nhật tồn kho sản phẩm: {$productName}";
            $message .= " | Từ {$oldQuantity} → {$newQuantity} ({$changeText})";
            $message .= " | Lý do: {$request->reason}";

            return redirect()->route('admin.inventory.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Không thể cập nhật tồn kho. Vui lòng thử lại.')
                ->withInput();
        }
    }

    /**
     * Hiển thị sản phẩm sắp hết hàng
     */
    public function lowStock()
    {
        $lowStockProducts = Inventory::with(['product'])
            ->where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->get();

        if ($lowStockProducts->isEmpty()) {
            return redirect()->route('admin.inventory.index')
                ->with('info', '🎉 Tuyệt vời! Hiện tại không có sản phẩm nào sắp hết hàng.');
        }

        return view('admin.inventory.low-stock', compact('lowStockProducts'));
    }

    /**
     * Lịch sử nhập hàng
     */
    public function importHistory()
    {
        $imports = ProductImport::with(['supplier', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.history', compact('imports'));
    }

    /**
     * Chi tiết phiếu nhập hàng
     */
    public function importDetail($id)
    {
        try {
            $import = ProductImport::with(['supplier', 'items.product'])
                ->findOrFail($id);

            return view('admin.inventory.detail', compact('import'));
        } catch (\Exception $e) {
            return redirect()->route('admin.inventory.history')
                ->with('error', 'Không tìm thấy phiếu nhập hàng.');
        }
    }

    public function dashboard()
    {
        // Thống kê tổng quan
        $totalProducts = Inventory::count();
        $inStock = Inventory::where('quantity', '>', 10)->count();
        $lowStock = Inventory::whereBetween('quantity', [1, 10])->count();
        $outOfStock = Inventory::where('quantity', 0)->count();

        // Sản phẩm sắp hết hàng (top 5)
        $lowStockProducts = Inventory::with('product')
            ->whereBetween('quantity', [1, 10])
            ->orderBy('quantity', 'asc')
            ->take(5)
            ->get();

        // Nhập hàng gần đây (5 đơn)
        $recentImports = ProductImport::with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.inventory.dashboard', compact(
            'totalProducts', 'inStock', 'lowStock', 'outOfStock',
            'lowStockProducts', 'recentImports'
        ));
    }

    public function getProductsBySupplier($supplierId)
    {
        $products = Product::whereHas('brand', function($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })
            ->where('status', 1)
            ->select('id', 'name', 'sku', 'base_price')
            ->get();

        return response()->json($products);
    }
}
