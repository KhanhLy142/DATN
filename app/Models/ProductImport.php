<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'total_cost',
        'import_code',
        'notes'
    ];

    protected $casts = [
        'total_cost' => 'decimal:2'
    ];

    // Tự động tạo mã đơn nhập khi tạo mới
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->import_code)) {
                $model->import_code = self::generateImportCode();
            }
        });
    }

    public static function generateImportCode()
    {
        $today = today();
        $count = self::whereDate('created_at', $today)->count() + 1;
        return 'IMP' . $today->format('Ymd') . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    // Relationship với Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship với ProductImportItem
    public function items()
    {
        return $this->hasMany(ProductImportItem::class, 'import_id');
    }

    // Accessor để format tổng tiền
    public function getFormattedTotalCostAttribute()
    {
        return number_format($this->total_cost, 0, ',', '.') . ' ₫';
    }

    // Accessor để lấy tổng số sản phẩm
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }
}
