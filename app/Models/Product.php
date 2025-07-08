<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'brand_id',
        'category_id',
        'description',
        'base_price',
        'stock',
        'image',
        'status'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'status' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productDiscounts()
    {
        return $this->hasMany(ProductDiscount::class, 'product_id');
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'product_discounts', 'product_id', 'discount_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id');
    }

    public function getActiveDiscount()
    {
        $productDiscount = $this->productDiscounts()
            ->with('discount')
            ->whereHas('discount', function($query) {
                $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->first();

        return $productDiscount ? $productDiscount->discount : null;
    }

    public function getFinalPriceAttribute()
    {
        $activeDiscount = $this->getActiveDiscount();

        if ($activeDiscount) {
            return $activeDiscount->getFinalPrice($this->base_price);
        }

        return $this->base_price;
    }

    public function getPriceAttribute()
    {
        return $this->base_price;
    }

    public function getHasDiscountAttribute()
    {
        return $this->getActiveDiscount() !== null;
    }

    public function getDiscountPercentageAttribute()
    {
        $activeDiscount = $this->getActiveDiscount();

        if ($activeDiscount) {
            return $activeDiscount->getDiscountPercentage($this->base_price);
        }

        return 0;
    }

    public function getBestDiscountAttribute()
    {
        return $this->getActiveDiscount();
    }

    public function getIsActiveAttribute()
    {
        return $this->status == 1;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->base_price, 0, ',', '.') . 'đ';
    }

    public function getFormattedFinalPriceAttribute()
    {
        return number_format($this->final_price, 0, ',', '.') . 'đ';
    }

    public function getTotalStockAttribute()
    {
        $variantStock = $this->variants()->sum('stock_quantity');
        return $variantStock > 0 ? $variantStock : $this->stock;
    }

    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    public function getImagesArrayAttribute()
    {
        if (!$this->image) {
            return [];
        }

        return array_filter(explode(',', $this->image));
    }

    public function getMainImageAttribute()
    {
        $images = $this->images_array;
        return !empty($images) ? $images[0] : null;
    }

    public function getMainImageUrlAttribute()
    {
        $mainImage = $this->main_image;
        return $mainImage ? asset($mainImage) : null;
    }

    public function getAllImageUrlsAttribute()
    {
        $images = $this->images_array;
        return array_map(function($image) {
            return asset($image);
        }, $images);
    }

    public function hasMultipleImages()
    {
        return count($this->images_array) > 1;
    }

    public function getImageCountAttribute()
    {
        return count($this->images_array);
    }

    public function getImageByIndex($index)
    {
        $images = $this->images_array;
        return isset($images[$index]) ? $images[$index] : null;
    }

    public function getImageUrlByIndex($index)
    {
        $image = $this->getImageByIndex($index);
        return $image ? asset($image) : null;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?: 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }
}
