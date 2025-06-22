<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DaisyBeauty')</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>

<header class="py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="navbar-brand fs-2 fw-bold text-dark">DaisyBeauty</a>

        <div class="input-group w-50">
            <input type="text" class="form-control" placeholder="Enter your keyword...">
            <button class="btn btn-pink" type="button">
                <i class="bi bi-search icon-custom"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="bi bi-person icon-custom"></i>
                <div class="account-dropdown">
                    <a href="/login">Đăng nhập</a>
                    <a href="/register">Đăng ký</a>
                    <a href="/account">Tài khoản</a>
                    <a href="/orders">Đơn hàng</a>
                </div>
            </div>
            <div class="position-relative">
                <i class="bi bi-heart icon-custom"></i>
                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">0</span>
            </div>
            <div class="position-relative">
                <a href="/cart" class="text-decoration-none position-relative d-inline-block">
                    <i class="bi bi-cart3 icon-custom fs-4"></i>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle px-2">0</span>
                </a>

                <div class="cart-dropdown position-absolute bg-white shadow-sm rounded-3 p-3 mt-2 d-none" style="min-width: 200px; z-index: 1000;">
                    <p class="mb-1">0 sản phẩm</p>
                    <p class="mb-0">Phí ship: 0₫</p>
                </div>
            </div>
        </div>
    </div>
</header>

<nav class="bg-pink">
    <div class="container d-flex align-items-center">
        <div class="dropdown me-4 position-relative">
            <button class="btn btn-light dropdown-toggle" type="button" id="departmentsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('font/icons/list.svg') }}" alt="list" style="width:20px; height:20px;"> Danh mục
            </button>

            <ul class="dropdown-menu" aria-labelledby="departmentsDropdown">
                <li class="dropdown-submenu position-relative">
                    <a class="dropdown-item" href="#">Dưỡng Da</a>
                    <ul class="dropdown-menu mega-menu p-4">
                        <div class="row">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="col-md-4">
                                    <a class="dropdown-item fw-bold text-dark" href="#">Sữa rửa mặt</a>
                                    <a class="dropdown-item" href="#">Water Lipstick</a>
                                    <a class="dropdown-item" href="#">Wax Lipstick</a>
                                    <a class="dropdown-item" href="#">Blush</a>
                                </div>
                            @endfor
                        </div>
                    </ul>
                </li>
                <li><a class="dropdown-item" href="#">Trang điểm</a></li>
                <li><a class="dropdown-item" href="#">Chăm sóc tóc</a></li>
                <li><a class="dropdown-item" href="#">Nước hoa</a></li>
                <li><a class="dropdown-item" href="#">Phụ kiện làm đẹp</a></li>
            </ul>
        </div>

        <ul class="nav">
            <li class="nav-item"><a class="nav-link text-white" href="/">Trang chủ</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/gioi-thieu">Giới thiệu</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/san-pham">Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/ai-chatbot">Chatbot AI</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/contact">Liên hệ</a></li>
        </ul>
    </div>
</nav>

@yield('banner')

<main>
    @yield('content')
</main>

<footer class="bg-light py-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5 class="fw-semibold mb-3">Về DaisyBeauty</h5>
                <p>DaisyBeauty cung cấp mỹ phẩm chính hãng, an toàn và phù hợp với mọi loại da. Đồng hành cùng bạn trong hành trình làm đẹp mỗi ngày.</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-semibold mb-3">Liên hệ</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-pink"></i>123 Hoa Hồng, Q.1, TP.HCM</li>
                    <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-pink"></i>0901 234 567</li>
                    <li><i class="bi bi-envelope-fill me-2 text-pink"></i>support@daisybeauty.vn</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="fw-semibold mb-3">Hỗ trợ & Kết nối</h5>
                <div class="mb-3">
                    <a href="#" class="fs-4 me-3 social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="fs-4 me-3 social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="fs-4 social-icon"><i class="bi bi-tiktok"></i></a>
                </div>
                <form class="d-flex">
                    <input type="email" class="form-control me-2 rounded-custom" placeholder="Nhập email">
                    <button class="btn btn-pink rounded-custom px-3" type="submit">Đăng ký</button>
                </form>
            </div>
        </div>
    </div>
</footer>

<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/swiper-init.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>
