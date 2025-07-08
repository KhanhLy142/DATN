@extends('user.layouts.master')

@section('title', 'Thông tin tài khoản')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tài khoản của tôi</li>
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
                            <a href="#" class="list-group-item list-group-item-action active border-0 rounded-3 mb-2">
                                <i class="bi bi-person-circle me-2"></i>
                                Thông tin cá nhân
                            </a>
                            <a href="{{ route('change-password') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2">
                                <i class="bi bi-shield-check me-2"></i>
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
                            <h4 class="fw-bold text-pink mb-1">Thông tin tài khoản</h4>
                            <p class="text-muted mb-0">Quản lý thông tin cá nhân của bạn</p>
                        </div>
                        <a href="{{ route('account.edit') }}" class="btn btn-pink rounded-pill px-4">
                            <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-person-badge me-2 text-pink"></i>
                        Thông tin cá nhân
                    </h5>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label text-muted small">Họ và tên</label>
                                <p class="fw-semibold mb-0">{{ Auth::guard('customer')->user()->customer->name ?? Auth::guard('customer')->user()->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label text-muted small">Email</label>
                                <p class="fw-semibold mb-0">{{ Auth::guard('customer')->user()->customer->email ?? Auth::guard('customer')->user()->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label text-muted small">Số điện thoại</label>
                                <p class="fw-semibold mb-0">{{ $customer && $customer->phone ? $customer->phone : 'Chưa cập nhật' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label text-muted small">Ngày tham gia</label>
                                <p class="fw-semibold mb-0">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item">
                                <label class="form-label text-muted small">Địa chỉ</label>
                                <p class="fw-semibold mb-0">{{ $customer && $customer->address ? $customer->address : 'Chưa cập nhật địa chỉ' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


