<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
    }

    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return view('category.show', compact('category'));
    }
}
