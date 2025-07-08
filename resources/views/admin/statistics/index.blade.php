@extends('admin.layouts.master')

@section('title', 'Thống kê')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-header bg-white p-3 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-primary mb-2">
                                <i class="bi bi-bar-chart me-2"></i>Thống Kê Tổng Quan
                            </h2>
                            <p class="text-muted mb-0">Báo cáo và phân tích dữ liệu kinh doanh theo tuần</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" id="autoRefresh">
                                <label class="form-check-label" for="autoRefresh">
                                    <i class="bi bi-arrow-repeat"></i> Auto-refresh
                                </label>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                Cập nhật: <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('admin.statistics.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label small">Từ ngày</label>
                                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                                       value="{{ request('start_date', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label small">Đến ngày</label>
                                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                                       value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="period" class="form-label small">Chu kỳ</label>
                                <select class="form-select form-select-sm" id="period" name="period">
                                    <option value="daily" {{ request('period', 'weekly') == 'daily' ? 'selected' : '' }}>Theo ngày</option>
                                    <option value="weekly" {{ request('period', 'weekly') == 'weekly' ? 'selected' : '' }}>Theo tuần</option>
                                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Theo tháng</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.statistics.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body text-center p-3">
                        <div class="stat-icon bg-success text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-currency-dollar fs-5"></i>
                        </div>
                        <h5 class="fw-bold text-success revenue-amount mb-1">₫ {{ number_format($stats['revenue']['current']) }}</h5>
                        <p class="text-muted mb-1 small">Doanh thu {{ request('period') == 'daily' ? 'hôm nay' : 'tuần này' }}</p>
                        <small class="{{ $stats['revenue']['growth'] >= 0 ? 'text-success' : 'text-danger' }} growth-indicator">
                            <i class="bi bi-arrow-{{ $stats['revenue']['growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ $stats['revenue']['growth'] >= 0 ? '+' : '' }}{{ $stats['revenue']['growth'] }}% so với {{ request('period') == 'daily' ? 'hôm qua' : 'tuần trước' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body text-center p-3">
                        <div class="stat-icon bg-primary text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-box-seam fs-5"></i>
                        </div>
                        <h5 class="fw-bold text-primary orders-count mb-1">{{ $stats['orders']['current'] }}</h5>
                        <p class="text-muted mb-1 small">Đơn hàng {{ request('period') == 'daily' ? 'hôm nay' : 'tuần này' }}</p>
                        <small class="{{ $stats['orders']['growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi bi-arrow-{{ $stats['orders']['growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ $stats['orders']['growth'] >= 0 ? '+' : '' }}{{ $stats['orders']['growth'] }}% so với {{ request('period') == 'daily' ? 'hôm qua' : 'tuần trước' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body text-center p-3">
                        <div class="stat-icon bg-info text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-bag fs-5"></i>
                        </div>
                        <h5 class="fw-bold text-info products-count mb-1">{{ $stats['products'] }}</h5>
                        <p class="text-muted mb-1 small">Tổng sản phẩm</p>
                        <small class="text-info">
                            <i class="bi bi-check-circle"></i> Đang bán
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body text-center p-3">
                        <div class="stat-icon bg-warning text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-people fs-5"></i>
                        </div>
                        <h5 class="fw-bold text-warning customers-count mb-1">{{ $stats['customers']['total'] }}</h5>
                        <p class="text-muted mb-1 small">Tổng khách hàng</p>
                        <small class="text-success">
                            <i class="bi bi-person-plus"></i>
                            {{ isset($stats['customers']['new_this_week']) ? $stats['customers']['new_this_week'] : ($stats['customers']['new_today'] ?? 0) }}
                            mới {{ request('period') == 'daily' ? 'hôm nay' : 'tuần này' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2">
                        <h6 class="card-title fw-bold mb-0">
                            <i class="bi bi-graph-up text-primary me-2"></i>
                            Doanh thu {{ request('period') == 'daily' ? '7 ngày gần đây' : 'theo tuần' }}
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <canvas id="revenueChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2">
                        <h6 class="card-title fw-bold mb-0">
                            <i class="bi bi-star text-warning me-2"></i>
                            Top sản phẩm bán chạy
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="list-group list-group-flush" id="topProductsList">
                            @forelse($topProducts as $product)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 small">{{ $product->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($product->description, 25) }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $product->total_sold }} bán</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <p class="text-muted small">Chưa có dữ liệu bán hàng</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2">
                        <h6 class="card-title fw-bold mb-0">
                            <i class="bi bi-pie-chart text-info me-2"></i>
                            Doanh thu theo danh mục
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2">
                        <h6 class="card-title fw-bold mb-0">
                            <i class="bi bi-clipboard-data text-success me-2"></i>
                            Trạng thái đơn hàng
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row text-center" id="orderStatusGrid">
                            <div class="col-6 mb-2">
                                <div class="p-2 bg-light rounded">
                                    <h6 class="text-success fw-bold mb-1">{{ $orderStats['completed'] ?? 0 }}</h6>
                                    <small class="text-muted">Hoàn thành</small>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="p-2 bg-light rounded">
                                    <h6 class="text-warning fw-bold mb-1">{{ $orderStats['processing'] ?? 0 }}</h6>
                                    <small class="text-muted">Đang xử lý</small>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="p-2 bg-light rounded">
                                    <h6 class="text-info fw-bold mb-1">{{ $orderStats['shipping'] ?? 0 }}</h6>
                                    <small class="text-muted">Đang giao</small>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="p-2 bg-light rounded">
                                    <h6 class="text-danger fw-bold mb-1">{{ $orderStats['cancelled'] ?? 0 }}</h6>
                                    <small class="text-muted">Đã hủy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2">
                        <h6 class="card-title fw-bold mb-0">
                            <i class="bi bi-table text-primary me-2"></i>
                            Thống kê theo {{ request('period') == 'daily' ? 'ngày' : 'tuần' }}
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                <tr>
                                    <th class="small">{{ request('period') == 'daily' ? 'Ngày' : 'Tuần' }}</th>
                                    <th class="small">Doanh thu</th>
                                    <th class="small">Đơn hàng</th>
                                    <th class="small">SP bán</th>
                                    <th class="small">KH mới</th>
                                    <th class="small">Tăng trưởng</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($weeklyStats as $period)
                                    <tr>
                                        <td><strong class="small">{{ $period['week'] ?? $period['day'] ?? $period['month'] }}</strong></td>
                                        <td class="text-success fw-bold small">₫ {{ number_format($period['revenue']) }}</td>
                                        <td class="small">{{ $period['orders'] }}</td>
                                        <td class="small">{{ $period['products_sold'] ?? 0 }}</td>
                                        <td class="small">{{ $period['new_customers'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $period['growth'] >= 0 ? 'success' : 'danger' }} small">
                                                {{ $period['growth'] >= 0 ? '+' : '' }}{{ $period['growth'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center small">Chưa có dữ liệu</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Compact styling */
        .stat-icon {
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(0,0,0,0.1) !important;
        }

        .list-group-item {
            transition: background-color 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .table-responsive {
            border-radius: 8px;
        }

        .badge {
            font-size: 10px;
            padding: 3px 6px;
        }

        /* Compact spacing */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #e91e63;
            box-shadow: 0 0 0 0.1rem rgba(233, 30, 99, 0.25);
        }

        .form-check-input:checked {
            background-color: #e91e63;
            border-color: #e91e63;
        }

        .form-check-input:focus {
            border-color: #e91e63;
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }

        /* Loading animations */
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Remove extra spacing */
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let revenueChart, categoryChart;

        function formatCurrency(value) {
            if (value >= 1000000) {
                return (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
                return (value / 1000).toFixed(0) + 'K';
            } else {
                return value.toFixed(0);
            }
        }

        function formatTooltip(value) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(value);
        }

        function initRevenueChart() {
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const chartData = @json($chartData['data']);
            const maxValue = Math.max(...chartData);

            revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: chartData,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#0d6efd',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Doanh thu: ' + formatTooltip(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        function initCategoryChart() {
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');

            @if($categoryStats->count() > 0)
                categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($categoryStats->pluck('name')),
                    datasets: [{
                        data: @json($categoryStats->pluck('revenue')),
                        backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 10, usePointStyle: true, font: { size: 11 } }
                        }
                    }
                }
            });
            @else
                categoryCtx.fillStyle = '#6c757d';
            categoryCtx.font = '14px Arial';
            categoryCtx.textAlign = 'center';
            categoryCtx.fillText('Chưa có dữ liệu', categoryCtx.canvas.width/2, categoryCtx.canvas.height/2);
            @endif
        }

        document.addEventListener('DOMContentLoaded', function() {
            initRevenueChart();
            initCategoryChart();

            const periodSelect = document.getElementById('period');
            if (periodSelect) {
                periodSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    </script>
@endsection
