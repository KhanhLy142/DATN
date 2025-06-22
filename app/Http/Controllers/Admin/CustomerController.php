<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('orders');

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
            'email' => 'required|email|unique:customers,email',
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
            // Thông báo lỗi cho trường name
            'name.required' => 'Tên khách hàng là bắt buộc.',
            'name.string' => 'Tên khách hàng phải là chuỗi ký tự.',
            'name.max' => 'Tên khách hàng không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho trường email
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng bởi khách hàng khác.',

            // Thông báo lỗi cho trường password
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',

            // Thông báo lỗi cho trường phone
            'phone.regex' => 'Số điện thoại phải là 10-11 chữ số và chỉ chứa các ký tự số.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng bởi khách hàng khác.',

            // Thông báo lỗi cho trường address
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
        ]);

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được tạo thành công!');
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the customer
     */
    public function edit(Customer $customer)
    {
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
                Rule::unique('customers')->ignore($customer->id),
            ],
            'password' => [
                'nullable',
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
            // Thông báo lỗi cho trường name
            'name.required' => 'Tên khách hàng là bắt buộc.',
            'name.string' => 'Tên khách hàng phải là chuỗi ký tự.',
            'name.max' => 'Tên khách hàng không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho trường email
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng bởi khách hàng khác.',

            // Thông báo lỗi cho trường password
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',

            // Thông báo lỗi cho trường phone
            'phone.regex' => 'Số điện thoại phải là 10-11 chữ số và chỉ chứa các ký tự số.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng bởi khách hàng khác.',

            // Thông báo lỗi cho trường address
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Thông tin khách hàng đã được cập nhật!');
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

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được xóa thành công!');
    }
}
