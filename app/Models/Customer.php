<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order; // THÊM DÒNG NÀY

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
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
}
