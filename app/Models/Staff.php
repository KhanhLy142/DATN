<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Định nghĩa các role
    public static function getRoles()
    {
        return [
            'admin' => 'Quản trị viên',
            'sales' => 'Nhân viên bán hàng',
            'warehouse' => 'Nhân viên kho',
            'cskh' => 'Chăm sóc khách hàng'
        ];
    }

    // Lấy tên role tiếng Việt
    public function getRoleNameAttribute()
    {
        $roles = self::getRoles();
        return $roles[$this->role] ?? $this->role;
    }

    // Scope để lọc theo role
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Scope để tìm kiếm
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
