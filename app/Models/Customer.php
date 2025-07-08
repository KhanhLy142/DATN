<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getPasswordAttribute()
    {
        return $this->user ? $this->user->password : null;
    }

    public function checkPassword($password)
    {
        return $this->user ? Hash::check($password, $this->user->password) : false;
    }

    public function updatePassword($newPassword)
    {
        if ($this->user) {
            return $this->user->update([
                'password' => Hash::make($newPassword)
            ]);
        }
        return false;
    }

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

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }

    protected static function booted()
    {
        static::creating(function ($customer) {
            if (!$customer->user_id && $customer->email) {
                $user = User::create([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'user_type' => 'customer',
                    'password' => Hash::make('password123'),
                ]);
                $customer->user_id = $user->id;
            }
        });

        static::updated(function ($customer) {
            if ($customer->user && $customer->wasChanged(['name', 'email'])) {
                $customer->user->update([
                    'name' => $customer->name,
                    'email' => $customer->email,
                ]);
            }
        });
    }
}
