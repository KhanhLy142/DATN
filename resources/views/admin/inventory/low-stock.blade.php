@extends('admin.layouts.master')

@section('title', 'Sản phẩm sắp hết hàng')

@section('content')
    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
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
                        <h4 class="fw-bold text-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Sản phẩm sắp hết hàng
                        </h4>
                        <small class="text-muted">Danh sách sản phẩm có số lượng tồn kho ≤ 10</small>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-warning">
                            <i class="bi bi-plus-circle me-2"></i>Nhập hàng ngay
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-exclamation-triangle display-6 mb-2"></i>
                                <h6 class="card-title">Sản phẩm sắp hết</h6>
                                <h3 class="mb-0">{{ $lowStockProducts->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-x-octagon display-6 mb-2"></i>
                                <h6 class="card-title">Cần nhập gấp</h6>
                                <h3 class="mb-0">{{ $lowStockProducts->where('quantity', '<=', 5)->count() }}</h3>
                                <small>≤ 5 sản phẩm</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-clock display-6 mb-2"></i>
                                <h6 class="card-title">Tổng giá trị</h6>
                                <h3 class="mb-0">{{ number_format($lowStockProducts->sum(function($item) { return $item->quantity * $item->product->base_price; })) }}₫</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-modern">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>SKU</th>
                            <th>Danh mục</th>
                            <th>Tồn kho</th>
                            <th>Mức cảnh báo</th>
                            <th>Giá bán</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($lowStockProducts as $index => $inventory)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $inventory->product->name }}</div>
                                        <small class="text-muted">{{ $inventory->product->brand->name ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $inventory->product->sku }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $inventory->product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($inventory->quantity <= 5)
                                        <span class="badge bg-danger fs-6">{{ $inventory->quantity }}</span>
                                        <br><small class="text-danger">Cần nhập gấp!</small>
                                    @else
                                        <span class="badge bg-warning fs-6">{{ $inventory->quantity }}</span>
                                        <br><small class="text-warning">Sắp hết</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-outline-warning text-dark">≤ 10</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">{{ number_format($inventory->product->base_price) }}₫</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.inventory.create') }}?product_id={{ $inventory->product->id }}"
                                       class="btn btn-outline-info btn-sm"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-plus-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-check-circle text-success display-4 mb-3"></i>
                                        <h5 class="text-success">Tuyệt vời!</h5>
                                        <p class="text-muted">Hiện tại không có sản phẩm nào sắp hết hàng</p>
                                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-success">
                                            <i class="bi bi-boxes me-2"></i>Quay lại quản lý tồn kho
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($lowStockProducts->count() > 0)
                    <div class="alert alert-warning mt-4">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-lightbulb me-2"></i>Gợi ý hành động:
                        </h6>
                        <ul class="mb-0">
                            <li>Ưu tiên nhập hàng các sản phẩm có số lượng ≤ 5</li>
                            <li>Liên hệ nhà cung cấp để đặt hàng thường xuyên</li>
                            <li>Cân nhắc tăng mức tồn kho tối thiểu cho sản phẩm bán chạy</li>
                            <li>Kiểm tra lại dự báo nhu cầu khách hàng</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.create') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-plus-circle text-success display-6 mb-2"></i>
                        <h6 class="card-title">Nhập hàng ngay</h6>
                        <p class="text-muted small mb-0">Bổ sung tồn kho cho các sản phẩm sắp hết</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.index') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-boxes text-primary display-6 mb-2"></i>
                        <h6 class="card-title">Quản lý tồn kho</h6>
                        <p class="text-muted small mb-0">Xem tổng quan tình trạng kho hàng</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.history') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-info display-6 mb-2"></i>
                        <h6 class="card-title">Lịch sử nhập hàng</h6>
                        <p class="text-muted small mb-0">Theo dõi các lần nhập hàng trước đây</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
