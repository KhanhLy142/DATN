<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\User;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // THÊM: Accessor để lấy password từ User
    public function getPasswordAttribute()
    {
        return $this->user ? $this->user->password : null;
    }

    // THÊM: Method để kiểm tra password
    public function checkPassword($password)
    {
        return $this->user ? Hash::check($password, $this->user->password) : false;
    }

    // THÊM: Method để cập nhật password (qua User)
    public function updatePassword($newPassword)
    {
        if ($this->user) {
            return $this->user->update([
                'password' => Hash::make($newPassword)
            ]);
        }
        return false;
    }

    // Accessors
    public function getFullContactAttribute()
    {
        return $this->name . ' - ' . ($this->phone ?? $this->email);
    }

    public function getFormattedPhoneAttribute()
    {
        return $this->phone ? $this->phone : 'Chưa có';
    }

    public function getFormattedAddressAttribute()
    {
        return $this->address ? $this->address : 'Chưa có địa chỉ';
    }

    // Scope để tìm kiếm
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }
}
