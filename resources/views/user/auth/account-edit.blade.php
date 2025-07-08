@extends('user.layouts.master')
@section('title', 'Cập nhật thông tin tài khoản')
@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('account') }}">Tài khoản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cập nhật thông tin</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h6 class="mt-2 mb-0 fw-bold">{{ Auth::guard('customer')->user()->name }}</h6>
                            <small class="text-muted">Thành viên từ {{ Auth::guard('customer')->user()->created_at->format('m/Y') }}</small>
                        </div>

                        <div class="list-group list-group-flush">
                            <a href="{{ route('account') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2">
                                <i class="bi bi-person-circle me-2"></i>
                                Thông tin cá nhân
                            </a>
                            <a href="#" class="list-group-item list-group-item-action active border-0 rounded-3 mb-2">
                                <i class="bi bi-pencil-square me-2"></i>
                                Cập nhật thông tin
                            </a>
                            <a href="{{ route('change-password') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2">
                                <i class="bi bi-shield-lock me-2"></i>
                                Đổi mật khẩu
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="list-group-item list-group-item-action border-0 rounded-3 text-danger w-100 text-start"
                                        style="background: none; border: none;">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold text-pink mb-1">Cập nhật thông tin</h4>
                            <p class="text-muted mb-0">Thay đổi thông tin cá nhân của bạn</p>
                        </div>
                        <div>
                            <a href="{{ route('account') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                                <i class="bi bi-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm rounded-4 p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('account.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-person-badge me-2 text-pink"></i>
                                    Thông tin cơ bản
                                </h5>
                            </div>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" id="name" name="name"
                                           class="form-control border-start-0 @error('name') is-invalid @enderror"
                                           value="{{ old('name', $customer->name ?? $user->name) }}"
                                           placeholder="Nhập họ và tên của bạn" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" id="email" name="email"
                                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                                           value="{{ old('email', $customer->email ?? $user->email) }}"
                                           placeholder="Nhập email của bạn" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Số điện thoại
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-telephone text-muted"></i>
                                    </span>
                                    <input type="text" id="phone" name="phone"
                                           class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $customer->phone ?? '') }}"
                                           placeholder="Nhập số điện thoại">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Ví dụ: 0123456789</small>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    Địa chỉ
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-geo-alt text-muted"></i>
                                    </span>
                                    <textarea id="address" name="address" rows="3"
                                              class="form-control border-start-0 @error('address') is-invalid @enderror"
                                              placeholder="Nhập địa chỉ đầy đủ của bạn">{{ old('address', $customer->address ?? '') }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Ví dụ: 123 Đường ABC, Phường XYZ, Quận 1, TP.HCM</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Thông tin của bạn sẽ được bảo mật tuyệt đối
                                        </small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-3" onclick="resetForm()">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Đặt lại
                                        </button>
                                        <button type="submit" class="btn btn-pink rounded-pill px-5" id="submit-btn">
                                            <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

