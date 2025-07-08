<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;

    protected $table = 'product_discounts';

    protected $fillable = [
        'product_id',
        'discount_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function scopeActiveDiscounts($query)
    {
        return $query->whereHas('discount', function($discountQuery) {
            $discountQuery->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        });
    }

    public function isValid()
    {
        return $this->discount && $this->discount->isValid();
    }

    public function calculateFinalPrice($originalPrice)
    {
        if (!$this->isValid()) {
            return $originalPrice;
        }

        return $this->discount->getFinalPrice($originalPrice);
    }

    public function getDiscountPercentage($originalPrice)
    {
        if (!$this->isValid()) {
            return 0;
        }

        return $this->discount->getDiscountPercentage($originalPrice);
    }
}
