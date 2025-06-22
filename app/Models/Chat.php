<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'chat_status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship với User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship với ChatMessages
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    // Lấy tin nhắn cuối cùng
    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    // Scope để lọc chat đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('chat_status', 'active');
    }
}



