<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | DaisyBeauty</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">DaisyBeauty</div>

    <div class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Dashboard
        </a>

        <!-- Quản lý danh mục -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-list-ul"></i> Quản lý danh mục</a>
            <div class="submenu">
                <a href="{{ route('admin.categories.index') }}">Danh sách danh mục</a>
                <a href="{{ route('admin.categories.create') }}">Thêm danh mục</a>
            </div>
        </div>

        <!-- Quản lý sản phẩm -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-bag"></i> Quản lý sản phẩm</a>
            <div class="submenu">
                <a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a>
                <a href="{{ route('admin.products.create') }}">Thêm sản phẩm</a>
                <a href="{{ route('admin.inventory.index') }}">Tồn kho</a>
            </div>
        </div>

        <!-- Quản lý thương hiệu -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-award"></i> Quản lý thương hiệu</a>
            <div class="submenu">
                <a href="{{ route('admin.brands.index') }}">Danh sách thương hiệu</a>
                <a href="{{ route('admin.brands.create') }}">Thêm thương hiệu</a>
            </div>
        </div>

        <!-- Quản lý nhà cung cấp -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-building"></i> Quản lý nhà cung cấp</a>
            <div class="submenu">
                <a href="{{ route('admin.suppliers.index') }}">Danh sách nhà cung cấp</a>
                <a href="{{ route('admin.suppliers.create') }}">Thêm nhà cung cấp</a>
            </div>
        </div>

        <!-- Quản lý đơn hàng -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-box-seam"></i> Quản lý đơn hàng</a>
            <div class="submenu">
                <a href="{{ route('admin.orders.index') }}">Danh sách đơn hàng</a>
                <a href="{{ route('admin.orders.create') }}">Tạo đơn hàng mới</a>
            </div>
        </div>

        <!-- Quản lý thanh toán -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-credit-card"></i> Quản lý thanh toán</a>
        </div>

        <!-- Quản lý vận chuyển -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-truck"></i> Quản lý vận chuyển</a>
        </div>

        <!-- Quản lý giảm giá -->
        <div class="sidebar-group hover-toggle">
            <a href="{{ route('admin.discounts.index') }}" class="has-submenu"><i class="bi bi-percent"></i> Quản lý giảm giá</a>
        </div>

        <!-- Quản lý khách hàng -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-person"></i> Quản lý khách hàng</a>
            <div class="submenu">
                <a href="{{ route('admin.customers.index') }}">Danh sách khách hàng</a>
                <a href="{{ route('admin.customers.create') }}">Thêm khách hàng</a>
            </div>
        </div>

        <!-- Quản lý đánh giá -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-chat-dots"></i> Quản lý đánh giá</a>
            <div class="submenu">
                <a href="{{ route('admin.reviews.index') }}">Danh sách đánh giá</a>
            </div>
        </div>

        <!-- Quản lý tài khoản -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-people"></i> Quản lý tài khoản</a>
            <div class="submenu">
                <a href="{{ route('admin.accounts.list') }}">Danh sách tài khoản</a>
                <a href="{{ route('admin.accounts.roles') }}">Phân quyền</a>
            </div>
        </div>

        <!-- Quản lý Chatbot -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-robot"></i> Quản lý Chatbot</a>
            <div class="submenu">
                <a href="{{ route('admin.chatbot.index') }}">Cấu hình Chatbot</a>
                <a href="#">Lịch sử hội thoại</a>
            </div>
        </div>

        <!-- Quản lý tồn kho -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-boxes"></i> Quản lý tồn kho</a>
            <div class="submenu">
                <a href="{{ route('admin.inventory.index') }}">Tổng quan tồn kho</a>
                <a href="{{ route('admin.inventory.create') }}">Nhập hàng</a>
                <a href="{{ route('admin.inventory.low-stock') }}">Sắp hết hàng</a>
                <a href="{{ route('admin.inventory.history') }}">Lịch sử nhập hàng</a>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="sidebar-group hover-toggle">
            <a href="#" class="has-submenu"><i class="bi bi-bar-chart"></i> Thống kê</a>
            <div class="submenu">
                <a href="#">Doanh thu</a>
                <a href="#">Sản phẩm bán chạy</a>
                <a href="#">Người dùng mới</a>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    @yield('content')
</div>

<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/admin-script.js') }}"></script>
</body>
</html>
