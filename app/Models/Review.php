<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'rating',
        'comment',
        'status',
        'reply'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
    }

    public function scopeByRating($query, $rating)
    {
        if ($rating) {
            return $query->where('rating', $rating);
        }
        return $query;
    }

    public function scopeByProduct($query, $productId)
    {
        if ($productId) {
            return $query->where('product_id', $productId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status !== null) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function getStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating ? '⭐' : '☆';
        }
        return $stars;
    }

    public function getShortCommentAttribute()
    {
        return strlen($this->comment) > 50
            ? substr($this->comment, 0, 50) . '...'
            : $this->comment;
    }

    public function getStatusTextAttribute()
    {
        return $this->status ? 'Hiển thị' : 'Ẩn';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status
            ? '<span class="badge bg-success">Hiển thị</span>'
            : '<span class="badge bg-secondary">Ẩn</span>';
    }
}
