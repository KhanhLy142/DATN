<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'tracking_number',
        'customer_id',
        'total_amount',
        'status',
        'note',
        'vnpay_transaction_id'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function shipping(): HasOne
    {
        return $this->hasOne(Shipping::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }


    public function getCustomerNoteAttribute()
    {
        if ($this->isJsonNote()) {
            $data = json_decode($this->attributes['note'], true);
            return $data['__customer_note__'] ?? null;
        }

        return $this->attributes['note'];
    }

    public function getSystemDataAttribute()
    {
        if ($this->isJsonNote()) {
            $data = json_decode($this->attributes['note'], true);
            unset($data['__customer_note__'], $data['__version__']);
            return $data;
        }
        return [];
    }

    public function getOrderMetadata()
    {
        return $this->system_data;
    }

    private function isJsonNote()
    {
        return is_string($this->attributes['note'] ?? '') &&
            str_starts_with($this->attributes['note'], '{') &&
            json_decode($this->attributes['note'], true) !== null;
    }

    public function getTotalItemsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->name : 'N/A';
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->customer ? $this->customer->phone : null;
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.') . 'đ';
    }

    public function getFormattedNoteAttribute()
    {
        return $this->note ? strip_tags($this->note) : null;
    }

    public function getVnpayTransactionAttribute()
    {
        return $this->vnpay_transaction_id;
    }

    public function calculateTotal()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function updateStatus($status)
    {
        $this->update(['status' => $status]);

        return $this;
    }

    public function canBeEdited()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function setVnpayTransaction($transactionId)
    {
        $this->update(['vnpay_transaction_id' => $transactionId]);
        return $this;
    }

    public function scopeByVnpayTransaction($query, $transactionId)
    {
        return $query->where('vnpay_transaction_id', $transactionId);
    }

    public function canBeCancelled()
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            return false;
        }

        if (!$this->payment) {
            return false;
        }

        switch ($this->payment->payment_method) {
            case 'cod':
                if ($this->shipping) {
                    return $this->shipping->shipping_status === 'pending';
                }
                return true;

            case 'vnpay':
            case 'bank_transfer':
                return $this->payment->payment_status === 'pending';

            default:
                return $this->payment->payment_status !== 'completed';
        }
    }

    public function getSubtotalAttribute()
    {
        return $this->orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Customer::class, 'id', 'id', 'customer_id', 'user_id');
    }

    public function getPaymentMethodLabelAttribute()
    {
        if (!$this->payment) {
            return 'Chưa có';
        }

        $labels = [
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'vnpay' => 'VNPay',
        ];

        return $labels[$this->payment->payment_method] ?? $this->payment->payment_method;
    }
    public function getDiscountAttribute()
    {
        $metadata = $this->getOrderMetadata();
        $discountInfo = $metadata['coupon_info'] ?? null;

        if (!$discountInfo) {
            return null;
        }

        return (object) [
            'code' => $discountInfo['code'] ?? null,
            'discount_type' => $discountInfo['type'] ?? null,
            'discount_value' => $discountInfo['discount_amount'] ?? 0,
            'display_value' => $discountInfo['display_value'] ?? null
        ];
    }

    public function hasDiscount(): bool
    {
        $metadata = $this->getOrderMetadata();
        return isset($metadata['coupon_info']) && !empty($metadata['coupon_info']);
    }
}
