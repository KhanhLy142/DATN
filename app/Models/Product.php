<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'base_price',
        'stock',
        'brand_id',
        'category_id',
        'description',
        'image',
        'status'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean'
    ];

    /**
     * Quan hệ với Brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Quan hệ với Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Quan hệ với ProductVariant
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Quan hệ với Inventory
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function importItems()
    {
        return $this->hasMany(ProductImportItem::class);
    }

    /**
     * Lấy số lượng tồn kho
     */
    public function getStockQuantityAttribute(): int
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }

    /**
     * Scope để lấy sản phẩm đang active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope để lấy sản phẩm theo brand
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope để lấy sản phẩm theo category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Format giá tiền
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->base_price, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy URL ảnh
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset($this->image);
        }
        return asset('images/no-image.png'); // Ảnh mặc định
    }
}
