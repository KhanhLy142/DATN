<?php

use Illuminate\Support\Facades\Route;

// Import Admin Controllers
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;

/*
|--------------------------------------------------------------------------
| Trang người dùng (User)
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', function () {
    return view('user.home');
})->name('home');

// Trang giới thiệu
Route::get('/gioi-thieu', function () {
    return view('user.about');
})->name('about');

// Danh sách sản phẩm
Route::get('/san-pham', function () {
    return view('user.products.index');
})->name('products.index');

// Trang chi tiết sản phẩm
Route::get('/san-pham/{id}', function ($id) {
    return view('user.products.detail');
})->name('products.show');

Route::get('/ai-chatbot', function () {
    return view('user.ai-chatbot');
})->name('ai.chatbot');

Route::get('/contact', function () {
    return view('user.contact');
})->name('contact');

// Auth routes cho user
Route::get('/login', function () {
    return view('user.auth.login');
})->name('login');

Route::get('/register', function () {
    return view('user.auth.register');
})->name('register');

Route::get('/account', function () {
    return view('user.auth.account');
})->name('account');

Route::get('/account-edit', function () {
    return view('user.auth.account-edit');
})->name('account-edit');

// Cart và Order routes
Route::get('/order/cart', function () {
    return view('user.order.cart');
})->name('order.cart');

Route::get('/order/checkout', function () {
    return view('user.order.order-checkout');
})->name('order.checkout');

Route::get('/order/detail/{id}', function ($id) {
    return view('user.order.order-detail');
})->name('order.detail');

/*
|--------------------------------------------------------------------------
| API Routes cho Frontend
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('check-discount-code', [AdminDiscountController::class, 'checkCode'])->name('api.check-discount');

    // API Routes cho đánh giá (cho frontend)
    Route::prefix('reviews')->name('api.reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'apiIndex'])->name('index');
        Route::post('/', [AdminReviewController::class, 'store'])->name('store');
        Route::get('/product/{productId}', [AdminReviewController::class, 'getByProduct'])->name('by-product');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes - ĐÃ BỎ MIDDLEWARE TẠM THỜI ĐỂ TEST
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Trang Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');

    // ===== QUẢN LÝ TỒN KHO (ĐẶT TRƯỚC RESOURCES) =====
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [AdminInventoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminInventoryController::class, 'create'])->name('create');
        Route::post('/', [AdminInventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminInventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminInventoryController::class, 'update'])->name('update');
        Route::get('/low-stock', [AdminInventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('/history', [AdminInventoryController::class, 'importHistory'])->name('history');
        Route::get('/detail/{id}', [AdminInventoryController::class, 'importDetail'])->name('detail');
        Route::get('/api/products/supplier/{id}', [AdminInventoryController::class, 'getProductsBySupplier'])->name('api.products.by-supplier');
    });

    // ===== QUẢN LÝ MÃ GIẢM GIÁ (ĐẶT TRƯỚC RESOURCES) =====
    Route::prefix('discounts')->name('discounts.')->group(function () {
        // Routes đặc biệt trước resource routes
        Route::patch('/{discount}/toggle-status', [AdminDiscountController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/report', [AdminDiscountController::class, 'report'])->name('report');
        Route::get('/export', [AdminDiscountController::class, 'export'])->name('export');

        // API routes
        Route::get('/api/validate/{code}', [AdminDiscountController::class, 'validateCode'])->name('api.validate');
        Route::get('/api/applicable/{productId}', [AdminDiscountController::class, 'getApplicableDiscounts'])->name('api.applicable');
    });

    // ===== QUẢN LÝ THANH TOÁN (ĐẶT TRƯỚC RESOURCES) =====
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::patch('/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
        Route::patch('/{payment}/update-status', [AdminPaymentController::class, 'updateStatus'])->name('update-status');
        Route::get('/{payment}/receipt', [AdminPaymentController::class, 'receipt'])->name('receipt');
        Route::get('/export', [AdminPaymentController::class, 'export'])->name('export');
    });

    // ===== QUẢN LÝ VẬN CHUYỂN (ĐẶT TRƯỚC RESOURCES) =====
    Route::prefix('shippings')->name('shippings.')->group(function () {
        // Routes đặc biệt trước resource routes
        Route::patch('/{shipping}/mark-shipped', [AdminShippingController::class, 'markAsShipped'])->name('mark-shipped');
        Route::patch('/{shipping}/mark-delivered', [AdminShippingController::class, 'markAsDelivered'])->name('mark-delivered');
        Route::post('/{shipping}/generate-tracking', [AdminShippingController::class, 'generateTrackingCode'])->name('generate-tracking');
        Route::get('/statistics', [AdminShippingController::class, 'statistics'])->name('statistics');
        Route::get('/export', [AdminShippingController::class, 'export'])->name('export');

        // API routes
        Route::get('/api/order/{orderId}', [AdminShippingController::class, 'getByOrder'])->name('api.by-order');
    });

    // ===== QUẢN LÝ ĐÁNH GIÁ (ĐẶT TRƯỚC RESOURCES) =====
    Route::prefix('reviews')->name('reviews.')->group(function () {
        // Routes đặc biệt trước resource routes
        Route::get('/{id}/reply', [AdminReviewController::class, 'reply'])->name('reply');
        Route::post('/{id}/reply', [AdminReviewController::class, 'storeReply'])->name('store-reply');
        Route::patch('/{id}/toggle-status', [AdminReviewController::class, 'toggleStatus'])->name('toggle-status');
    });

    // ===== ROUTES CỤ THỂ TRƯỚC RESOURCES =====
    Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');

    // Route cho toggle status của staff
    Route::post('/staffs/{staff}/toggle-status', [AdminStaffController::class, 'toggleStatus'])->name('staffs.toggle-status');

    // ===== QUẢN LÝ NGƯỜI DÙNG =====
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/users/{id}', function ($id) {
        return view('admin.users.show');
    })->name('users.show');

    // ===== QUẢN LÝ CHATBOT =====
    Route::get('/chatbot', function () {
        return view('admin.chatbot.index');
    })->name('chatbot.index');

    // ===== QUẢN LÝ TÀI KHOẢN =====
    Route::get('/accounts', function () {
        return view('admin.accounts.list');
    })->name('accounts.list');

    Route::get('/accounts/{id}', function ($id) {
        return view('admin.accounts.show');
    })->name('accounts.show');

    Route::get('/accounts/{id}/edit', function ($id) {
        return view('admin.accounts.edit');
    })->name('accounts.edit');

    Route::get('/accounts/roles', function () {
        return view('admin.accounts.roles');
    })->name('accounts.roles');

    // ===== CRUD RESOURCES (ĐẶT CUỐI CÙNG) =====
    Route::resource('customers', AdminCustomerController::class);
    Route::resource('orders', AdminOrderController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('suppliers', AdminSupplierController::class);
    Route::resource('brands', AdminBrandController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('staffs', AdminStaffController::class);
    Route::resource('payments', AdminPaymentController::class);
    Route::resource('shippings', AdminShippingController::class);
    Route::resource('discounts', AdminDiscountController::class);
    Route::resource('reviews', AdminReviewController::class);
});
