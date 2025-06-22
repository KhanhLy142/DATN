<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_status',
        'momo_transaction_id',
        'payment_note',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }

    // Scope for search
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('momo_transaction_id', 'like', "%{$search}%")
                    ->orWhere('payment_note', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    // Scope for filter by method
    public function scopeByMethod($query, $method)
    {
        if ($method) {
            return $query->where('payment_method', $method);
        }
        return $query;
    }

    // Scope for filter by status
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('payment_status', $status);
        }
        return $query;
    }

    // Scope for date range
    public function scopeDateRange($query, $dateFrom, $dateTo)
    {
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        return $query;
    }

    // Accessors
    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'cod' => 'COD',
            'momo' => 'MoMo'
        ];

        return $methods[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function getPaymentStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Đang chờ',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại'
        ];

        return $statuses[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . 'đ';
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update(['payment_status' => 'completed']);

        // Cập nhật trạng thái đơn hàng
        if ($this->order && $this->order->status === 'pending') {
            $this->order->update(['status' => 'processing']);
        }
    }

    public function markAsFailed()
    {
        $this->update(['payment_status' => 'failed']);
    }

    public function markAsRefunded($reason = null)
    {
        $this->update([
            'payment_status' => 'refunded',
            'payment_note' => $reason ? "Hoàn tiền: {$reason}" : 'Hoàn tiền'
        ]);
    }

    public function canBeRefunded()
    {
        return in_array($this->payment_status, ['completed']);
    }

    public function canBeUpdated()
    {
        return $this->payment_status === 'pending';
    }

    // Thêm vào PaymentController.php

    public function show(Payment $payment)
    {
        $payment->load('order'); // Load thông tin đơn hàng liên quan

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        // Chỉ cho phép edit payment đang pending
        if ($payment->payment_status !== 'pending') {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Chỉ có thể cập nhật giao dịch đang chờ xử lý');
        }

        return view('admin.payments.edit', compact('payment'));
    }
}
