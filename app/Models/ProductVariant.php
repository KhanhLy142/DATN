<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'variant_name',
        'color',
        'volume',
        'scent',
        'price',
        'stock_quantity',
    ];

    // Quan hệ ngược về sản phẩm chính
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
