@extends('user.layouts.master')

@section('title', 'Chi tiết bài viết')

@section('content')
    <div class="container py-5">
        <!-- Tiêu đề và breadcrumb -->
        <div class="mb-4">
            <h2 class="text-center text-pink fw-bold">Chi tiết bài viết</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/blog') }}">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">5 Cách Giữ Ẩm Cho Da</li>
                </ol>
            </nav>
        </div>

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

            <!-- Nội dung chính -->
            <div class="col-md-8 order-md-2 order-1">
                <div class="mb-4">
                    <img src="{{ asset('images/blog/sample1.jpg') }}" class="img-fluid rounded shadow-sm mb-4"
                         alt="Ảnh bài viết">
                    <h3 class="fw-bold text-pink">5 Cách Giữ Ẩm Cho Da Mùa Hè</h3>
                    <p class="text-muted small">Đăng ngày: 17/05/2025 - Tác giả: Daisy Beauty Team</p>

                    <div class="content">
                        <p>
                            Mùa hè với ánh nắng gay gắt và nhiệt độ cao khiến làn da dễ bị mất nước, khô ráp. Dưới đây
                            là 5 cách đơn giản giúp bạn duy trì độ ẩm cho da một cách hiệu quả:
                        </p>
                        <ul>
                            <li>Uống đủ 2 lít nước mỗi ngày</li>
                            <li>Dùng serum cấp ẩm có chứa hyaluronic acid</li>
                            <li>Tránh rửa mặt bằng nước quá nóng</li>
                            <li>Luôn dùng kem chống nắng khi ra ngoài</li>
                            <li>Đắp mặt nạ dưỡng ẩm 2–3 lần/tuần</li>
                        </ul>
                        <p>
                            Ngoài ra, hãy lựa chọn mỹ phẩm dịu nhẹ, phù hợp với làn da để tránh kích ứng. Một làn da đủ
                            ẩm sẽ giúp bạn luôn rạng rỡ, tươi tắn dù nắng hè gay gắt!
                        </p>
                    </div>

                    <div class="mt-5">
                        <a href="{{ url('/blog') }}" class="btn btn-outline-secondary rounded-pill">← Quay lại blog</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
