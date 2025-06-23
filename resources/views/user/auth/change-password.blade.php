@extends('user.layouts.master')

@section('title', 'Đổi mật khẩu')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('account') }}">Tài khoản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đổi mật khẩu</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h6 class="mt-2 mb-0 fw-bold">{{ Auth::user()->name ?? 'Nguyễn Văn A' }}</h6>
                            <small class="text-muted">Thành viên từ {{ Auth::user()->created_at ? Auth::user()->created_at->format('m/Y') : '01/2024' }}</small>
                        </div>

                        <div class="list-group list-group-flush">
                            <a href="{{ route('account') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2">
                                <i class="bi bi-person-circle me-2"></i>
                                Thông tin cá nhân
                            </a>
                            <a href="{{ route('account.edit') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2">
                                <i class="bi bi-pencil-square me-2"></i>
                                Cập nhật thông tin
                            </a>
                            <a href="#" class="list-group-item list-group-item-action active border-0 rounded-3 mb-2">
                                <i class="bi bi-shield-lock me-2"></i>
                                Đổi mật khẩu
                            </a>
                            <a href="#" class="list-group-item list-group-item-action border-0 rounded-3 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Header -->
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold text-pink mb-1">
                                <i class="bi bi-shield-lock me-2"></i>
                                Đổi mật khẩu
                            </h4>
                            <p class="text-muted mb-0">Cập nhật mật khẩu để bảo vệ tài khoản của bạn</p>
                        </div>
                        <div>
                            <a href="{{ route('account') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form đổi mật khẩu -->
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <!-- Hiển thị thông báo -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
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

                    <form action="{{ route('change-password') }}" method="POST" id="change-password-form">
                        @csrf

                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="current_password" class="form-label fw-semibold">
                                        Mật khẩu hiện tại <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock text-muted"></i>
                                        </span>
                                        <input type="password" id="current_password" name="current_password"
                                               class="form-control border-start-0 border-end-0 @error('current_password') is-invalid @enderror"
                                               placeholder="Nhập mật khẩu hiện tại" required>
                                        <button type="button" class="btn btn-outline-secondary border-start-0 password-toggle"
                                                data-target="current_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Nhập mật khẩu bạn đang sử dụng để xác thực</small>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">
                                        Mật khẩu mới <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-key text-muted"></i>
                                        </span>
                                        <input type="password" id="password" name="password"
                                               class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                               placeholder="Nhập mật khẩu mới" required>
                                        <button type="button" class="btn btn-outline-secondary border-start-0 password-toggle"
                                                data-target="password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password strength indicator -->
                                    <div class="mt-2">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted" id="password-strength-text">Độ mạnh mật khẩu</small>
                                    </div>

                                    <small class="text-muted">Tối thiểu 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt</small>
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label fw-semibold">
                                        Xác nhận mật khẩu mới <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-check-square text-muted"></i>
                                        </span>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                               class="form-control border-start-0 border-end-0"
                                               placeholder="Nhập lại mật khẩu mới" required>
                                        <button type="button" class="btn btn-outline-secondary border-start-0 password-toggle"
                                                data-target="password_confirmation">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-feedback" class="mt-1"></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="resetForm()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Đặt lại
                                    </button>
                                    <button type="submit" class="btn btn-pink rounded-pill px-5" id="submit-btn">
                                        <i class="bi bi-shield-check me-2"></i>Đổi mật khẩu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Hướng dẫn bảo mật -->
                <div class="card shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-shield-check me-2 text-success"></i>
                        Hướng dẫn tạo mật khẩu mạnh
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill text-success me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Độ dài tối thiểu</h6>
                                    <small class="text-muted">Sử dụng ít nhất 8 ký tự, càng dài càng an toàn</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill text-success me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Kết hợp nhiều loại ký tự</h6>
                                    <small class="text-muted">Chữ hoa, chữ thường, số và ký tự đặc biệt</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-x-circle-fill text-danger me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Tránh thông tin cá nhân</h6>
                                    <small class="text-muted">Không sử dụng tên, ngày sinh, số điện thoại</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-x-circle-fill text-danger me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Tránh từ phổ biến</h6>
                                    <small class="text-muted">Không dùng "123456", "password", "qwerty"</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Lời khuyên:</strong> Sử dụng trình quản lý mật khẩu để tạo và lưu trữ mật khẩu mạnh một cách an toàn.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .list-group-item.active {
            background-color: var(--bs-pink);
            border-color: var(--bs-pink);
        }

        .input-group-text {
            min-width: 45px;
            justify-content: center;
        }

        .form-control:focus {
            border-color: #e91e63;
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }

        .password-toggle {
            cursor: pointer;
        }

        .password-toggle:hover {
            background-color: #f8f9fa;
        }

        #submit-btn {
            min-width: 160px;
            position: relative;
        }

        #submit-btn:disabled {
            opacity: 0.6;
        }

        .loading-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .progress-bar {
            transition: all 0.3s ease;
        }

        .form-control, .input-group-text {
            border-radius: 0.5rem;
        }

        .input-group .form-control:not(:first-child) {
            border-left: 0;
        }

        .input-group .form-control:not(:last-child) {
            border-right: 0;
        }
    </style>
@endpush
