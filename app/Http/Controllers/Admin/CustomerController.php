<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::with('user')->withCount('orders');

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Kiểm tra unique ở bảng users
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'phone' => [
                'nullable',
                'regex:/^[0-9]{10,11}$/',
                'unique:customers,phone'
            ],
            'address' => 'nullable|string|max:500'
        ], [
            'name.required' => 'Tên khách hàng là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
            'phone.regex' => 'Số điện thoại phải là 10-11 chữ số.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Tạo User với password (CHỈ lưu password ở đây)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // Password chỉ ở users
            ]);

            // 2. Tạo Customer KHÔNG có password
            Customer::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                // BỎ password vì bảng customers không có cột này
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            DB::commit();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Khách hàng đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load(['user', 'orders' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the customer
     */
    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($customer->user_id),
            ],
            'password' => [
                'nullable', // Không bắt buộc khi update
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'phone' => [
                'nullable',
                'regex:/^[0-9]{10,11}$/',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'address' => 'nullable|string|max:500'
        ], [
            'name.required' => 'Tên khách hàng là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
            'phone.regex' => 'Số điện thoại phải là 10-11 chữ số.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Cập nhật User (password chỉ ở đây)
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            // Nếu có password mới, cập nhật vào User
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $customer->user->update($userData);

            // 2. Cập nhật Customer (KHÔNG có password)
            $customer->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            DB::commit();

            return redirect()->route('admin.customers.show', $customer)
                ->with('success', 'Thông tin khách hàng đã được cập nhật!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Không thể xóa khách hàng có đơn hàng!');
        }

        try {
            DB::beginTransaction();

            $user = $customer->user;

            // Xóa customer trước
            $customer->delete();

            // Xóa user sau
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Khách hàng đã được xóa thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.customers.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Reset customer password
     */
    public function resetPassword(Request $request, Customer $customer)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
        ], [
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
        ]);

        try {
            // Cập nhật password trong bảng users
            $customer->user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('admin.customers.show', $customer)
                ->with('success', 'Đặt lại mật khẩu thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Get customer password info (for debugging)
     */
    public function getPasswordInfo(Customer $customer)
    {
        return response()->json([
            'has_user' => $customer->user !== null,
            'user_id' => $customer->user_id,
            'password_exists' => $customer->user && !empty($customer->user->password),
            'password_hash' => $customer->user ? substr($customer->user->password, 0, 20) . '...' : null,
        ]);
    }
}
