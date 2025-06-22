@extends('user.layouts.master')

@section('title', 'Blog làm đẹp')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar bên trái -->
            <div class="col-md-4 order-md-1 order-2">
                <div class="mb-4">
                    <h6 class="fw-bold text-pink">Danh mục</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="#" class="text-decoration-none text-dark">Chăm sóc da</a>
                        </li>
                        <li class="list-group-item"><a href="#" class="text-decoration-none text-dark">Trang điểm</a>
                        </li>
                        <li class="list-group-item"><a href="#" class="text-decoration-none text-dark">Dưỡng tóc</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h6 class="fw-bold text-pink">Bài viết nổi bật</h6>
                    <div class="mb-3 d-flex">
                        <img src="{{ asset('images/blog/sample1.jpg') }}" class="me-3 rounded" width="60" height="60"
                             alt="Bài viết 1">
                        <div>
                            <a href="#" class="text-dark fw-semibold small">5 Cách Giữ Ẩm Cho Da Mùa Hè</a>
                        </div>
                    </div>
                    <div class="mb-3 d-flex">
                        <img src="{{ asset('images/blog/sample2.jpg') }}" class="me-3 rounded" width="60" height="60"
                             alt="Bài viết 2">
                        <div>
                            <a href="#" class="text-dark fw-semibold small">Top 3 Loại Son Tint Đáng Mua</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách bài viết bên phải -->
            <div class="col-md-8 order-md-2 order-1">
                <!-- Bài viết mẫu 1 -->
                <div class="card mb-4 shadow-sm rounded-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{{ asset('images/blog/sample1.jpg') }}"
                                 class="img-fluid rounded-start h-100 object-fit-cover" alt="Bài viết 1">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title text-pink fw-semibold">5 Cách Giữ Ẩm Cho Da Mùa Hè</h5>
                                <p class="card-text small text-muted">Mùa hè khiến da mất nước nhanh chóng. Cùng khám
                                    phá những bí quyết đơn giản để làn da luôn mịn màng...</p>
                                <a href="#" class="btn btn-pink btn-sm rounded-pill">Đọc tiếp</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bài viết mẫu 2 -->
                <div class="card mb-4 shadow-sm rounded-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{{ asset('images/blog/sample2.jpg') }}"
                                 class="img-fluid rounded-start h-100 object-fit-cover" alt="Bài viết 2">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title text-pink fw-semibold">Top 3 Loại Son Tint Đáng Mua</h5>
                                <p class="card-text small text-muted">Son tint đang là xu hướng làm đẹp hot nhất hiện
                                    nay. Cùng khám phá 3 sản phẩm được yêu thích nhất!</p>
                                <a href="#" class="btn btn-pink btn-sm rounded-pill">Đọc tiếp</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
