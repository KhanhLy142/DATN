<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_note',
        'vnpay_transaction_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function saveVNPayResponse($vnpayData)
    {
        $responseCode = $vnpayData['vnp_ResponseCode'] ?? '';
        $transactionNo = $vnpayData['vnp_TransactionNo'] ?? '';

        if ($responseCode === '00') {
            $vnpayInfo = [
                'transaction_no' => $transactionNo,
                'response_code' => $responseCode,
                'amount' => isset($vnpayData['vnp_Amount']) ? ($vnpayData['vnp_Amount'] / 100) : 0,
                'bank_code' => $vnpayData['vnp_BankCode'] ?? '',
                'pay_date' => $vnpayData['vnp_PayDate'] ?? '',
                'status' => 'success'
            ];

            $this->update([
                'payment_status' => 'completed',
                'vnpay_transaction_id' => $transactionNo,
                'payment_note' => 'VNPay: ' . json_encode($vnpayInfo)
            ]);

        } else {
            $vnpayInfo = [
                'response_code' => $responseCode,
                'status' => 'failed',
                'message' => $this->getVNPayErrorMessage($responseCode)
            ];

            $this->update([
                'payment_status' => 'failed',
                'vnpay_transaction_id' => $transactionNo,
                'payment_note' => 'VNPay: ' . json_encode($vnpayInfo)
            ]);
        }
    }

    public function getVNPayInfo()
    {
        if ($this->payment_method !== 'vnpay' || !$this->payment_note) {
            return null;
        }

        if (strpos($this->payment_note, 'VNPay:') === 0) {
            $jsonData = substr($this->payment_note, 7);
            return json_decode($jsonData, true);
        }

        return null;
    }

    public function getVNPayTransactionNoAttribute()
    {
        if ($this->vnpay_transaction_id) {
            return $this->vnpay_transaction_id;
        }

        $vnpayInfo = $this->getVNPayInfo();
        return $vnpayInfo['transaction_no'] ?? null;
    }

    private function getVNPayErrorMessage($code)
    {
        $messages = [
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking.',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán.',
            '12' => 'Thẻ/Tài khoản bị khóa.',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ số dư.',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Các lỗi khác'
        ];

        return $messages[$code] ?? 'Giao dịch thất bại - Mã lỗi: ' . $code;
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cod' => 'Thanh toán khi nhận hàng',
            'vnpay' => 'VNPay',
            'bank_transfer' => 'Chuyển khoản ngân hàng'
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    public function canBeRefunded()
    {
        return $this->payment_status === 'completed' &&
            in_array($this->payment_method, ['vnpay', 'bank_transfer']);
    }

    public function canBeUpdated()
    {
        return $this->payment_status === 'pending';
    }

    public function markAsRefunded($reason = '')
    {
        $this->update([
            'payment_status' => 'refunded',
            'payment_note' => $this->payment_note . ' | Hoàn tiền: ' . $reason
        ]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('order', function($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
                ->orWhere('tracking_number', 'like', "%{$search}%")
                ->orWhere('vnpay_transaction_id', 'like', "%{$search}%");
        })->orWhere('vnpay_transaction_id', 'like', "%{$search}%");
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

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

    public function scopeByVnpayTransaction($query, $transactionId)
    {
        return $query->where('vnpay_transaction_id', $transactionId);
    }

    public function scopeVnpayOnly($query)
    {
        return $query->where('payment_method', 'vnpay');
    }

}
