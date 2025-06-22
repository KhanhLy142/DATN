<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relationship với danh mục cha
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relationship với danh mục con
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relationship với sản phẩm (sẽ dùng sau)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scope để lấy danh mục đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope để lấy danh mục cha
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Accessor để hiển thị tên đầy đủ (có parent)
    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    // Accessor để hiển thị trạng thái
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Hiển thị' : 'Ẩn';
    }
}
