<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
        Route::aliasMiddleware('customer.auth', \App\Http\Middleware\CustomerAuth::class);
        Route::aliasMiddleware('role', \App\Http\Middleware\CheckStaffRole::class);

        View::composer('*', function ($view) {
            if (!request()->is('admin/*')) {
                try {
                    if (class_exists('\App\Helpers\CategoryHelper')) {
                        $view->with('menuCategories', \App\Helpers\CategoryHelper::getMenuCategories());
                    } else {
                        $view->with('menuCategories', collect([]));
                    }
                } catch (\Exception $e) {
                    $view->with('menuCategories', collect([]));
                    \Log::error('Error loading menu categories: ' . $e->getMessage());
                }
            }
        });

        View::composer('*', function ($view) {
            $customer = Auth::guard('customer')->user();
            $staff = Auth::guard('staff')->user();

            $view->with([
                'currentCustomer' => $customer,
                'currentStaff' => $staff,
                'isCustomerAuth' => Auth::guard('customer')->check(),
                'isStaffAuth' => Auth::guard('staff')->check()
            ]);
        });
    }
}
