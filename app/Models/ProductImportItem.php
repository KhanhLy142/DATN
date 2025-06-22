<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2'
    ];

    // Relationship với ProductImport
    public function import()
    {
        return $this->belongsTo(ProductImport::class, 'import_id');
    }

    // Relationship với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor để tính tổng tiền cho item này
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    // Accessor để format đơn giá
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', '.') . ' ₫';
    }

    // Accessor để format tổng tiền
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', '.') . ' ₫';
    }
}
