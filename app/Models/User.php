<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Customer;
use App\Models\Staff;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'user_type',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'user_type' => 'string',
        ];
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    public function isStaff()
    {
        return $this->user_type === 'staff';
    }

    public function isAdmin()
    {
        return $this->isStaff() && $this->staff && $this->staff->role === 'admin';
    }

    public function hasRole($role)
    {
        return $this->isStaff() && $this->staff && $this->staff->role === $role;
    }

    public function getStaffRole()
    {
        if ($this->isStaff() && $this->staff) {
            return $this->staff->role;
        }
        return null;
    }

    public function getUserRole()
    {
        if ($this->isStaff()) {
            return $this->getStaffRole();
        }

        if ($this->isCustomer()) {
            return 'customer';
        }

        return null;
    }

    public function canAccessAdmin()
    {
        return $this->isStaff();
    }

    public function canManage($feature)
    {
        $role = $this->getStaffRole();

        if ($role === 'admin') {
            return true;
        }

        $permissions = [
            'sales' => [
                'orders', 'customers', 'payments', 'shippings'
            ],
            'warehouse' => [
                'inventory', 'products', 'suppliers', 'categories', 'brands'
            ],
            'cskh' => [
                'customers', 'reviews', 'chats', 'discounts'
            ]
        ];

        return isset($permissions[$role]) && in_array($feature, $permissions[$role]);
    }

    public function hasPermission($permission)
    {
        if (!$this->isStaff()) {
            return false;
        }

        $role = $this->getStaffRole();

        if ($role === 'admin') {
            return true;
        }

        return checkRolePermission($role, $permission);
    }

    public function getDisplayNameWithRole()
    {
        $role = $this->getUserRole();
        $roleNames = [
            'admin' => 'Quản trị viên',
            'sales' => 'Nhân viên bán hàng',
            'warehouse' => 'Nhân viên kho',
            'cskh' => 'Chăm sóc khách hàng',
            'customer' => 'Khách hàng'
        ];

        $roleName = $roleNames[$role] ?? 'Người dùng';
        return "{$this->name} ({$roleName})";
    }

    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    public function scopeStaffs($query)
    {
        return $query->where('user_type', 'staff');
    }

    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->wasChanged(['name', 'email'])) {
                if ($user->customer) {
                    $user->customer->update([
                        'name' => $user->name,
                        'email' => $user->email,
                    ]);
                }

                if ($user->staff) {
                    $user->staff->update([
                        'name' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }
        });

        static::creating(function ($user) {
            if (empty($user->user_type)) {
                $user->user_type = 'customer';
            }
        });
    }
}
