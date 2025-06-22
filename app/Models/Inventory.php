<?php

// app/Models/Inventory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity'
    ];

    // Relationship với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scope để lấy sản phẩm sắp hết hàng
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('quantity', '<=', $threshold)->where('quantity', '>', 0);
    }

    // Scope để lấy sản phẩm hết hàng
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }
}

