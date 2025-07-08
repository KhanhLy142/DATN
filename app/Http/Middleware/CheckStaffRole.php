<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('staff')->user();

        if (!$user) {
            return redirect('/login')->withErrors(['error' => 'Vui lòng đăng nhập']);
        }

        if (!$user->isStaff()) {
            abort(403, 'Bạn không có quyền truy cập trang quản trị');
        }

        $staffRole = $user->getStaffRole();

        if ($staffRole === 'admin') {
            return $next($request);
        }

        if (in_array($staffRole, $roles)) {
            return $next($request);
        }

        $redirectUrl = $this->getSmartRedirectUrl($user);
        return redirect($redirectUrl)
            ->with('warning', "Chức năng này chỉ dành cho: " . implode(', ', $roles) . ". Đã chuyển bạn đến trang phù hợp.");
    }

    private function getSmartRedirectUrl($user): string
    {
        $role = $user->getStaffRole();

        $redirectUrls = [
            'sales' => '/admin/orders',
            'warehouse' => '/admin/products',
            'cskh' => '/admin/reviews',
        ];

        return $redirectUrls[$role] ?? '/admin/statistics';
    }
}
