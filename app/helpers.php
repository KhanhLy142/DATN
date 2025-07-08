<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('getStaffInfo')) {
    function getStaffInfo() {
        $staffInfo = session('staff_info');

        if (!$staffInfo) {
            $user = Auth::guard('staff')->user();
            if ($user && $user->staff) {
                $staffInfo = $user->staff;

                session(['staff_info' => $staffInfo]);
            }
        }

        return $staffInfo;
    }
}

if (!function_exists('getCustomerInfo')) {
    function getCustomerInfo() {
        $customerInfo = session('customer_info');

        if (!$customerInfo) {
            $user = Auth::guard('customer')->user();
            if ($user && $user->customer) {
                $customerInfo = $user->customer;
                session(['customer_info' => $customerInfo]);
            }
        }

        return $customerInfo;
    }
}

if (!function_exists('getStaffRole')) {
    function getStaffRole() {
        $staff = getStaffInfo();
        if ($staff && isset($staff->role)) {
            return $staff->role;
        }

        $user = Auth::guard('staff')->user();
        if ($user && $user->staff && isset($user->staff->role)) {
            return $user->staff->role;
        }

        return null;
    }
}


if (!function_exists('hasPermission')) {
    function hasPermission($permission) {
        $user = Auth::guard('staff')->user();

        if (!$user) {
            return false;
        }


        if (!$user->isStaff()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return checkRolePermission($user->getStaffRole(), $permission);
    }
}

if (!function_exists('checkRolePermission')) {
    function checkRolePermission($role, $permission) {
        $rolePermissions = [
            'sales' => [

                'view_orders', 'create_orders', 'edit_orders', 'process_orders',

                'view_customers', 'create_customers', 'edit_customers', 'delete_customers',

                'view_shippings', 'manage_shippings',

                'view_payments', 'process_payments',

            ],

            'warehouse' => [

                'view_categories', 'manage_categories',

                'view_products', 'create_products', 'edit_products', 'delete_products',

                'view_brands', 'manage_brands',

                'view_inventory', 'update_inventory',

                'view_suppliers', 'manage_suppliers',

            ],

            'cskh' => [

                'view_chats', 'manage_chats',

                'view_reviews', 'manage_reviews',

                'view_discounts', 'manage_discounts',

            ]
        ];

        return isset($rolePermissions[$role]) && in_array($permission, $rolePermissions[$role]);
    }
}

if (!function_exists('hasRole')) {
    function hasRole($role) {
        $user = Auth::guard('staff')->user();

        if (!$user) {
            return false;
        }

        if (!$user->isStaff()) {
            return false;
        }

        return $user->hasRole($role);
    }
}

if (!function_exists('getCurrentStaffUser')) {
    function getCurrentStaffUser() {
        $user = Auth::guard('staff')->user();

        if (!$user || !$user->isStaff()) {
            return null;
        }

        return $user;
    }
}

if (!function_exists('isCurrentUserAdmin')) {
    function isCurrentUserAdmin() {
        $user = getCurrentStaffUser();
        return $user ? $user->isAdmin() : false;
    }
}

if (!function_exists('getCurrentUserRole')) {
    function getCurrentUserRole() {
        $user = getCurrentStaffUser();
        return $user ? $user->getStaffRole() : null;
    }
}

if (!function_exists('getCurrentUserDisplayName')) {
    function getCurrentUserDisplayName() {
        $staffInfo = getStaffInfo();

        if ($staffInfo) {
            return $staffInfo->name ?? 'Admin User';
        }

        $user = getCurrentStaffUser();
        if ($user) {
            return $user->staff->name ?? $user->name ?? 'Admin User';
        }

        return 'Admin User';
    }
}

if (!function_exists('getLoginRedirectUrl')) {
    function getLoginRedirectUrl($user) {
        if ($user->isAdmin()) {
            return '/admin/statistics';
        }

        $role = $user->getStaffRole();

        $redirectPriorities = [
            'sales' => '/admin/orders',
            'warehouse' => '/admin/products',
            'cskh' => '/admin/reviews',
        ];

        return $redirectPriorities[$role] ?? '/admin/statistics';
    }
}

if (!function_exists('redirectAfterLogin')) {
    function redirectAfterLogin($user) {
        $url = getLoginRedirectUrl($user);
        return redirect($url);
    }
}
