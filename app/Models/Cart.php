<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'session_id',
        'product_id',
        'variant_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Customer::class, 'id', 'id', 'customer_id', 'user_id');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeForCustomerOrSession($query, $customerId = null, $sessionId = null)
    {
        return $query->where(function ($q) use ($customerId, $sessionId) {
            if ($customerId) {
                $q->where('customer_id', $customerId);
            } elseif ($sessionId) {
                $q->where('session_id', $sessionId);
            }
        });
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price) . '₫';
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal) . '₫';
    }

    public function getOriginalPriceAttribute()
    {
        return $this->variant ? $this->variant->price : $this->product->base_price;
    }

    public function getHasSaleAttribute()
    {
        return $this->original_price > $this->price;
    }

    public function getSalePercentageAttribute()
    {
        if (!$this->has_sale || $this->original_price <= 0) {
            return 0;
        }

        return round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    public function getSavingsAmountAttribute()
    {
        return max(0, $this->original_price - $this->price);
    }

    public function getFormattedSavingsAttribute()
    {
        return number_format($this->savings_amount) . '₫';
    }

    public function getVariantDisplayNameAttribute()
    {
        if (!$this->variant) {
            return null;
        }

        $parts = [];

        if ($this->variant->color) {
            $parts[] = "Màu: {$this->variant->color}";
        }

        if ($this->variant->volume) {
            $parts[] = "Dung tích: {$this->variant->volume}";
        }

        if ($this->variant->scent) {
            $parts[] = "Mùi hương: {$this->variant->scent}";
        }

        return implode(', ', $parts);
    }

    public function updatePrice()
    {
        if ($this->variant && $this->variant->price > 0) {
            $this->price = $this->variant->price;
        } elseif ($this->product) {
            $this->price = $this->product->base_price;
        }
        $this->save();
    }

    public function updatePriceWithSale()
    {
        $originalPrice = $this->original_price;

        $productSaleDiscount = Discount::valid()
            ->where('applies_to', 'product')
            ->whereHas('products', function($query) {
                $query->where('product_id', $this->product_id);
            })
            ->first();

        if ($productSaleDiscount) {
            $discountAmount = $productSaleDiscount->calculateDiscount($originalPrice);
            $this->price = max(0, $originalPrice - $discountAmount);
        } else {
            $this->price = $originalPrice;
        }

        $this->save();
    }

    public function isGuestCart()
    {
        return is_null($this->customer_id) && !is_null($this->session_id);
    }


    public function isCustomerCart()
    {
        return !is_null($this->customer_id);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            if (!$cart->price) {
                if ($cart->variant && $cart->variant->price > 0) {
                    $cart->price = $cart->variant->price;
                } elseif ($cart->product) {
                    $cart->price = $cart->product->base_price;
                }
            }
        });
    }
}
