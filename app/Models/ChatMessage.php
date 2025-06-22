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

    // Relationship với Chat
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    // Scope để lọc tin nhắn từ user
    public function scopeFromUser($query)
    {
        return $query->where('sender', 'user');
    }

    // Scope để lọc tin nhắn từ chatbot
    public function scopeFromChatbot($query)
    {
        return $query->where('sender', 'chatbot');
    }
}
