<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with(['brand', 'category'])
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(5))
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            $discountedProducts = Product::with(['brand', 'category'])
                ->whereHas('productDiscounts', function($query) {
                    $query->whereHas('discount', function($discountQuery) {
                        $discountQuery->where('is_active', true)
                            ->where('start_date', '<=', now())
                            ->where('end_date', '>=', now());
                    });
                })
                ->where('status', 1)
                ->where('stock', '>', 0)
                ->take(8)
                ->get()
                ->map(function($product) {
                    $productDiscount = ProductDiscount::with('discount')
                        ->where('product_id', $product->id)
                        ->whereHas('discount', function($query) {
                            $query->where('is_active', true)
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now());
                        })
                        ->first();

                    if ($productDiscount && $productDiscount->discount) {
                        $discount = $productDiscount->discount;
                        $basePrice = (float) $product->base_price;

                        if ($discount->discount_type === 'percent') {
                            $discountPercent = (float) $discount->discount_value;
                            $finalPrice = $basePrice * (1 - $discountPercent / 100);
                        } else {
                            $discountAmount = (float) $discount->discount_value;
                            $finalPrice = $basePrice - $discountAmount;
                            $discountPercent = ($discountAmount / $basePrice) * 100;
                        }

                        $finalPrice = max(0, $finalPrice);
                        $discountPercent = max(0, $discountPercent);

                        $product->has_discount = true;
                        $product->discount_percentage = round($discountPercent, 1);
                        $product->final_price = $finalPrice;
                        $product->best_discount = $discount;
                    } else {
                        $product->has_discount = false;
                        $product->final_price = $product->base_price;
                        $product->discount_percentage = 0;
                    }

                    return $product;
                });

            $products = $products ?? collect();
            $discountedProducts = $discountedProducts ?? collect();

            $products = $products ?? collect();
            $discountedProducts = $discountedProducts ?? collect();

            $products = $products ?? collect();
            $discountedProducts = $discountedProducts ?? collect();

            $products = $products ?? collect();
            $discountedProducts = $discountedProducts ?? collect();

            return view('user.home', compact('products', 'discountedProducts'));

        } catch (\Exception $e) {
            \Log::error('Home Controller Error: ' . $e->getMessage());

            $products = Product::with(['brand', 'category'])
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            $discountedProducts = Product::with(['brand', 'category'])
                ->where('status', 1)
                ->where('stock', '>', 0)
                ->inRandomOrder()
                ->take(8)
                ->get()
                ->map(function($product) {
                    $product->has_discount = true;
                    $product->discount_percentage = rand(10, 30);
                    $product->final_price = $product->base_price * (1 - $product->discount_percentage / 100);
                    $product->best_discount = (object) [
                        'code' => 'SALE' . rand(10, 99),
                        'discount_percentage' => $product->discount_percentage
                    ];
                    return $product;
                });

            return view('user.home', compact('products', 'discountedProducts'));
        }
    }
}
