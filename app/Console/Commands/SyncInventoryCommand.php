<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class SyncInventoryCommand extends Command
{
    protected $signature = 'inventory:sync {--force : Force sync without confirmation}';
    protected $description = 'Đồng bộ dữ liệu tồn kho từ bảng products sang bảng inventories';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có chắc muốn đồng bộ dữ liệu tồn kho? Thao tác này sẽ cập nhật bảng inventories.')) {
                $this->info('Đã hủy thao tác.');
                return 0;
            }
        }

        $this->info('Bắt đầu đồng bộ dữ liệu tồn kho...');

        DB::beginTransaction();

        try {
            $products = Product::where('status', 1)->get();
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

            $this->info("✅ Đồng bộ hoàn thành!");
            $this->line("Tổng sản phẩm xử lý: {$syncedCount}");
            $this->line("Inventory mới tạo: {$createdCount}");
            $this->line("Inventory cập nhật: {$updatedCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Lỗi: ' . $e->getMessage());
            return 1;
        }
    }
}
