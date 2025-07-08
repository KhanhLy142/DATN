<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'applies_to',
        'description',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_discounts');
    }

    public function discounts(){
        return $this->hasMany(ProductDiscount::class, 'discount_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_active', true);
    }

    public function isValid(): bool
    {
        $now = Carbon::now();

        return $this->is_active &&
            $this->start_date <= $now &&
            $this->end_date >= $now;
    }

    public function calculateDiscount($originalPrice): float
    {
        $originalPrice = (float) $originalPrice;
        if ($originalPrice <= 0) {
            return 0.0;
        }

        if (!$this->isValid()) {
            return 0.0;
        }

        $discountValue = (float) $this->discount_value;
        if ($discountValue <= 0) {
            return 0.0;
        }

        if ($this->discount_type === 'percent') {
            $discountAmount = $originalPrice * ($discountValue / 100);
            return min($discountAmount, $originalPrice);
        }

        return min($discountValue, $originalPrice);
    }


    public function getDisplayTypeAttribute(): string
    {
        return $this->discount_type === 'percent' ? 'Phần trăm' : 'Cố định';
    }

    public function getDisplayValueAttribute(): string
    {
        $value = (float) $this->discount_value;

        return $this->discount_type === 'percent'
            ? $value . '%'
            : number_format($value, 0, ',', '.') . ' VNĐ';
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Không hoạt động';
        }

        $now = Carbon::now();

        if ($this->start_date > $now) {
            return 'Chưa bắt đầu';
        }

        if ($this->end_date < $now) {
            return 'Đã hết hạn';
        }

        return 'Đang hoạt động';
    }

    public function getStatusClassAttribute(): string
    {
        switch ($this->status) {
            case 'Đang hoạt động':
                return 'success';
            case 'Chưa bắt đầu':
                return 'warning';
            case 'Đã hết hạn':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function getDiscountPercentage($originalPrice): float
    {
        $originalPrice = (float) $originalPrice;
        if ($originalPrice <= 0) {
            return 0.0;
        }

        $discountAmount = $this->calculateDiscount($originalPrice);
        return ($discountAmount / $originalPrice) * 100;
    }

    public function getFinalPrice($originalPrice): float
    {
        $originalPrice = (float) $originalPrice;
        $discountAmount = $this->calculateDiscount($originalPrice);

        return max(0, $originalPrice - $discountAmount);
    }

    public function isApplicableToProduct($productId): bool
    {
        if ($this->products()->count() === 0) {
            return true;
        }

        return $this->products()->where('product_id', $productId)->exists();
    }

    public function getApplicableProductsTextAttribute(): string
    {
        $count = $this->products()->count();

        if ($count === 0) {
            return 'Tất cả sản phẩm';
        }

        return $count . ' sản phẩm được chọn';
    }
}
