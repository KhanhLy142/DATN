<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
        'color',
        'volume',
        'scent',
        'price',
        'stock_quantity',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'status' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function getIsActiveAttribute()
    {
        return $this->status == 1;
    }

    public function getFullNameAttribute()
    {
        $parts = array_filter([
            $this->variant_name,
            $this->color,
            $this->volume,
            $this->scent
        ]);

        return implode(' - ', $parts);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . 'đ';
    }

    public function getEffectivePriceAttribute()
    {
        return $this->price > 0 ? $this->price : $this->product->base_price;
    }
}
