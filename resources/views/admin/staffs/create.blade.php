@extends('admin.layouts.master')

@section('title', 'Thêm nhân viên mới')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Thêm mới nhân viên</h4>
                    <div class="card-body">

                        <!-- DEBUG: Hiển thị lỗi validation -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- DEBUG: Hiển thị lỗi từ session -->
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <h6><i class="bi bi-exclamation-triangle"></i> Lỗi hệ thống:</h6>
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- DEBUG: Hiển thị thông báo thành công -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.staffs.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <!-- Tên nhân viên -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        Tên nhân viên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="VD: Nguyễn Văn A"
                                           required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="VD: nhanvien@daisybeauty.com"
                                           required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Mật khẩu -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Hiển thị mật khẩu">
                                            <i class="bi bi-eye" id="eyeIcon"></i>
                                        </button>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Xác nhận mật khẩu -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Xác nhận mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Nhập lại mật khẩu"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" title="Hiển thị mật khẩu">
                                            <i class="bi bi-eye" id="eyeIconConfirm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Số điện thoại -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           placeholder="VD: 0123456789">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Vai trò -->
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">
                                        Vai trò <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('role') is-invalid @enderror"
                                            id="role"
                                            name="role"
                                            required>
                                        <option value="">Chọn vai trò</option>
                                        @foreach($roles as $key => $value)
                                            <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Chọn vai trò phù hợp cho nhân viên
                                    </div>
                                </div>
                            </div>

                            <!-- Checkbox hiển thị -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           checked>
                                    <label class="form-check-label" for="is_active">
                                        Kích hoạt tài khoản ngay
                                    </label>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end">
                                <button class="btn btn-pink" type="submit">Lưu</button>
                                <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
