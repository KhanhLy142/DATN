<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProductDiscount;
use Carbon\Carbon;
use App\Helpers\ProductFilterHelper;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filterData = ProductFilterHelper::getCachedFilterData();

        $query = Product::with(['brand', 'category'])
            ->where('status', 1)
            ->where('stock', '>', 0);

        $hasFilters = $request->hasAny(['category', 'brand', 'price_range', 'min_price', 'max_price']);

        if ($request->filled('category')) {
            $categoryId = $request->category;

            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function ($subQ) use ($categoryId) {
                        $subQ->where('parent_id', $categoryId);
                    });
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', $request->brand);
            });
        }

        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) == 2) {
                $minPrice = (float)$priceRange[0];
                $maxPrice = (float)$priceRange[1];

                if ($maxPrice == 99999999) {
                    $query->where('base_price', '>=', $minPrice);
                } else {
                    $query->whereBetween('base_price', [$minPrice, $maxPrice]);
                }
            }
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('id', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(6);

        if ($products->count() > 0) {
            $products->getCollection()->transform(function ($product) {
                return $this->enrichProductWithPromotions($product);
            });
        }

        return view('user.products.index', compact('products', 'filterData', 'hasFilters'));
    }

    public function show($id)
    {
        try {
            $product = Product::with(['brand', 'category'])
                ->where('status', 1)
                ->findOrFail($id);

            $product = $this->enrichProductWithPromotions($product);
            $variants = ProductVariant::where('product_id', $id)
                ->where('status', 1)
                ->get();

            $groupedVariants = [
                'colors' => [],
                'volumes' => [],
                'scents' => []
            ];

            if ($variants->count() > 0) {
                $uniqueColors = $variants->whereNotNull('color')->where('color', '!=', '')->pluck('color')->unique()->values();
                $uniqueVolumes = $variants->whereNotNull('volume')->where('volume', '!=', '')->pluck('volume')->unique()->values();
                $uniqueScents = $variants->whereNotNull('scent')->where('scent', '!=', '')->pluck('scent')->unique()->values();

                foreach ($uniqueColors as $color) {
                    if ($color) {
                        $variant = $variants->where('color', $color)->first();
                        $groupedVariants['colors'][] = [
                            'id' => $variant->id,
                            'value' => $color,
                            'name' => $color,
                            'price' => $variant->price ?? $product->base_price,
                            'price_adjustment' => $variant->price ? ($variant->price - $product->base_price) : 0,
                            'stock' => $variant->stock_quantity ?? 0
                        ];
                    }
                }

                foreach ($uniqueVolumes as $volume) {
                    if ($volume) {
                        $variant = $variants->where('volume', $volume)->first();
                        $groupedVariants['volumes'][] = [
                            'id' => $variant->id,
                            'value' => $volume,
                            'name' => $volume,
                            'price' => $variant->price ?? $product->base_price,
                            'price_adjustment' => $variant->price ? ($variant->price - $product->base_price) : 0,
                            'stock' => $variant->stock_quantity ?? 0
                        ];
                    }
                }

                foreach ($uniqueScents as $scent) {
                    if ($scent) {
                        $variant = $variants->where('scent', $scent)->first();
                        $groupedVariants['scents'][] = [
                            'id' => $variant->id,
                            'value' => $scent,
                            'name' => $scent,
                            'price' => $variant->price ?? $product->base_price,
                            'price_adjustment' => $variant->price ? ($variant->price - $product->base_price) : 0,
                            'stock' => $variant->stock_quantity ?? 0
                        ];
                    }
                }
            }

            $product->grouped_variants = $groupedVariants;
            $product->variants = $variants;


            $reviews = \App\Models\Review::where('product_id', $id)
                ->where('status', 1)
                ->with(['customer'])
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            $hasPurchased = false;
            $canReview = false;

            if (auth()->check()) {
                $customer = \App\Models\Customer::where('user_id', auth()->id())->first();

                if ($customer) {
                    $hasPurchased = \App\Models\Order::where('customer_id', $customer->id)
                        ->where('status', 'completed')
                        ->whereHas('orderItems', function($query) use ($id) {
                            $query->where('product_id', $id);
                        })
                        ->exists();

                    $hasReviewed = \App\Models\Review::where('product_id', $id)
                        ->where('customer_id', $customer->id)
                        ->exists();

                    $canReview = $hasPurchased && !$hasReviewed;
                }
            }

            $reviewStats = [
                'total_reviews' => $reviews->total(),
                'average_rating' => \App\Models\Review::where('product_id', $id)
                        ->where('status', 1)
                        ->avg('rating') ?? 0,
                'rating_breakdown' => []
            ];

            for ($i = 1; $i <= 5; $i++) {
                $count = \App\Models\Review::where('product_id', $id)
                    ->where('status', 1)
                    ->where('rating', $i)
                    ->count();

                $reviewStats['rating_breakdown'][$i] = [
                    'count' => $count,
                    'percentage' => $reviewStats['total_reviews'] > 0
                        ? round(($count / $reviewStats['total_reviews']) * 100, 1)
                        : 0
                ];
            }

            return view('user.products.detail', compact('product', 'reviews', 'reviewStats', 'hasPurchased', 'canReview'));

        } catch (\Exception $e) {
            \Log::error('Error loading product detail: ' . $e->getMessage());

            return redirect()->route('products.index')
                ->with('error', 'Không tìm thấy sản phẩm hoặc có lỗi xảy ra.');
        }
    }

    private function enrichProductWithPromotions($product)
    {
        $product->is_new = $product->created_at >= Carbon::now()->subDays(2);

        $productDiscount = ProductDiscount::with('discount')
            ->where('product_id', $product->id)
            ->whereHas('discount', function ($query) {
                $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->first();

        if ($productDiscount && $productDiscount->discount) {
            $discount = $productDiscount->discount;
            $basePrice = (float)$product->base_price;

            if ($discount->discount_type === 'percent') {
                $discountPercent = (float)$discount->discount_value;
                $finalPrice = $basePrice * (1 - $discountPercent / 100);
            } else {
                $discountAmount = (float)$discount->discount_value;
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
    }

    public function getVariantPrice(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'color' => 'nullable|string',
                'volume' => 'nullable|string',
                'scent' => 'nullable|string'
            ]);

            $productId = $request->input('product_id');
            $color = $request->input('color');
            $volume = $request->input('volume');
            $scent = $request->input('scent');

            $query = ProductVariant::where('product_id', $productId)->where('status', 1);

            if ($color) $query->where('color', $color);
            if ($volume) $query->where('volume', $volume);
            if ($scent) $query->where('scent', $scent);

            $variant = $query->first();

            if ($variant) {
                $product = Product::find($productId);
                $priceAdjustment = $variant->price - $product->base_price;

                return response()->json([
                    'success' => true,
                    'variant_id' => $variant->id,
                    'price' => $variant->price,
                    'formatted_price' => number_format($variant->price) . 'đ',
                    'price_adjustment' => $priceAdjustment,
                    'formatted_price_adjustment' => number_format($priceAdjustment) . 'đ',
                    'stock' => $variant->stock_quantity,
                    'variant_name' => $variant->variant_name,
                    'display_name' => $variant->display_name
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy variant phù hợp'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting variant price: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin variant'
            ], 500);
        }
    }

    public function detail($id)
    {
        return $this->show($id);
    }

    public function search(Request $request)
    {
        $filterData = ProductFilterHelper::getCachedFilterData();

        $query = Product::where('status', 1);

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('brand', function($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('category', function($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas('parent', function($parentQ) use ($searchTerm) {
                                $parentQ->where('name', 'like', '%' . $searchTerm . '%');
                            });
                    });
            });
        }

        $hasFilters = $request->hasAny(['category', 'brand', 'price_range', 'min_price', 'max_price']);

        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function ($subQ) use ($categoryId) {
                        $subQ->where('parent_id', $categoryId);
                    });
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', $request->brand);
            });
        }

        $products = $query->paginate(6);

        if ($products->count() > 0) {
            $products->getCollection()->transform(function ($product) {
                return $this->enrichProductWithPromotions($product);
            });
        }

        $searchTerm = $request->q;

        return view('user.products.search', compact('products', 'searchTerm', 'filterData', 'hasFilters'));
    }

    public function newProducts(Request $request)
    {
        $filterData = ProductFilterHelper::getCachedFilterData();

        $query = Product::with(['brand', 'category'])
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->where('created_at', '>=', Carbon::now()->subDays(2))
            ->orderBy('created_at', 'desc');

        $hasFilters = $request->hasAny(['category', 'brand', 'price_range', 'min_price', 'max_price']);

        if ($request->filled('category')) {
            $categoryId = $request->category;

            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function ($subQ) use ($categoryId) {
                        $subQ->where('parent_id', $categoryId);
                    });
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', $request->brand);
            });
        }

        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) == 2) {
                $minPrice = (float)$priceRange[0];
                $maxPrice = (float)$priceRange[1];

                if ($maxPrice == 99999999) {
                    $query->where('base_price', '>=', $minPrice);
                } else {
                    $query->whereBetween('base_price', [$minPrice, $maxPrice]);
                }
            }
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(6);

        if ($products->count() > 0) {
            $products->getCollection()->transform(function ($product) {
                return $this->enrichProductWithPromotions($product);
            });
        }

        return view('user.products.new', compact('products', 'filterData', 'hasFilters'));
    }

    public function discountedProducts(Request $request)
    {
        $filterData = ProductFilterHelper::getCachedFilterData();

        $query = Product::with(['brand', 'category'])
            ->whereHas('productDiscounts', function ($query) {
                $query->whereHas('discount', function ($discountQuery) {
                    $discountQuery->where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now());
                });
            })
            ->where('status', 1)
            ->where('stock', '>', 0);

        $hasFilters = $request->hasAny(['category', 'brand', 'price_range', 'min_price', 'max_price']);

        if ($request->filled('category')) {
            $categoryId = $request->category;

            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function ($subQ) use ($categoryId) {
                        $subQ->where('parent_id', $categoryId);
                    });
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', $request->brand);
            });
        }

        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) == 2) {
                $minPrice = (float)$priceRange[0];
                $maxPrice = (float)$priceRange[1];

                if ($maxPrice == 99999999) {
                    $query->where('base_price', '>=', $minPrice);
                } else {
                    $query->whereBetween('base_price', [$minPrice, $maxPrice]);
                }
            }
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(6);

        if ($products->count() > 0) {
            $products->getCollection()->transform(function ($product) {
                return $this->enrichProductWithPromotions($product);
            });
        }

        return view('user.products.discounted', compact('products', 'filterData', 'hasFilters'));
    }
}
