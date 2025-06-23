<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 1); // Chỉ lấy sản phẩm đang hoạt động

        // Filter theo category
        if ($request->filled('category')) {
            // Nếu bạn có relationship với Category model
            if (class_exists('App\Models\Category')) {
                $query->whereHas('category', function($q) use ($request) {
                    $q->where('slug', $request->category)
                        ->orWhere('name', 'like', '%' . $request->category . '%');
                });
            } else {
                // Nếu category là string field trong products table
                $query->where('category', $request->category);
            }
        }

        // Filter theo brand
        if ($request->filled('brand')) {
            // Nếu bạn có relationship với Brand model
            if (class_exists('App\Models\Brand')) {
                $query->whereHas('brand', function($q) use ($request) {
                    $q->where('name', $request->brand);
                });
            } else {
                // Nếu brand là string field trong products table
                $query->where('brand', $request->brand);
            }
        }

        // Filter theo price range
        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) == 2) {
                $minPrice = (float) $priceRange[0];
                $maxPrice = (float) $priceRange[1];

                if ($maxPrice == 999) { // Trên $100
                    $query->where('price', '>=', $minPrice);
                } else {
                    $query->whereBetween('price', [$minPrice, $maxPrice]);
                }
            }
        }

        // Custom price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('id', 'desc'); // Hoặc field khác nếu có
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // QUAN TRỌNG: Sử dụng paginate() thay vì all()
        $products = $query->paginate(6);

        return view('user.products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::where('status', 1)->findOrFail($id);

        // Lấy sản phẩm liên quan (cùng category nếu có)
        $relatedProducts = Product::where('status', 1)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return view('user.products.show', compact('product', 'relatedProducts'));
    }

    public function detail($id)
    {
        // Alias cho method show
        return $this->show($id);
    }

    public function search(Request $request)
    {
        $query = Product::where('status', 1);

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $products = $query->paginate(12);
        $searchTerm = $request->q;

        return view('user.products.search', compact('products', 'searchTerm'));
    }
}
