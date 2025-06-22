<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
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

    // Scopes
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

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Accessors & Mutators
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã giao hàng',
            'delivered' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
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

    // Methods
    public function calculateTotal()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function updateStatus($status)
    {
        $this->update(['status' => $status]);

        // Auto update related records
        if ($this->payment) {
            $paymentStatus = 'pending';
            switch ($status) {
                case 'delivered':
                    $paymentStatus = 'completed';
                    break;
                case 'cancelled':
                    $paymentStatus = 'failed';
                    break;
            }
            $this->payment->update(['payment_status' => $paymentStatus]);
        }

        if ($this->shipping) {
            $shippingStatus = 'pending';
            switch ($status) {
                case 'shipped':
                    $shippingStatus = 'shipped';
                    break;
                case 'delivered':
                    $shippingStatus = 'delivered';
                    break;
                case 'cancelled':
                    $shippingStatus = 'cancelled';
                    break;
            }
            $this->shipping->update(['shipping_status' => $shippingStatus]);
        }
    }

    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    public function canBeEdited()
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }
}
