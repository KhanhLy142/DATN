<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Bỏ Authenticatable vì authentication qua User

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        // Bỏ 'password' vì đã chuyển sang users
    ];

    // Bỏ hidden password và remember_token
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship với User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

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
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function getLoginEmail()
    {
        return $this->user->email ?? $this->email;
    }

    public function canLogin()
    {
        return $this->user !== null;
    }

    // Kiểm tra quyền
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function canAccessAdmin()
    {
        return in_array($this->role, ['admin', 'sales', 'warehouse', 'cskh']);
    }

    public function canManageProducts()
    {
        return in_array($this->role, ['admin', 'warehouse']);
    }

    public function canManageOrders()
    {
        return in_array($this->role, ['admin', 'sales']);
    }

    public function canManageCustomers()
    {
        return in_array($this->role, ['admin', 'cskh']);
    }
}
