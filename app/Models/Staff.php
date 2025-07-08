<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getRoles()
    {
        return [
            'admin' => 'Quản trị viên',
            'sales' => 'Nhân viên bán hàng',
            'warehouse' => 'Nhân viên kho',
            'cskh' => 'Chăm sóc khách hàng'
        ];
    }

    public function getRoleNameAttribute()
    {
        $roles = self::getRoles();
        return $roles[$this->role] ?? $this->role;
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        });
    }

    public function getLoginEmail()
    {
        return $this->user->email ?? $this->email;
    }

    public function canLogin()
    {
        return $this->user !== null;
    }

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

    public function hasPermission($permission)
    {
        if ($this->role === 'admin') {
            return true;
        }

        return \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role', $this->role)
            ->where('permissions.name', $permission)
            ->exists();
    }

    public function getPermissions()
    {
        if ($this->role === 'admin') {
            return \DB::table('permissions')->pluck('name')->toArray();
        }

        return \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role', $this->role)
            ->pluck('permissions.name')
            ->toArray();
    }

    protected static function booted()
    {
        static::updated(function ($staff) {
            if ($staff->user && $staff->wasChanged(['name', 'email'])) {
                $staff->user->update([
                    'name' => $staff->name,
                    'email' => $staff->email,
                ]);
            }
        });

        static::deleting(function ($staff) {
            if ($staff->user) {
                $staff->user->delete();
            }
        });
    }
}
