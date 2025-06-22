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

    /**
     * Quan hệ với bảng products thông qua bảng product_discounts
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_discounts');
    }

    /**
     * Scope để lấy các mã giảm giá đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope để lấy các mã giảm giá còn hiệu lực
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_active', true);
    }

    /**
     * Kiểm tra mã giảm giá có hợp lệ không
     */
    public function isValid(): bool
    {
        $now = Carbon::now();

        return $this->is_active &&
            $this->start_date <= $now &&
            $this->end_date >= $now;
    }

    /**
     * Tính toán giá trị giảm giá
     * FIXED: Đảm bảo luôn trả về float, không bao giờ trả về null
     */
    public function calculateDiscount($originalPrice): float
    {
        // Đảm bảo originalPrice là số và không âm
        $originalPrice = (float) $originalPrice;
        if ($originalPrice <= 0) {
            return 0.0;
        }

        // Kiểm tra mã giảm giá có hợp lệ không
        if (!$this->isValid()) {
            return 0.0;
        }

        // Đảm bảo discount_value là số
        $discountValue = (float) $this->discount_value;
        if ($discountValue <= 0) {
            return 0.0;
        }

        if ($this->discount_type === 'percent') {
            // Giảm theo phần trăm
            $discountAmount = $originalPrice * ($discountValue / 100);
            // Đảm bảo không vượt quá giá gốc
            return min($discountAmount, $originalPrice);
        }

        // Giảm cố định - không vượt quá giá gốc
        return min($discountValue, $originalPrice);
    }

    /**
     * Lấy tên hiển thị của loại giảm giá
     */
    public function getDisplayTypeAttribute(): string
    {
        return $this->discount_type === 'percent' ? 'Phần trăm' : 'Cố định';
    }

    /**
     * Lấy giá trị hiển thị
     */
    public function getDisplayValueAttribute(): string
    {
        $value = (float) $this->discount_value;

        return $this->discount_type === 'percent'
            ? $value . '%'
            : number_format($value, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Lấy trạng thái hiển thị
     */
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

    /**
     * Lấy CSS class cho badge trạng thái
     */
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

    /**
     * Tính phần trăm giảm giá (để hiển thị)
     */
    public function getDiscountPercentage($originalPrice): float
    {
        $originalPrice = (float) $originalPrice;
        if ($originalPrice <= 0) {
            return 0.0;
        }

        $discountAmount = $this->calculateDiscount($originalPrice);
        return ($discountAmount / $originalPrice) * 100;
    }

    /**
     * Lấy giá cuối sau khi áp dụng giảm giá
     */
    public function getFinalPrice($originalPrice): float
    {
        $originalPrice = (float) $originalPrice;
        $discountAmount = $this->calculateDiscount($originalPrice);

        return max(0, $originalPrice - $discountAmount);
    }

    /**
     * Kiểm tra xem mã giảm giá có áp dụng được cho sản phẩm này không
     */
    public function isApplicableToProduct($productId): bool
    {
        // Nếu không có sản phẩm nào được chỉ định, áp dụng cho tất cả
        if ($this->products()->count() === 0) {
            return true;
        }

        // Kiểm tra sản phẩm có trong danh sách được áp dụng
        return $this->products()->where('product_id', $productId)->exists();
    }

    /**
     * Lấy danh sách sản phẩm áp dụng (để hiển thị)
     */
    public function getApplicableProductsTextAttribute(): string
    {
        $count = $this->products()->count();

        if ($count === 0) {
            return 'Tất cả sản phẩm';
        }

        return $count . ' sản phẩm được chọn';
    }
}
