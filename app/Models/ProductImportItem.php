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
        'variant_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2'
    ];

    public function import()
    {
        return $this->belongsTo(ProductImport::class, 'import_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', '.') . ' ₫';
    }

    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', '.') . ' ₫';
    }
}
