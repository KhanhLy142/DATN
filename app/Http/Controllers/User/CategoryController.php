<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ProductFilterHelper;
use App\Helpers\CategoryHelper;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function show(Request $request, $id)
    {
        \Log::info('=== CategoryController::show STARTED ===');
        \Log::info('Category ID: ' . $id);
        \Log::info('Full URL: ' . $request->fullUrl());
        \Log::info('Request method: ' . $request->method());

        try {
            $categoryExists = \DB::table('categories')->where('id', $id)->exists();
            \Log::info('Category exists in DB: ' . ($categoryExists ? 'YES' : 'NO'));
        } catch (\Exception $e) {
            \Log::error('Database error: ' . $e->getMessage());
        }

        try {
            $category = Category::where('status', 1)->find($id);

            if (!$category) {
                \Log::error('Category not found with ID: ' . $id);
                return redirect()->route('products.index')
                    ->with('error', 'Không tìm thấy danh mục với ID: ' . $id);
            }

            \Log::info('Found category: ' . $category->name);

            try {
                $categoryIds = CategoryHelper::getCategoryWithChildren($id);
                \Log::info('Category IDs from helper: ' . json_encode($categoryIds));
            } catch (\Exception $e) {
                \Log::error('CategoryHelper error: ' . $e->getMessage());
                $categoryIds = [$id];
            }

            try {
                $filterData = ProductFilterHelper::getCategoryPageFilterData();
                \Log::info('Filter data loaded successfully');
            } catch (\Exception $e) {
                \Log::error('ProductFilterHelper error: ' . $e->getMessage());
                $filterData = [
                    'brands' => [],
                    'priceRanges' => []
                ];
            }

            $query = Product::with(['brand', 'category'])
                ->where('status', 1)
                ->where('stock', '>', 0)
                ->whereIn('category_id', $categoryIds);

            $totalProducts = $query->count();
            \Log::info('Total products found: ' . $totalProducts);

            $hasFilters = $request->hasAny(['brand', 'price_range', 'min_price', 'max_price']);

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
            \Log::info('Products after pagination: ' . $products->count());

            if ($products->count() > 0) {
                $products->getCollection()->transform(function ($product) {
                    return $this->enrichProductWithPromotions($product);
                });
            }

            try {
                $breadcrumb = CategoryHelper::getBreadcrumb($id);
                \Log::info('Breadcrumb loaded successfully');
            } catch (\Exception $e) {
                \Log::error('Breadcrumb error: ' . $e->getMessage());
                $breadcrumb = [];
            }

            \Log::info('=== ATTEMPTING TO LOAD VIEW ===');
            \Log::info('View path: user.category.show');

            if (!view()->exists('user.category.show')) {
                \Log::error('VIEW NOT FOUND: user.category.show');
                return response('View user.category.show not found', 404);
            }

            return view('user.category.show', compact(
                'category',
                'products',
                'filterData',
                'hasFilters',
                'breadcrumb'
            ));

        } catch (\Exception $e) {
            \Log::error('=== CategoryController ERROR ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile());
            \Log::error('Error line: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Debug Mode',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function enrichProductWithPromotions($product)
    {
        $product->is_new = $product->created_at >= Carbon::now()->subDays(2);

        $productDiscount = \App\Models\ProductDiscount::with('discount')
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
}
