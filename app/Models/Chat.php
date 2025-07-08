<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'customer_id',
        'chat_status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function getFirstCustomerMessageAttribute()
    {
        return $this->messages()
            ->where('sender', 'customer')
            ->oldest()
            ->first();
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('chat_status', self::STATUS_ACTIVE);
    }

    public function scopeClosed($query)
    {
        return $query->where('chat_status', self::STATUS_CLOSED);
    }

    public function isActive()
    {
        return $this->chat_status === self::STATUS_ACTIVE;
    }

    public function getMessageCount()
    {
        return $this->messages()->count();
    }

    public function getCustomerMessageCount()
    {
        return $this->messages()->where('sender', 'customer')->count();
    }

    public function getChatbotMessageCount()
    {
        return $this->messages()->where('sender', 'chatbot')->count();
    }

    public function analyzeTopicFromMessages()
    {
        $customerMessages = $this->messages()
            ->where('sender', 'customer')
            ->pluck('message')
            ->implode(' ');

        $keywords = [
            'sản phẩm' => ['sản phẩm', 'mỹ phẩm', 'giá', 'chất lượng', 'thành phần'],
            'đơn hàng' => ['đơn hàng', 'đặt hàng', 'giao hàng', 'thanh toán', 'hoá đơn'],
            'hỗ trợ' => ['hỗ trợ', 'giúp đỡ', 'hướng dẫn', 'cách sử dụng'],
            'khiếu nại' => ['khiếu nại', 'phàn nàn', 'không hài lòng', 'lỗi', 'tệ']
        ];

        foreach ($keywords as $topic => $words) {
            foreach ($words as $word) {
                if (stripos($customerMessages, $word) !== false) {
                    return $topic;
                }
            }
        }

        return 'khác';
    }

    public function getChatDurationInMinutes()
    {
        $firstMessage = $this->messages()->oldest()->first();
        $lastMessage = $this->messages()->latest()->first();

        if ($firstMessage && $lastMessage) {
            return $firstMessage->created_at->diffInMinutes($lastMessage->created_at);
        }

        return 0;
    }
}
