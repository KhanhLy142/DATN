@extends('user.layouts.master')

@section('title', 'Chi tiết đơn hàng #123456')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/orders">Đơn hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết #123456</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <!-- Header đơn hàng -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-pink mb-2">Đơn hàng #123456</h2>
                <p class="text-muted mb-0">Đặt ngày 19/05/2025 lúc 14:30 • Mã vận đơn: DH123456VN</p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">Chờ xử lý</span>
                <div class="mt-2">
                    <button class="btn btn-outline-danger btn-sm me-2">Hủy đơn</button>
                    <button class="btn btn-pink btn-sm">Liên hệ hỗ trợ</button>
                </div>
            </div>
        </div>

        <!-- Tiến trình đơn hàng -->
        <div class="card shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-4">Tiến trình đơn hàng</h5>

            <div class="order-tracking">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="tracking-step active">
                            <div class="tracking-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <h6 class="mb-1">Đã đặt hàng</h6>
                            <small class="text-muted">19/05/2025 14:30</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="tracking-step">
                            <div class="tracking-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <h6 class="mb-1">Xác nhận</h6>
                            <small class="text-muted">Đang chờ</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="tracking-step">
                            <div class="tracking-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-truck"></i>
                            </div>
                            <h6 class="mb-1">Đang giao</h6>
                            <small class="text-muted">Chưa bắt đầu</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="tracking-step">
                            <div class="tracking-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-house-check"></i>
                            </div>
                            <h6 class="mb-1">Hoàn thành</h6>
                            <small class="text-muted">Chưa hoàn thành</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sản phẩm -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Sản phẩm đã đặt</h5>

                    <!-- Sản phẩm 1 -->
                    <div class="d-flex align-items-center border-bottom pb-4 mb-4">
                        <img src="{{ asset('images/product1.jpg') }}" alt="Sản phẩm" class="img-fluid rounded border me-3"
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Son dưỡng môi chống nắng SPF15</h6>
                            <p class="text-muted mb-1">Thương hiệu: Innisfree</p>
                            <p class="text-muted mb-1">Màu sắc: Hồng nhạt • Số lượng: 1</p>
                            <div class="d-flex align-items-center">
                                <span class="text-pink fw-bold me-2">120.000₫</span>
                                <small class="text-decoration-line-through text-muted">150.000₫</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-outline-pink btn-sm">Mua lại</button>
                        </div>
                    </div>

                    <!-- Sản phẩm 2 -->
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('images/product2.jpg') }}" alt="Sản phẩm" class="img-fluid rounded border me-3"
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Kem chống nắng SPF50+ PA+++</h6>
                            <p class="text-muted mb-1">Thương hiệu: The Ordinary</p>
                            <p class="text-muted mb-1">Dung tích: 50ml • Số lượng: 2</p>
                            <div class="d-flex align-items-center">
                                <span class="text-pink fw-bold">440.000₫</span>
                                <small class="text-muted ms-2">(220.000₫ x 2)</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-outline-pink btn-sm">Mua lại</button>
                        </div>
                    </div>
                </div>

                <!-- Thông tin giao hàng -->
                <div class="card shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Thông tin giao hàng</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Người nhận</h6>
                            <p class="mb-1"><strong>Nguyễn Văn A</strong></p>
                            <p class="mb-1">SĐT: 0123 456 789</p>
                            <p class="mb-0">Email: abc@gmail.com</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2">Địa chỉ giao hàng</h
