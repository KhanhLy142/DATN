<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerAuth
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('CustomerAuth check', [
            'url' => $request->url(),
            'customer_check' => Auth::guard('customer')->check(),
            'web_check' => Auth::guard('web')->check(),
            'customer_user' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->email : null,
            'web_user' => Auth::guard('web')->user() ? Auth::guard('web')->user()->email : null,
        ]);

        if (str_contains($request->path(), 'debug') || str_contains($request->path(), 'test-order')) {
            return $next($request);
        }

        $isAuthenticated = Auth::guard('customer')->check() || Auth::guard('web')->check();

        if (!$isAuthenticated) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        return $next($request);
    }
}
