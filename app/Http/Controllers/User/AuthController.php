<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // ===== AUTHENTICATION METHODS =====

    public function showLogin()
    {
        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/account');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Mật khẩu không khớp',
        ]);

        try {
            DB::beginTransaction();

            // 1. Tạo User (password CHỈ lưu ở đây)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Tạo Customer (KHÔNG có password)
            Customer::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            DB::commit();

            Auth::login($user);
            return redirect()->route('account')->with('success', 'Đăng ký thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ===== ACCOUNT MANAGEMENT METHODS =====

    /**
     * Xem thông tin tài khoản
     */
    public function showAccount()
    {
        $user = Auth::user();
        $customer = $user->customer;

        return view('user.auth.account', compact('user', 'customer'));
    }

    /**
     * Form chỉnh sửa thông tin
     */
    public function editAccount()
    {
        $user = Auth::user();
        $customer = $user->customer;

        return view('user.auth.account-edit', compact('user', 'customer'));
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function updateAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.unique' => 'Email đã tồn tại',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // 1. Cập nhật bảng Users (chỉ name và email)
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // 2. Cập nhật hoặc tạo Customer
            $customer = $user->customer;
            if ($customer) {
                // Cập nhật customer hiện có
                $customer->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            } else {
                // Tạo customer mới nếu chưa có
                Customer::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            }

            DB::commit();

            return redirect()->route('account')->with('success', 'Cập nhật thông tin thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    // ===== PASSWORD MANAGEMENT METHODS =====

    /**
     * Form đổi mật khẩu
     */
    public function showChangePassword()
    {
        return view('user.auth.change-password');
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống',
            'password.required' => 'Mật khẩu mới không được để trống',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Mật khẩu mới không khớp',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không đúng'
            ]);
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('change-password')->with('success', 'Đổi mật khẩu thành công!');
    }
}
