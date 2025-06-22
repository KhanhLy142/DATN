@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Tổng đơn hàng -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Tổng đơn hàng</h5>
                    <h3 class="fw-bold">152</h3> <!-- Giá trị tĩnh cho tổng đơn hàng -->
                    <p class="text-success mb-0"><i class="bi bi-arrow-up-right"></i> +12% so với tháng trước</p>
                </div>
            </div>
        </div>

        <!-- Tổng sản phẩm -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Tổng sản phẩm</h5>
                    <h3 class="fw-bold">68</h3> <!-- Giá trị tĩnh cho tổng sản phẩm -->
                    <p class="text-info mb-0"><i class="bi bi-box-seam"></i> Đang bán</p>
                </div>
            </div>
        </div>

        <!-- Tổng người dùng -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Người dùng</h5>
                    <h3 class="fw-bold">24</h3> <!-- Giá trị tĩnh cho số người dùng -->
                    <p class="text-primary mb-0"><i class="bi bi-people"></i> Hoạt động hôm nay</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tổng doanh thu -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Tổng doanh thu</h5>
                    <h3 class="fw-bold">₫ 500,000</h3> <!-- Giá trị tĩnh cho tổng doanh thu -->
                    <p class="text-warning mb-0"><i class="bi bi-currency-dollar"></i> Tăng 8% so với tháng trước</p>
                </div>
            </div>
        </div>

        <!-- Tổng giảm giá -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Giảm giá đã áp dụng</h5>
                    <h3 class="fw-bold">₫ 50,000</h3> <!-- Giá trị tĩnh cho giảm giá -->
                    <p class="text-danger mb-0"><i class="bi bi-percent"></i> Giảm giá tổng cộng</p>
                </div>
            </div>
        </div>
    </div>
@endsection
