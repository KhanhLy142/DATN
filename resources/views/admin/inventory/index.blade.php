@extends('admin.layouts.master')

@section('title', 'Quản lý tồn kho')

@section('content')
    <div class="container-fluid py-4">
        {{-- Hiển thị thông báo --}}
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

        <div class="card shadow-sm rounded-4">
            <div class="card-header border-0 bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold text-pink mb-0">
                            <i class="bi bi-boxes me-2"></i>Quản lý tồn kho
                        </h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-pink">
                            <i class="bi bi-plus-circle me-2"></i>Nhập hàng
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Bộ lọc nhanh --}}
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

                {{-- Thống kê nhanh --}}
                <div class="row mb-4 inventory-stats">
                    <div class="col-md-3">
                        <div class="card bg-success text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Còn hàng</h6>
                                <h3 class="mb-0">{{ $inventories->where('quantity', '>', 10)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Sắp hết</h6>
                                <h3 class="mb-0">{{ $inventories->whereBetween('quantity', [1, 10])->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Hết hàng</h6>
                                <h3 class="mb-0">{{ $inventories->where('quantity', 0)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white stat-card">
                            <div class="card-body">
                                <h6 class="card-title">Tổng sản phẩm</h6>
                                <h3 class="mb-0">{{ $inventories->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bảng tồn kho --}}
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
                            <tr class="{{ $inventory->quantity == 0 ? 'table-danger' : ($inventory->quantity <= 10 ? 'table-warning' : '') }}">
                                <td class="text-center">{{ $inventories->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inventory->product->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $inventory->product->sku }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="quantity-display fw-bold cursor-pointer"
                                          data-id="{{ $inventory->id }}"
                                          data-quantity="{{ $inventory->quantity }}"
                                          onclick="editQuantityInline(this)">
                                        {{ $inventory->quantity }}
                                    </span>
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

                {{-- Phân trang --}}
                @include('admin.layouts.pagination', ['paginator' => $inventories, 'itemName' => 'tồn kho'])
            </div>
        </div>

        {{-- Link nhanh --}}
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

    {{-- Modal chỉnh sửa nhanh số lượng --}}
    <div class="modal fade" id="quickEditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Điều chỉnh tồn kho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickEditForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Số lượng mới</label>
                            <input type="number" name="quantity" id="quickEditQuantity" class="form-control" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lý do điều chỉnh</label>
                            <select name="reason" class="form-select" required>
                                <option value="">-- Chọn lý do --</option>
                                <option value="Kiểm kê">Kiểm kê</option>
                                <option value="Hàng hỏng">Hàng hỏng</option>
                                <option value="Trả hàng">Trả hàng</option>
                                <option value="Sửa lỗi">Sửa lỗi nhập liệu</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-pink">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
