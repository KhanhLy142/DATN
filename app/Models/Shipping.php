<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipping extends Model
{
    protected $fillable = [
        'order_id',
        'shipping_address',
        'shipping_method',
        'shipping_status',
        'province',
        'shipping_fee',
        'shipping_note',
        'tracking_code'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_fee' => 'decimal:2'
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('shipping_status', 'pending');
    }

    public function scopeShipped($query)
    {
        return $query->where('shipping_status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('shipping_status', 'delivered');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('shipping_method', $method);
    }

    public function scopeByProvince($query, $province)
    {
        return $query->where('province', 'like', '%' . $province . '%');
    }

    // Scope tìm kiếm
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_id', 'like', '%' . $search . '%')
                ->orWhere('shipping_address', 'like', '%' . $search . '%')
                ->orWhere('tracking_code', 'like', '%' . $search . '%')
                ->orWhere('province', 'like', '%' . $search . '%');
        });
    }

    // Accessors
    public function getShippingMethodLabelAttribute()
    {
        $methods = [
            'standard' => 'Giao hàng tiêu chuẩn',
            'express' => 'Giao hàng nhanh'
        ];

        return $methods[$this->shipping_method] ?? $this->shipping_method;
    }

    public function getShippingStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Chờ giao hàng',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng'
        ];

        return $statuses[$this->shipping_status] ?? $this->shipping_status;
    }

    public function getFormattedShippingFeeAttribute()
    {
        return $this->shipping_fee > 0 ? number_format($this->shipping_fee) . 'đ' : 'Miễn phí';
    }

    public function getShortAddressAttribute()
    {
        return \Str::limit($this->shipping_address, 50);
    }

    // Methods
    public function markAsShipped($trackingCode = null)
    {
        $updateData = ['shipping_status' => 'shipped'];

        if ($trackingCode) {
            $updateData['tracking_code'] = $trackingCode;
        }

        $this->update($updateData);

        // Cập nhật trạng thái đơn hàng
        if ($this->order) {
            $this->order->update(['status' => 'shipped']);
        }

        return $this;
    }

    public function markAsDelivered()
    {
        $this->update(['shipping_status' => 'delivered']);

        // Cập nhật trạng thái đơn hàng thành completed
        if ($this->order) {
            $this->order->update(['status' => 'completed']);
        }

        return $this;
    }

    public function generateTrackingCode()
    {
        if (!$this->tracking_code) {
            $prefix = $this->shipping_method === 'express' ? 'EX' : 'ST';
            $trackingCode = $prefix . date('Ymd') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->update(['tracking_code' => $trackingCode]);
        }

        return $this->tracking_code;
    }

    // Static methods để lấy thống kê
    public static function getStatistics()
    {
        return [
            'pending' => self::pending()->count(),
            'shipped' => self::shipped()->count(),
            'delivered' => self::delivered()->count(),
            'total' => self::count(),
            'total_fee' => self::sum('shipping_fee')
        ];
    }

    public static function getMethodStatistics()
    {
        return [
            'standard' => self::byMethod('standard')->count(),
            'express' => self::byMethod('express')->count()
        ];
    }

    // Boot method để tự động tạo tracking code
    protected static function boot()
    {
        parent::boot();

        static::created(function ($shipping) {
            if (!$shipping->tracking_code) {
                $shipping->generateTrackingCode();
            }
        });
    }
}
