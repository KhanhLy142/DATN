@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa nhân viên')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chỉnh sửa nhân viên</h4>
                    <div class="card-body">
                        <form action="{{ route('admin.staffs.update', $staff) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        Tên nhân viên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $staff->name) }}"
                                           placeholder="VD: Nguyễn Văn A"
                                           required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $staff->email) }}"
                                           placeholder="VD: nhanvien@daisybeauty.com"
                                           required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Mật khẩu mới
                                    </label>
                                    <div class="input-group @error('password') is-invalid @enderror">
                                        <input type="password"
                                               class="form-control"
                                               id="password"
                                               name="password"
                                               placeholder="Để trống nếu không đổi mật khẩu">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Hiển thị mật khẩu">
                                            <i class="bi bi-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Để trống nếu không muốn thay đổi mật khẩu
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Xác nhận mật khẩu mới
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Xác nhận mật khẩu mới">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" title="Hiển thị mật khẩu">
                                            <i class="bi bi-eye" id="eyeIconConfirm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $staff->phone) }}"
                                           placeholder="VD: 0123456789">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

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
                                            <option value="{{ $key }}"
                                                {{ old('role', $staff->role) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle"></i> Thông tin bổ sung:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>
                                            <strong>Mã nhân viên:</strong> NV-{{ str_pad($staff->id, 4, '0', STR_PAD_LEFT) }}<br>
                                            <strong>Ngày tạo:</strong> {{ $staff->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small>
                                            <strong>Cập nhật cuối:</strong> {{ $staff->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-pink" type="submit">Cập nhật</button>
                                <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeIcon.className = 'bi bi-eye-slash';
                    } else {
                        eyeIcon.className = 'bi bi-eye';
                    }
                });
            }

            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const eyeIconConfirm = document.getElementById('eyeIconConfirm');

            if (togglePasswordConfirm && passwordConfirmInput && eyeIconConfirm) {
                togglePasswordConfirm.addEventListener('click', function() {
                    const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirmInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeIconConfirm.className = 'bi bi-eye-slash';
                    } else {
                        eyeIconConfirm.className = 'bi bi-eye';
                    }
                });
            }
        });
    </script>
@endpush
