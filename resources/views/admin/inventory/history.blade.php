@extends('admin.layouts.master')

@section('title', 'Lịch sử nhập hàng')

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

        <div class="card shadow-sm rounded-4">
            <div class="card-header border-0 bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold text-pink mb-0">
                            <i class="bi bi-clock-history me-2"></i>Lịch sử nhập hàng
                        </h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-pink">
                            <i class="bi bi-plus-circle me-2"></i>Nhập hàng mới
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Tổng phiếu nhập</h6>
                                <h3 class="mb-0">{{ $imports->total() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Tổng giá trị</h6>
                                <h3 class="mb-0">{{ number_format($imports->sum('total_cost')) }} ₫</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Nhập tháng này</h6>
                                <h3 class="mb-0">{{ $imports->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Nhà cung cấp</h6>
                                <h3 class="mb-0">{{ $imports->unique('supplier_id')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-modern">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Mã đơn nhập</th>
                            <th>Nhà cung cấp</th>
                            <th>Số sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Ngày nhập</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($imports as $index => $import)
                            <tr>
                                <td class="text-center">{{ $imports->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $import->import_code ?? 'IMP' . str_pad($import->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $import->supplier->name }}</div>
                                    @if($import->supplier->phone)
                                        <small class="text-muted">{{ $import->supplier->phone }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $import->items->count() }} loại</span>
                                    <br>
                                    <small class="text-muted">{{ $import->items->sum('quantity') }} sp</small>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($import->total_cost) }} ₫</span>
                                </td>
                                <td class="text-center">
                                    <div>{{ $import->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $import->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.inventory.detail', $import->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                        <p class="text-muted">Chưa có lần nhập hàng nào</p>
                                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-pink">
                                            <i class="bi bi-plus-circle me-2"></i>Tạo phiếu nhập đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @include('admin.layouts.pagination', ['paginator' => $imports, 'itemName' => 'lịch sử nhập'])
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.create') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-plus-circle text-success display-6 mb-2"></i>
                        <h6 class="card-title">Nhập hàng mới</h6>
                        <p class="text-muted small mb-0">Tạo phiếu nhập hàng mới</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.index') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-boxes text-primary display-6 mb-2"></i>
                        <h6 class="card-title">Quản lý tồn kho</h6>
                        <p class="text-muted small mb-0">Xem tình trạng tồn kho hiện tại</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.low-stock') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle text-warning display-6 mb-2"></i>
                        <h6 class="card-title">Sản phẩm sắp hết</h6>
                        <p class="text-muted small mb-0">Kiểm tra hàng cần nhập thêm</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
