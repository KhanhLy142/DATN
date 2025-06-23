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
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    // Events - Đồng bộ password khi thay đổi
    protected static function booted()
    {
        // Khi User được cập nhật
        static::updated(function ($user) {
            // Nếu password thay đổi, đồng bộ sang Customer/Staff
            if ($user->wasChanged('password')) {
                // Đồng bộ password cho Customer
                if ($user->customer) {
                    $user->customer->update([
                        'password' => $user->password, // Đã được hash tự động
                    ]);
                }

                // Đồng bộ password cho Staff
                if ($user->staff) {
                    $user->staff->update([
                        'password' => $user->password, // Đã được hash tự động
                    ]);
                }
            }

            // Đồng bộ name và email
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
    }

    // Helper methods
    public function isCustomer()
    {
        return $this->customer !== null;
    }

    public function isStaff()
    {
        return $this->staff !== null;
    }

    public function getUserType()
    {
        if ($this->isStaff()) return 'staff';
        if ($this->isCustomer()) return 'customer';
        return 'user';
    }

    public function canAccessAdmin()
    {
        return $this->isStaff();
    }
}
