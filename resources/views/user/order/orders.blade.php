@extends('user.layouts.master')

@section('title', 'Đơn hàng của tôi')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/account">Tài khoản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <h2 class="fw-bold mb-4 text-center text-pink">Đơn hàng của tôi</h2>

        <!-- Filter đơn hàng -->
        <div class="card shadow-sm rounded-4 p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="orderStatus" id="all" checked>
                        <label class="btn btn-outline-pink" for="all">Tất cả</label>

                        <input type="radio" class="btn-check" name="orderStatus" id="pending">
                        <label class="btn btn-outline-pink" for="pending">Chờ xử lý</label>

                        <input type="radio" class="btn-check" name="orderStatus" id="confirmed">
                        <label class="btn btn-outline-pink" for="confirmed">Đã xác nhận</label>

                        <input type="radio" class="btn-check" name="orderStatus" id="shipping">
                        <label class="btn btn-outline-pink" for="shipping">Đang giao</label>

                        <input type="radio" class="btn-check" name="orderStatus" id="completed">
                        <label class="btn btn-outline-pink" for="completed">Hoàn thành</label>

                        <input type="radio" class="btn-check" name="orderStatus" id="cancelled">
                        <label class="btn btn-outline-pink" for="cancelled">Đã hủy</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng...">
                        <button class="btn btn-pink" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách đơn hàng -->
        <div class="row g-4">
            <!-- Đơn hàng 1 -->
            <div class="col-12">
                <div class="card shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Đơn hàng #123456</h5>
                            <small class="text-muted">Đặt ngày: 19/05/2025 lúc 14:30</small>
                        </div>
                        <span class="badge bg-warning text-dark fs-6">Chờ xử lý</span>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <img src="{{ asset('images/product1.jpg') }}" class="img-fluid rounded" alt="Product">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Son dưỡng môi chống nắng</h6>
                            <p class="text-muted mb-1">Màu sắc: Hồng nhạt • Số lượng: 1</p>
                            <p class="text-muted mb-0">+ 1 sản phẩm khác</p>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1"><strong>Tổng tiền:</strong></p>
                            <p class="text-pink fw-bold fs-5 mb-0">590.000₫</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="/order-detail-123456" class="btn btn-pink btn-sm mb-2 w-100">Xem chi tiết</a>
                            <button class="btn btn-outline-danger btn-sm w-100">Hủy đơn</button>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-truck me-1"></i>
                                    Giao hàng tiêu chuẩn • Dự kiến: 22-24/05/2025
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">Thanh toán: COD</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng 2 -->
            <div class="col-12">
                <div class="card shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Đơn hàng #123455</h5>
                            <small class="text-muted">Đặt ngày: 15/05/2025 lúc 09:15</small>
                        </div>
                        <span class="badge bg-success fs-6">Hoàn thành</span>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <img src="{{ asset('images/product2.jpg') }}" class="img-fluid rounded" alt="Product">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Kem chống nắng SPF50+</h6>
                            <p class="text-muted mb-1">Dung tích: 50ml • Số lượng: 1</p>
                            <p class="mb-0">
                                <small class="text-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Đã giao thành công ngày 18/05/2025
                                </small>
                            </p>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1"><strong>Tổng tiền:</strong></p>
                            <p class="text-pink fw-bold fs-5 mb-0">250.000₫</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="/order-detail-123455" class="btn btn-pink btn-sm mb-2 w-100">Xem chi tiết</a>
                            <button class="btn btn-outline-pink btn-sm w-100">Mua lại</button>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-truck me-1"></i>
                                    Đã giao thành công
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-star me-1"></i>Đánh giá
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng 3 -->
            <div class="col-12">
                <div class="card shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Đơn hàng #123454</h5>
                            <small class="text-muted">Đặt ngày: 10/05/2025 lúc 16:45</small>
                        </div>
                        <span class="badge bg-info text-dark fs-6">Đang giao</span>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <img src="{{ asset('images/product3.jpg') }}" class="img-fluid rounded" alt="Product">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Serum Vitamin C</h6>
                            <p class="text-muted mb-1">Dung tích: 30ml • Số lượng: 2</p>
                            <p class="mb-0">
                                <small class="text-info">
                                    <i class="bi bi-truck me-1"></i>
                                    Đơn hàng đang được giao đến bạn
                                </small>
                            </p>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1"><strong>Tổng tiền:</strong></p>
                            <p class="text-pink fw-bold fs-5 mb-0">390.000₫</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="/order-detail-123454" class="btn btn-pink btn-sm mb-2 w-100">Theo dõi</a>
                            <button class="btn btn-outline-secondary btn-sm w-100">Liên hệ</button>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 75%"></div>
                                </div>
                                <small class="text-muted mt-1">Đang giao hàng • Dự kiến giao: 20/05/2025</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted">Thanh toán: Chuyển khoản</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phân trang -->
        <div class="text-center mt-5">
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Thống kê nhanh -->
        <div class="row mt-5">
            <div class="col-md-3">
                <div class="card text-center p-3 border-warning">
                    <h4 class="text-warning mb-1">2</h4>
                    <small class="text-muted">Chờ xử lý</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 border-info">
                    <h4 class="text-info mb-1">1</h4>
                    <small class="text-muted">Đang giao</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 border-success">
                    <h4 class="text-success mb-1">15</h4>
                    <small class="text-muted">Hoàn thành</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 border-danger">
                    <h4 class="text-danger mb-1">0</h4>
                    <small class="text-muted">Đã hủy</small>
                </div>
            </div>
        </div>
    </div>
@endsection
