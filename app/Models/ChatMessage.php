<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'sender',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const SENDER_CUSTOMER = 'customer';
    const SENDER_CHATBOT = 'chatbot';

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function scopeFromCustomer($query)
    {
        return $query->where('sender', self::SENDER_CUSTOMER);
    }

    public function scopeFromChatbot($query)
    {
        return $query->where('sender', self::SENDER_CHATBOT);
    }

    public function isFromCustomer()
    {
        return $this->sender === self::SENDER_CUSTOMER;
    }

    public function isFromChatbot()
    {
        return $this->sender === self::SENDER_CHATBOT;
    }

    public function getSenderNameAttribute()
    {
        return $this->sender === self::SENDER_CUSTOMER ? 'Khách hàng' : 'AI Bot';
    }

    public function getSenderIconAttribute()
    {
        return $this->sender === self::SENDER_CUSTOMER ? '👤' : '🤖';
    }

    public function scopeFromUser($query)
    {
        return $this->scopeFromCustomer($query);
    }
}
