@extends('admin.layouts.master')

@section('title', 'Quản lý tồn kho')

@section('content')
    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-pink">Danh sách tồn kho</h2>
            </div>
        </div>

        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="syncInventory()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Đồng bộ tồn kho
                    </button>
                    <small class="text-muted ms-2">Click để đồng bộ dữ liệu tồn kho với sản phẩm</small>
                </div>

                <div class="filter-section mb-4">
                    <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control border-start-0 search-input"
                                       placeholder="Tìm kiếm sản phẩm, SKU..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select inventory-filter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>
                                    Còn hàng
                                </option>
                                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>
                                    Sắp hết
                                </option>
                                <option value="out_stock" {{ request('status') == 'out_stock' ? 'selected' : '' }}>
                                    Hết hàng
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel me-2"></i>Lọc
                            </button>
                        </div>
                    </form>
                </div>

                <div class="row mb-4 inventory-stats">
                    <div class="col-md-3">
                        <div class="card bg-success text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Còn hàng</h6>
                                <h3 class="mb-0">{{ $stats['in_stock'] ?? 0 }}</h3>
                                <small>Tồn kho > 10</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Sắp hết</h6>
                                <h3 class="mb-0">{{ $stats['low_stock'] ?? 0 }}</h3>
                                <small>Tồn kho 1-10</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Hết hàng</h6>
                                <h3 class="mb-0">{{ $stats['out_stock'] ?? 0 }}</h3>
                                <small>Tồn kho = 0</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Tổng sản phẩm</h6>
                                <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                <small>Tất cả sản phẩm</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-modern inventory-table">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên sản phẩm</th>
                            <th>SKU</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Ngày cập nhật</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($inventories as $index => $inventory)
                            <tr>
                                <td class="text-center">{{ $inventories->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inventory->product->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $inventory->product->sku }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $inventory->quantity }}</span>
                                </td>
                                <td class="text-center">
                                    @if($inventory->quantity > 10)
                                        <span class="badge bg-success status-badge status-in-stock">Còn hàng</span>
                                    @elseif($inventory->quantity > 0)
                                        <span class="badge bg-warning status-badge status-low-stock">Sắp hết</span>
                                    @else
                                        <span class="badge bg-danger status-badge status-out-stock">Hết hàng</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        {{ $inventory->updated_at->format('d/m/Y') }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam display-4 text-muted mb-3"></i>
                                        <p class="text-muted">Chưa có sản phẩm nào trong kho</p>
                                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-pink">
                                            <i class="bi bi-plus-circle me-2"></i>Nhập hàng đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @include('admin.layouts.pagination', ['paginator' => $inventories, 'itemName' => 'tồn kho'])
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.create') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-plus-circle text-success display-6 mb-2"></i>
                        <h6 class="card-title">Nhập hàng mới</h6>
                        <p class="text-muted small mb-0">Thêm sản phẩm vào kho</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.low-stock') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle text-warning display-6 mb-2"></i>
                        <h6 class="card-title">Sản phẩm sắp hết</h6>
                        <p class="text-muted small mb-0">Xem danh sách cần nhập thêm</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.history') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-info display-6 mb-2"></i>
                        <h6 class="card-title">Lịch sử nhập hàng</h6>
                        <p class="text-muted small mb-0">Xem các lần nhập trước</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <style>
        .inventory-table th,
        .inventory-table td {
            padding: 6px 10px !important;
            vertical-align: middle;
            font-size: 14px;
        }

        .inventory-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
        }

        .status-badge {
            font-size: 11px;
            padding: 4px 8px;
            min-width: 60px;
        }

        .badge {
            font-size: 11px;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>

@endsection

@section('scripts')
    <script>
        async function syncInventory() {
            if (!confirm('Bạn có chắc muốn đồng bộ dữ liệu tồn kho? Thao tác này có thể mất vài phút.')) {
                return;
            }

            const button = event.target;
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm me-2"></i>Đang đồng bộ...';
            button.disabled = true;

            try {
                const response = await fetch('/admin/inventory/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>
                ${result.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

                    document.querySelector('.container-fluid').prepend(alertDiv);

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Sync error:', error);
                alert('Có lỗi xảy ra khi đồng bộ: ' + error.message);
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }

        setInterval(async function() {
            try {
                const response = await fetch('/admin/inventory/stats');
                if (response.ok) {
                    const stats = await response.json();

                    document.querySelector('.stat-card:nth-child(1) h3').textContent = stats.in_stock || 0;
                    document.querySelector('.stat-card:nth-child(2) h3').textContent = stats.low_stock || 0;
                    document.querySelector('.stat-card:nth-child(3) h3').textContent = stats.out_stock || 0;
                    document.querySelector('.stat-card:nth-child(4) h3').textContent = stats.total || 0;
                }
            } catch (error) {
                console.log('Stats refresh failed:', error);
            }
        }, 30000);
    </script>
@endsection
