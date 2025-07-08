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
    public function index(Request $request)
    {
        $query = Staff::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('role')) {
            $query->byRole($request->role);
        }

        $staffs = $query->paginate(10);
        $roles = Staff::getRoles();

        return view('admin.staffs.index', compact('staffs', 'roles'));
    }

    public function create()
    {
        $roles = Staff::getRoles();
        return view('admin.staffs.create', compact('roles'));
    }

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

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'user_type' => 'staff',
            ]);

            Staff::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
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

    public function show(Staff $staff)
    {
        $staff->load('user');
        return view('admin.staffs.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $staff->load('user');
        $roles = Staff::getRoles();
        return view('admin.staffs.edit', compact('staff', 'roles'));
    }

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
            'password' => 'nullable|string|min:8|confirmed',
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

            if ($request->filled('password')) {
                $hashedPassword = Hash::make($request->password);
                $userData['password'] = $hashedPassword;
                $staffData['password'] = $hashedPassword;
            }

            $staff->user->update($userData);

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

    public function destroy(Staff $staff)
    {
        try {
            DB::beginTransaction();

            $user = $staff->user;

            $staff->delete();

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

    public function toggleStatus(Staff $staff)
    {
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
