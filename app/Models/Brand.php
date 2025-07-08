<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'description',
        'logo',
        'country',
        'supplier_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getStatusTextAttribute()
    {
        return $this->status ? 'Hoạt động' : 'Ngưng hoạt động';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(trim($value));
    }
}
