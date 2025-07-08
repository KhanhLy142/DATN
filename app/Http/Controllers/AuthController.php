<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('=== LOGIN FORM SUBMITTED ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'email' => $request->input('email'),
            'has_password' => $request->filled('password'),
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        Auth::guard('customer')->logout();
        Auth::guard('staff')->logout();
        Auth::guard('web')->logout();
        session()->forget(['staff_info', 'customer_info']);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại.'])->withInput();
        }

        \Log::info('User found', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'has_customer' => $user->customer ? true : false,
            'has_staff' => $user->staff ? true : false,
        ]);

        if ($user->isStaff()) {
            \Log::info('Processing staff login');

            if (!$user->staff) {
                return back()->withErrors(['email' => 'Tài khoản nhân viên không tồn tại.'])->withInput();
            }

            if (Auth::guard('staff')->attempt($credentials, $remember)) {
                \Log::info('Staff authentication successful');
                $request->session()->regenerate();

                session(['staff_info' => $user->staff]);

                $redirectUrl = $this->getStaffRedirectUrl($user->staff->role);
                \Log::info('Staff redirecting to: ' . $redirectUrl);

                return redirect()->intended($redirectUrl)
                    ->with('success', 'Chào mừng ' . $user->staff->name);
            } else {
                \Log::error('Staff authentication failed');
                return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.'])->withInput();
            }
        }

        if ($user->isCustomer()) {
            \Log::info('Processing customer login');

            if (!$user->customer) {
                return back()->withErrors(['email' => 'Tài khoản khách hàng không tồn tại.'])->withInput();
            }

            if (Auth::guard('customer')->attempt($credentials, $remember)) {
                \Log::info('Customer authentication successful');
                $request->session()->regenerate();

                $sessionData = [
                    'id' => $user->customer->id,
                    'user_id' => $user->id,
                    'name' => $user->customer->name,
                    'email' => $user->customer->email,
                    'phone' => $user->customer->phone,
                    'address' => $user->customer->address,
                    'created_at' => $user->customer->created_at,
                    'updated_at' => $user->customer->updated_at,
                ];

                session(['customer_info' => $sessionData]);

                return redirect('/')
                    ->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->customer->name);
            } else {
                \Log::error('Customer authentication failed');
                return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.'])->withInput();
            }
        }

        \Log::error('User type not recognized', ['user_type' => $user->user_type]);
        return back()->withErrors(['email' => 'Loại tài khoản không được hỗ trợ.'])->withInput();
    }

    private function getStaffRedirectUrl($role)
{
    $roleRedirects = [
        'admin' => '/admin/statistics',
        'sales' => '/admin/orders',
        'warehouse' => '/admin/products',
        'cskh' => '/admin/reviews',
    ];

    return $roleRedirects[$role] ?? '/admin';
}

    public function logout(Request $request)
    {
        \Log::info('=== LOGOUT ATTEMPT ===');

        $wasStaff = Auth::guard('staff')->check();
        $wasCustomer = Auth::guard('customer')->check();

        Auth::guard('customer')->logout();
        Auth::guard('staff')->logout();
        Auth::guard('web')->logout();

        session()->forget(['staff_info', 'customer_info']);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($wasStaff) {
            return redirect('/login')->with('success', 'Đã đăng xuất khỏi hệ thống quản lý');
        } else {
            return redirect('/')->with('success', 'Đã đăng xuất thành công');
        }
    }

    public function showRegister()
    {
        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'customer',
        ]);

        $user->customer()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        Auth::guard('customer')->login($user);
        session(['customer_info' => $user->customer]);

        return redirect('/')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với DaisyBeauty.');
    }

    public function checkAuth()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['authenticated' => false]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => $user,
            'user_type' => $user->user_type,
            'role' => $user->getUserRole(),
        ]);
    }

    public function showAccount()
    {
        $user = Auth::guard('customer')->user();
        $customer = $user ? $user->customer : null;

        return view('user.auth.account', compact('user', 'customer'));
    }

    public function editAccount()
    {
        $user = Auth::guard('customer')->user();
        $customer = $user ? $user->customer : null;

        return view('user.auth.account-edit', compact('user', 'customer'));
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->customer) {
            $user->customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }

        return redirect()->route('account')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function showChangePassword()
    {
        return view('user.auth.change-password');
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('account')->with('success', 'Đổi mật khẩu thành công!');
    }
}
