<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relationship
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function imports()
    {
        return $this->hasMany(ProductImport::class);
    }
}
