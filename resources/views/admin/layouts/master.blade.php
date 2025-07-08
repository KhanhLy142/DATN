<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | DaisyBeauty</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
    @yield('styles')
    @stack('styles')

    <style>
        .sidebar-menu .dark-text {
            color: #333 !important;
        }
        .sidebar-menu .dark-text:hover {
            color: #000 !important;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%) !important;
            border: none !important;
            color: white !important;
            padding: 10px 15px !important;
            border-radius: 8px !important;
            font-size: 0.85em !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            width: 100% !important;
            box-shadow: 0 2px 8px rgba(238, 90, 90, 0.3) !important;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(238, 90, 90, 0.4) !important;
            color: white !important;
        }

        .logout-btn:active {
            transform: translateY(0) !important;
        }

        .logout-btn i {
            font-size: 1em;
        }

        /* Cải thiện user info */
        .user-info {
            background: rgba(255,255,255,0.12) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            border-radius: 12px !important;
            padding: 16px !important;
            margin: 15px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <div>DaisyBeauty</div>
    </div>

    @php
        $staffInfo = getStaffInfo();
        $currentUser = Auth::guard('staff')->user();
    @endphp

    @if($staffInfo || ($currentUser && $currentUser->staff))
        @php
            $staff = $staffInfo ?: $currentUser->staff;
            $userName = $staff->name ?? $currentUser->name ?? 'Admin';
            $userRole = $staff->role ?? 'admin';
            $userRoleName = $staff->role_name ?? 'Quản trị viên';
        @endphp

        <div class="user-info">
            <div class="mb-3">
                <div class="fw-bold" style="font-size: 1.1em; color: #2c3e50;">
                    {{ $userName }}
                </div>
                <div style="font-size: 0.85em; color: #34495e; margin-top: 5px;">
                    {{ $userRoleName }}
                </div>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="logout-btn d-flex align-items-center justify-content-center" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    @else
        <div class="user-info">
            <div class="mb-3">
                <div class="fw-bold" style="font-size: 1.1em; color: #2c3e50;">Admin User</div>
                <div style="font-size: 0.85em; color: #34495e; margin-top: 5px;">
                    Quản trị viên
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="logout-btn d-flex align-items-center justify-content-center" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    @endif

    <div class="sidebar-menu">
        @php
            $currentRole = getCurrentUserRole();
            $isAdmin = isCurrentUserAdmin();
        @endphp

        @if($isAdmin)
            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.statistics.index') }}" class="has-submenu">
                    <i class="bi bi-bar-chart"></i> Thống kê
                </a>
            </div>
            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.staffs.index') }}" class="has-submenu dark-text">
                    <i class="bi bi-person"></i> Quản lý nhân viên
                </a>
            </div>
            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.suppliers.index') }}" class="has-submenu">
                    <i class="bi bi-building"></i> Quản lý nhà cung cấp
                </a>
            </div>
        @endif

        @if($currentRole === 'sales' || $isAdmin)
            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.orders.index') }}" class="has-submenu dark-text">
                    <i class="bi bi-box-seam"></i> Quản lý đơn hàng
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.customers.index') }}" class="has-submenu dark-text">
                    <i class="bi bi-person"></i> Quản lý khách hàng
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.shippings.index') }}" class="has-submenu">
                    <i class="bi bi-truck"></i> Quản lý vận chuyển
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.payments.index') }}" class="has-submenu">
                    <i class="bi bi-credit-card"></i> Quản lý thanh toán
                </a>
            </div>
        @endif

        @if($currentRole === 'warehouse' || $isAdmin)
            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.categories.index') }}" class="has-submenu">
                    <i class="bi bi-list-ul"></i> Quản lý danh mục
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.products.index') }}" class="has-submenu dark-text">
                    <i class="bi bi-bag"></i> Quản lý sản phẩm
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.brands.index') }}" class="has-submenu">
                    <i class="bi bi-award"></i> Quản lý thương hiệu
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.inventory.index') }}" class="has-submenu">
                    <i class="bi bi-boxes"></i> Quản lý kho
                </a>
            </div>
        @endif

        @if($currentRole === 'cskh' || $isAdmin)
            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.chats.index') }}" class="has-submenu">
                    <i class="bi bi-robot"></i> Quản lý Chatbot
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.reviews.index') }}" class="has-submenu">
                    <i class="bi bi-chat-dots"></i> Quản lý đánh giá
                </a>
            </div>

            <div class="sidebar-group hover-toggle">
                <a href="{{ route('admin.discounts.index') }}" class="has-submenu">
                    <i class="bi bi-percent"></i> Quản lý giảm giá
                </a>
            </div>
        @endif
    </div>
</div>

<div class="main-content">
    @yield('content')
</div>

<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/admin-script.js') }}"></script>
@yield('scripts')
@stack('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert.classList.contains('fade')) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 5000);
        });
    });
</script>

</body>
</html>
