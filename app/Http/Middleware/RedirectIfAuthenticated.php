<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->isStaff()) {
                    return redirect($this->getStaffHomePage($user));
                }
                elseif ($user->isCustomer()) {
                    return redirect('/');
                }
                else {
                    return redirect('/');
                }
            }
        }

        return $next($request);
    }

    private function getStaffHomePage($user): string
    {
        $role = $user->getStaffRole();

        $roleRedirects = [
            'admin' => '/admin/statistics',
            'sales' => '/admin/orders',
            'warehouse' => '/admin/products',
            'cskh' => '/admin/reviews',
        ];

        return $roleRedirects[$role] ?? '/admin/orders';
    }
}
