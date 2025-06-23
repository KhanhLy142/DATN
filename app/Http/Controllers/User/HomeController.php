<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $discountedProducts = Product::whereHas('products', function($query) {
            $query->whereNotNull('discount_id');
        })->get();

        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('user.home')->with('products', $products)->with('discountedProducts', $discountedProducts);
    }
}
