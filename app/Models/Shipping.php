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
        'ghn_province_id',
        'ghn_district_id',
        'ghn_ward_code',
        'province_name',
        'district_name',
        'ward_name',
        'shipping_fee',
        'tracking_code'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_fee' => 'decimal:2',
        'ghn_province_id' => 'integer',
        'ghn_district_id' => 'integer'
    ];

    protected $appends = [
        'shipping_status_label',
        'shipping_method_label',
        'formatted_shipping_fee',
        'full_address',
        'has_ghn_info'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePending($query)
    {
        return $query->where('shipping_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('shipping_status', 'confirmed');
    }

    public function scopeShipping($query)
    {
        return $query->where('shipping_status', 'shipping');
    }

    public function scopeDelivered($query)
    {
        return $query->where('shipping_status', 'delivered');
    }

    public function scopeShipped($query)
    {
        return $query->where('shipping_status', 'shipped');
    }

    public function scopeCompleted($query)
    {
        return $query->where('shipping_status', 'delivered');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('shipping_method', $method);
    }

    public function scopeByProvince($query, $provinceId = null, $provinceName = null)
    {
        if ($provinceId) {
            return $query->where('ghn_province_id', $provinceId);
        }
        if ($provinceName) {
            return $query->where('province_name', 'like', '%' . $provinceName . '%');
        }
        return $query;
    }

    public function scopeByDistrict($query, $districtId)
    {
        return $query->where('ghn_district_id', $districtId);
    }

    public function scopeByWard($query, $wardCode)
    {
        return $query->where('ghn_ward_code', $wardCode);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_id', 'like', '%' . $search . '%')
                ->orWhere('shipping_address', 'like', '%' . $search . '%')
                ->orWhere('tracking_code', 'like', '%' . $search . '%')
                ->orWhere('province_name', 'like', '%' . $search . '%')
                ->orWhere('district_name', 'like', '%' . $search . '%')
                ->orWhere('ward_name', 'like', '%' . $search . '%');
        });
    }

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
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'shipped' => 'Đang giao hàng',
            'completed' => 'Đã giao hàng'
        ];

        return $statuses[$this->shipping_status] ?? ucfirst($this->shipping_status);
    }

    public function getFormattedShippingFeeAttribute()
    {
        return $this->shipping_fee > 0 ? number_format($this->shipping_fee) . 'đ' : 'Miễn phí';
    }

    public function getShortAddressAttribute()
    {
        return \Str::limit($this->shipping_address, 50);
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->shipping_address,
            $this->ward_name,
            $this->district_name,
            $this->province_name
        ]);

        return implode(', ', $parts);
    }

    public function getHasGhnInfoAttribute()
    {
        return !empty($this->ghn_province_id) && !empty($this->ghn_district_id) && !empty($this->ghn_ward_code);
    }

    public function markAsConfirmed()
    {
        $this->update(['shipping_status' => 'confirmed']);

        Log::info('Shipping updated, order status left unchanged');

        return $this;
    }

    public function markAsShipping($trackingCode = null)
    {
        $updateData = ['shipping_status' => 'shipping'];

        if ($trackingCode) {
            $updateData['tracking_code'] = $trackingCode;
        }

        $this->update($updateData);

        if ($this->order) {
            $this->order->update(['status' => 'shipped']);
        }

        return $this;
    }

    public function markAsShipped($trackingCode = null)
    {
        return $this->markAsShipping($trackingCode);
    }

    public function markAsDelivered()
    {
        $this->update(['shipping_status' => 'delivered']);

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

    public function updateGhnInfo($provinceId, $districtId, $wardCode, $provinceName, $districtName, $wardName)
    {
        return $this->update([
            'ghn_province_id' => $provinceId,
            'ghn_district_id' => $districtId,
            'ghn_ward_code' => $wardCode,
            'province_name' => $provinceName,
            'district_name' => $districtName,
            'ward_name' => $wardName
        ]);
    }

    public function calculateShippingFee($weight = 500, $length = 20, $width = 20, $height = 10)
    {
        if (!$this->has_ghn_info) {
            return 0;
        }
        return $this->shipping_method === 'express' ? 30000 : 25000;
    }

    public function updateShippingFee($fee)
    {
        return $this->update(['shipping_fee' => $fee]);
    }

    public function canBeUpdated()
    {
        return in_array($this->shipping_status, ['pending', 'confirmed']);
    }

    public function canBeCancelled()
    {
        return in_array($this->shipping_status, ['pending', 'confirmed']);
    }

    public static function getStatistics()
    {
        return [
            'pending' => self::pending()->count(),
            'confirmed' => self::confirmed()->count(),
            'shipping' => self::shipping()->count(),
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

    public static function getProvinceStatistics()
    {
        return self::selectRaw('province_name, COUNT(*) as count')
            ->whereNotNull('province_name')
            ->groupBy('province_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    public static function getStatusStatistics()
    {
        return self::selectRaw('shipping_status, COUNT(*) as count')
            ->groupBy('shipping_status')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'shipping_status')
            ->toArray();
    }

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
