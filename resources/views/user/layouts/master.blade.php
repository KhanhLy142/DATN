<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DaisyBeauty')</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    @stack('styles')
</head>
<body>

<header class="py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="navbar-brand fs-2 fw-bold text-dark">DaisyBeauty</a>

        <form action="{{ route('products.search') }}" method="GET" class="input-group w-50">
            <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm, danh mục, thương hiệu..." value="{{ request('q') }}">
            <button class="btn btn-pink" type="submit">
                <i class="bi bi-search icon-custom"></i>
            </button>
        </form>

        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="bi bi-person icon-custom"></i>
                <div class="account-dropdown">
                    @auth('customer')
                        <a href="/account">Tài khoản </a>
                        <a href="/orders">Đơn hàng</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item border-0 bg-transparent">Đăng xuất</button>
                        </form>
                    @else
                        <a href="/login">Đăng nhập</a>
                        <a href="/register">Đăng ký</a>
                    @endauth
                </div>
            </div>
            <div class="position-relative">
                <a href="/cart" class="text-decoration-none position-relative d-inline-block">
                    <i class="bi bi-cart3 icon-custom fs-4"></i>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle px-2 cart-count">0</span>
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
                @if(isset($menuCategories) && $menuCategories->count() > 0)
                    @foreach($menuCategories as $parentCategory)
                        <li class="dropdown-submenu position-relative">
                            <a class="dropdown-item d-flex justify-content-between align-items-center"
                               href="{{ route('category.show', $parentCategory->id) }}">
                                <span>{{ $parentCategory->name }}</span>
                                @if($parentCategory->children->count() > 0)
                                    <i class="bi bi-chevron-right"></i>
                                @endif
                            </a>

                            @if($parentCategory->children->count() > 0)
                                <ul class="dropdown-menu mega-menu">
                                    <div class="row">
                                        @php
                                            $childrenChunks = $parentCategory->children->chunk(ceil($parentCategory->children->count() / 3));
                                            $maxChunks = 3;
                                        @endphp

                                        @foreach($childrenChunks->take($maxChunks) as $chunkIndex => $chunk)
                                            <div class="col-md-4">
                                                @foreach($chunk as $childCategory)
                                                    <a class="dropdown-item"
                                                       href="{{ route('category.show', $childCategory->id) }}">
                                                        {{ $childCategory->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li>
                        <a class="dropdown-item text-muted" href="#">
                            <i class="bi bi-hourglass-split me-2"></i>
                            Đang tải danh mục...
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <ul class="nav">
            <li class="nav-item"><a class="nav-link text-white" href="/">Trang chủ</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/gioi-thieu">Giới thiệu</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/san-pham">Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/ai-chatbot">Chatbot AI</a></li>
        </ul>
    </div>
</nav>

@yield('banner')

<main>
    @yield('content')
</main>

<footer class="bg-light py-5">
    <div class="container">
        <div class="row gy-4 justify-content-center text-center text-md-start">
            <div class="col-lg-4 col-md-6 col-12">
                <h5 class="fw-semibold mb-3">Về DaisyBeauty</h5>
                <p class="text-muted lh-base">DaisyBeauty cung cấp mỹ phẩm chính hãng, an toàn và phù hợp với mọi loại da. Đồng hành cùng bạn trong hành trình làm đẹp mỗi ngày.</p>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <h5 class="fw-semibold mb-3">Liên hệ</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-geo-alt-fill me-2 text-pink"></i>
                        <span class="text-muted">123 Hoa Hồng, Q.1, TP.HCM</span>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone-fill me-2 text-pink"></i>
                        <span class="text-muted">0901 234 567</span>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-envelope-fill me-2 text-pink"></i>
                        <span class="text-muted">support@daisybeauty.vn</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12 col-12">
                <h5 class="fw-semibold mb-3">Hỗ trợ & Kết nối</h5>
                <div class="d-flex justify-content-center justify-content-md-start gap-3 mb-3">
                    <a href="#" class="text-decoration-none text-muted fs-3 social-icon" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-decoration-none text-muted fs-3 social-icon" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-decoration-none text-muted fs-3 social-icon" title="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
                <p class="text-muted small mb-0">Theo dõi chúng tôi để cập nhật những xu hướng làm đẹp mới nhất!</p>
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-12 text-center">
                <p class="text-muted mb-0 small">
                    © {{ date('Y') }} DaisyBeauty. Tất cả quyền được bảo lưu.
                </p>
            </div>
        </div>
    </div>
</footer>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show custom-notification" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show custom-notification" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show custom-notification" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/swiper-init.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>

@yield('scripts')
@stack('scripts')

</body>
</html>
