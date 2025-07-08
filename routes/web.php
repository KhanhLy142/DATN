<?php

use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\StatisticsController as AdminStatisticsController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ChatbotController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/gioi-thieu', function () {
    return view('user.about');
})->name('about');


Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');

Route::get('san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/tim-kiem', [ProductController::class, 'search'])->name('products.search');
Route::get('/san-pham-moi', [ProductController::class, 'newProducts'])->name('products.new');
Route::get('/san-pham-giam-gia', [ProductController::class, 'discountedProducts'])->name('products.discounted');
Route::get('/san-pham/{id}', [ProductController::class, 'show'])->name('products.show');


Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'updateQuantity'])->name('update');
    Route::post('/remove', [CartController::class, 'removeItem'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::post('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');


Route::get('/order/vnpay/return', [OrderController::class, 'vnpayReturn'])->name('order.vnpay.return');
Route::post('/order/vnpay/ipn', [OrderController::class, 'vnpayIPN'])->name('order.vnpay.ipn');


Route::middleware(['customer.auth'])->group(function () {
    Route::get('/account', [AuthController::class, 'showAccount'])->name('account');
    Route::get('/account/edit', [AuthController::class, 'editAccount'])->name('account.edit');
    Route::put('/account/update', [AuthController::class, 'updateAccount'])->name('account.update');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password.post');

    Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
    Route::post('/order/place', [OrderController::class, 'placeOrder'])->name('order.place');

    Route::prefix('order')->name('order.')->group(function () {
        Route::post('/calculate-shipping', [OrderController::class, 'calculateShipping'])->name('calculate-shipping');
        Route::get('/{orderId}/track-ghn', [OrderController::class, 'trackGHNOrder'])->name('track-ghn');
    });

    Route::get('/order/vnpay/{orderId}', [OrderController::class, 'vnpay'])->name('order.vnpay');

    Route::get('/order/success/{orderId}', [OrderController::class, 'success'])->name('order.success');
    Route::get('/order/bank-transfer/{orderId}', [OrderController::class, 'bankTransfer'])->name('order.bank-transfer');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{orderId}', [OrderController::class, 'show'])->name('show');
        Route::post('/{orderId}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{orderId}/bank-info', [OrderController::class, 'bankTransferInfo'])->name('bank-info');

        Route::get('/{orderId}/check-payment-status', [OrderController::class, 'checkPaymentStatus'])->name('check-payment-status');

        Route::post('/{orderId}/recheck-payment', [OrderController::class, 'recheckPayment'])->name('recheck-payment');
        Route::post('/{orderId}/resend-vnpay', [OrderController::class, 'resendVNPay'])->name('resend-vnpay');
        Route::post('/orders/{orderId}/recheck-payment', [OrderController::class, 'recheckPayment'])
            ->name('orders.recheck-payment');
    });

    Route::get('/ai-chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::post('/send', [ChatbotController::class, 'sendMessage'])->name('send');
        Route::post('/new', [ChatbotController::class, 'newChat'])->name('new');
        Route::get('/history', [ChatbotController::class, 'getChatHistory'])->name('history');
        Route::post('/close', [ChatbotController::class, 'closeChat'])->name('close');
        Route::get('/test', [ChatbotController::class, 'testGemini'])->name('test');
        Route::get('/user-info', [ChatbotController::class, 'getUserInfo'])->name('user-info');
        Route::get('/all-chats', [ChatbotController::class, 'getAllChats'])->name('all-chats');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\ReviewController::class, 'index'])->name('index');
        Route::get('/products/{product}/create', [App\Http\Controllers\User\ReviewController::class, 'create'])->name('create');
        Route::post('/products/{product}', [App\Http\Controllers\User\ReviewController::class, 'store'])->name('store');
        Route::get('/{review}', [App\Http\Controllers\User\ReviewController::class, 'show'])->name('show');
        Route::get('/{review}/edit', [App\Http\Controllers\User\ReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [App\Http\Controllers\User\ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [App\Http\Controllers\User\ReviewController::class, 'destroy'])->name('destroy');
    });
});


Route::get('/categories', function() {
    return \App\Models\Category::where('status', true)
        ->where('parent_id', null)
        ->with('children.children')
        ->orderBy('id', 'asc')
        ->get();
});

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/orders/{trackingNumber}/status', [OrderController::class, 'getOrderStatus'])->name('orders.status');

    Route::middleware(['auth'])->group(function () {
        Route::get('/orders/{orderId}/payment-status', [OrderController::class, 'getPaymentStatus'])->name('orders.payment-status');
    });

    Route::post('check-discount-code', [AdminDiscountController::class, 'checkCode'])->name('check-discount');
    Route::post('/product/variant-price', [ProductController::class, 'getVariantPrice'])->name('product.variant-price');

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'apiIndex'])->name('index');
        Route::post('/', [AdminReviewController::class, 'store'])->name('store');
        Route::get('/product/{productId}', [AdminReviewController::class, 'getByProduct'])->name('by-product');
    });
});



Route::prefix('admin')->name('admin.')->group(function () {


    Route::get('/', function () {
        $user = Auth::guard('staff')->user();
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.statistics.index');
        }

        return redirect(getLoginRedirectUrl($user));
    })->name('dashboard');

    Route::get('/dashboard', function () {
        $user = Auth::guard('staff')->user();
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.statistics.index');
        }
        return redirect(getLoginRedirectUrl($user));
    })->name('dashboard.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::middleware(['auth:staff'])->group(function () {
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/statistics', [AdminStatisticsController::class, 'index'])->name('statistics.index');
            Route::get('/statistics/api', [AdminStatisticsController::class, 'apiData'])->name('statistics.api');
        });

        Route::middleware(['role:sales,admin'])->group(function () {
            Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        });

        Route::middleware(['role:sales,admin'])->group(function () {
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
            Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
            Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/orders/{order}/mark-delivered', [AdminOrderController::class, 'markAsDelivered'])->name('orders.mark-delivered');
            Route::post('/orders/{order}/force-status', [AdminOrderController::class, 'forceUpdateStatus'])->name('orders.force-status');
            Route::post('/orders/{order}/sync-status', [AdminOrderController::class, 'syncOrderStatusManually'])->name('orders.sync-status');
        });

        Route::middleware(['role:sales,admin'])->group(function () {
            Route::get('/shippings', [AdminShippingController::class, 'index'])->name('shippings.index');
            Route::get('/shippings/{shipping}', [AdminShippingController::class, 'show'])->name('shippings.show');
            Route::get('/shippings/{shipping}/edit', [AdminShippingController::class, 'edit'])->name('shippings.edit');
            Route::put('/shippings/{shipping}', [AdminShippingController::class, 'update'])->name('shippings.update');
            Route::get('/shippings/statistics', [AdminShippingController::class, 'statistics'])->name('shippings.statistics');
            Route::get('/shippings/api/order/{orderId}', [AdminShippingController::class, 'getByOrder'])->name('shippings.api.by-order');
            Route::post('/shippings/{shipping}/mark-shipped', [AdminShippingController::class, 'markAsShipped'])->name('shippings.mark-shipped');
            Route::post('/shippings/{shipping}/mark-delivered', [AdminShippingController::class, 'markAsDelivered'])->name('shippings.mark-delivered');
            Route::post('/shippings/{shipping}/generate-tracking', [AdminShippingController::class, 'generateTrackingCode'])->name('shippings.generate-tracking');
            Route::get('/shippings/{shipping}/check-payment', [AdminShippingController::class, 'checkPaymentStatus'])->name('shippings.check-payment');
        });

        Route::middleware(['role:sales,admin'])->group(function () {
            Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
            Route::get('/payments/{payment}/edit', [AdminPaymentController::class, 'edit'])->name('payments.edit');
            Route::put('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');
            Route::get('/payments/{payment}/receipt', [AdminPaymentController::class, 'receipt'])->name('payments.receipt');
            Route::patch('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');
            Route::patch('/payments/{payment}/update-status', [AdminPaymentController::class, 'updateStatus'])->name('payments.update-status');
            Route::post('/payments/{payment}/confirm-bank-transfer', [AdminPaymentController::class, 'confirmBankTransfer'])->name('payments.confirm-bank-transfer');
            Route::post('/payments/{payment}/confirm-vnpay', [AdminPaymentController::class, 'confirmVNPay'])->name('payments.confirm-vnpay');
            Route::post('/payments/bulk-confirm', [AdminPaymentController::class, 'bulkConfirm'])->name('payments.bulk-confirm');
            Route::post('/payments/{payment}/verify-external', [AdminPaymentController::class, 'verifyExternalPayment'])->name('payments.verify-external');
        });

        Route::middleware(['role:warehouse,admin'])->group(function () {
            Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}', [AdminCategoryController::class, 'show'])->name('categories.show');
            Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
        });

        Route::middleware(['role:warehouse,admin'])->group(function () {
            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
            Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        });

        Route::middleware(['role:warehouse,admin'])->group(function () {
            Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
            Route::get('/brands/create', [AdminBrandController::class, 'create'])->name('brands.create');
            Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
            Route::get('/brands/{brand}', [AdminBrandController::class, 'show'])->name('brands.show');
            Route::get('/brands/{brand}/edit', [AdminBrandController::class, 'edit'])->name('brands.edit');
            Route::put('/brands/{brand}', [AdminBrandController::class, 'update'])->name('brands.update');
            Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');
            Route::post('/brands/{brand}/toggle-status', [AdminBrandController::class, 'toggleStatus'])->name('brands.toggle-status');
        });

        Route::middleware(['role:warehouse,admin'])->group(function () {
            Route::prefix('inventory')->name('inventory.')->group(function () {
                Route::get('/', [AdminInventoryController::class, 'index'])->name('index');
                Route::get('/low-stock', [AdminInventoryController::class, 'lowStock'])->name('low-stock');
                Route::get('/history', [AdminInventoryController::class, 'importHistory'])->name('history');
                Route::get('/detail/{id}', [AdminInventoryController::class, 'importDetail'])->name('detail');
                Route::get('/stats', [AdminInventoryController::class, 'getStats'])->name('stats');
                Route::get('/products/{product}/variants', [AdminInventoryController::class, 'getProductVariants'])->name('products.variants');
                Route::get('/variants/{variant}/stock', [AdminInventoryController::class, 'checkVariantStock'])->name('variants.stock');
                Route::get('/create', [AdminInventoryController::class, 'create'])->name('create');
                Route::get('/api/products-by-supplier/{supplierId}', [AdminInventoryController::class, 'getProductsBySupplier'])->name('api.products-by-supplier');
                Route::get('/api/brands-by-supplier/{supplierId}', [AdminInventoryController::class, 'getBrandsBySupplier'])->name('api.brands-by-supplier');
                Route::post('/', [AdminInventoryController::class, 'store'])->name('store');
                Route::post('/sync', [AdminInventoryController::class, 'syncInventory'])->name('sync');
            });
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/suppliers', [AdminSupplierController::class, 'index'])->name('suppliers.index');
            Route::get('/suppliers/create', [AdminSupplierController::class, 'create'])->name('suppliers.create');
            Route::post('/suppliers', [AdminSupplierController::class, 'store'])->name('suppliers.store');
            Route::get('/suppliers/{supplier}', [AdminSupplierController::class, 'show'])->name('suppliers.show');
            Route::get('/suppliers/{supplier}/edit', [AdminSupplierController::class, 'edit'])->name('suppliers.edit');
            Route::put('/suppliers/{supplier}', [AdminSupplierController::class, 'update'])->name('suppliers.update');
            Route::delete('/suppliers/{supplier}', [AdminSupplierController::class, 'destroy'])->name('suppliers.destroy');
            Route::post('/suppliers/{supplier}/toggle-status', [AdminSupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
        });

        Route::middleware(['role:cskh,admin'])->group(function () {
            Route::get('/chats', [AdminChatController::class, 'index'])->name('chats.index');
            Route::get('/chats/analytics', [AdminChatController::class, 'analytics'])->name('chats.analytics');
            Route::get('/chats/{id}', [AdminChatController::class, 'show'])->name('chats.show');
            Route::get('/chats/{id}/messages', [AdminChatController::class, 'getMessages'])->name('chats.get-messages');
            Route::post('/chats/send-message', [AdminChatController::class, 'sendMessage'])->name('chats.send-message');
            Route::post('/chats/{id}/close', [AdminChatController::class, 'closeChat'])->name('chats.close');
            Route::delete('/chats/{id}', [AdminChatController::class, 'destroy'])->name('chats.destroy');
            Route::get('/chats/export/training', [AdminChatController::class, 'exportTrainingData'])->name('chats.export-training-data');
        });

        Route::middleware(['role:cskh,admin'])->group(function () {
            Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
            Route::get('/reviews/{id}', [AdminReviewController::class, 'show'])->name('reviews.show');
            Route::get('/reviews/{id}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');
            Route::post('/reviews/{id}/reply', [AdminReviewController::class, 'storeReply'])->name('reviews.store-reply');
            Route::patch('/reviews/{id}/toggle-status', [AdminReviewController::class, 'toggleStatus'])->name('reviews.toggle-status');
            Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
        });

        Route::middleware(['role:cskh,admin'])->group(function () {
            Route::get('/discounts', [AdminDiscountController::class, 'index'])->name('discounts.index');
            Route::get('/discounts/create', [AdminDiscountController::class, 'create'])->name('discounts.create');
            Route::post('/discounts', [AdminDiscountController::class, 'store'])->name('discounts.store');
            Route::get('/discounts/{discount}', [AdminDiscountController::class, 'show'])->name('discounts.show');
            Route::get('/discounts/report', [AdminDiscountController::class, 'report'])->name('discounts.report');
            Route::get('/discounts/{discount}/edit', [AdminDiscountController::class, 'edit'])->name('discounts.edit');
            Route::put('/discounts/{discount}', [AdminDiscountController::class, 'update'])->name('discounts.update');
            Route::delete('/discounts/{discount}', [AdminDiscountController::class, 'destroy'])->name('discounts.destroy');
            Route::patch('/discounts/{discount}/toggle-status', [AdminDiscountController::class, 'toggleStatus'])->name('discounts.toggle-status');
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/staffs', [AdminStaffController::class, 'index'])->name('staffs.index');
            Route::get('/staffs/create', [AdminStaffController::class, 'create'])->name('staffs.create');
            Route::post('/staffs', [AdminStaffController::class, 'store'])->name('staffs.store');
            Route::get('/staffs/{staff}', [AdminStaffController::class, 'show'])->name('staffs.show');
            Route::get('/staffs/{staff}/edit', [AdminStaffController::class, 'edit'])->name('staffs.edit');
            Route::put('/staffs/{staff}', [AdminStaffController::class, 'update'])->name('staffs.update');
            Route::post('/staffs/{staff}/toggle-status', [AdminStaffController::class, 'toggleStatus'])->name('staffs.toggle-status');
            Route::delete('/staffs/{staff}', [AdminStaffController::class, 'destroy'])->name('staffs.destroy');
        });

    });

});

