<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource - THÊM METHOD NÀY
     */
    public function index(Request $request)
    {
        $query = Staff::with('user')->orderBy('created_at', 'desc');

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Lọc theo role
        if ($request->filled('role')) {
            $query->byRole($request->role);
        }

        $staffs = $query->paginate(10);
        $roles = Staff::getRoles();

        return view('admin.staffs.index', compact('staffs', 'roles'));
    }

    /**
     * Show the form for creating a new resource - THÊM METHOD NÀY
     */
    public function create()
    {
        $roles = Staff::getRoles();
        return view('admin.staffs.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,sales,warehouse,cskh',
        ], [
            'name.required' => 'Tên nhân viên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role.required' => 'Vai trò là bắt buộc',
        ]);

        try {
            DB::beginTransaction();

            $hashedPassword = Hash::make($request->password);

            // 1. Tạo User với password
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword, // Lưu password ở users
            ]);

            // 2. Tạo Staff với cùng password (đồng bộ)
            Staff::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword, // Lưu password ở staffs (đồng bộ)
                'phone' => $request->phone,
                'role' => $request->role,
            ]);

            DB::commit();

            return redirect()->route('admin.staffs.index')
                ->with('success', 'Thêm nhân viên thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo nhân viên: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource - THÊM METHOD NÀY
     */
    public function show(Staff $staff)
    {
        $staff->load('user');
        return view('admin.staffs.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource - THÊM METHOD NÀY
     */
    public function edit(Staff $staff)
    {
        $staff->load('user');
        $roles = Staff::getRoles();
        return view('admin.staffs.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($staff->user_id),
            ],
            'password' => 'nullable|string|min:8|confirmed', // Không bắt buộc khi update
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,sales,warehouse,cskh',
        ], [
            'name.required' => 'Tên nhân viên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role.required' => 'Vai trò là bắt buộc',
        ]);

        try {
            DB::beginTransaction();

            // Chuẩn bị data để update
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            $staffData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
            ];

            // Nếu có password mới, hash và cập nhật cả 2 bảng
            if ($request->filled('password')) {
                $hashedPassword = Hash::make($request->password);
                $userData['password'] = $hashedPassword;
                $staffData['password'] = $hashedPassword; // Đồng bộ password
            }

            // 1. Cập nhật User
            $staff->user->update($userData);

            // 2. Cập nhật Staff
            $staff->update($staffData);

            DB::commit();

            return redirect()->route('admin.staffs.index')
                ->with('success', 'Cập nhật nhân viên thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật nhân viên: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage - THÊM METHOD NÀY
     */
    public function destroy(Staff $staff)
    {
        try {
            DB::beginTransaction();

            $user = $staff->user;

            // Xóa Staff trước
            $staff->delete();

            // Xóa User sau
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.staffs.index')
                ->with('success', 'Xóa nhân viên thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.staffs.index')
                ->with('error', 'Có lỗi xảy ra khi xóa nhân viên: ' . $e->getMessage());
        }
    }

    /**
     * Toggle staff status - THÊM METHOD NÀY
     */
    public function toggleStatus(Staff $staff)
    {
        // Có thể thêm logic để toggle trạng thái hoạt động
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
