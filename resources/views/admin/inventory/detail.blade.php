@extends('admin.layouts.master')

@section('title', 'Chi tiết phiếu nhập hàng')

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

        <div class="card shadow-sm rounded-4">
            <div class="card-header border-0 bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold text-pink mb-0">
                            <i class="bi bi-file-text me-2"></i>Chi tiết phiếu nhập hàng
                        </h4>
                        <small class="text-muted">
                            Mã: {{ $import->import_code ?? 'IMP' . str_pad($import->id, 6, '0', STR_PAD_LEFT) }}
                        </small>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.inventory.history') }}" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>In phiếu
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Thông tin phiếu nhập --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="bi bi-building me-2"></i>Thông tin nhà cung cấp
                                </h6>
                                <div class="mb-2">
                                    <strong>{{ $import->supplier->name }}</strong>
                                </div>
                                @if($import->supplier->email)
                                    <div class="mb-1">
                                        <i class="bi bi-envelope me-1"></i>
                                        {{ $import->supplier->email }}
                                    </div>
                                @endif
                                @if($import->supplier->phone)
                                    <div class="mb-1">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $import->supplier->phone }}
                                    </div>
                                @endif
                                @if($import->supplier->address)
                                    <div class="mb-1">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        {{ $import->supplier->address }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="bi bi-info-circle me-2"></i>Thông tin phiếu nhập
                                </h6>
                                <div class="mb-2">
                                    <strong>Ngày nhập:</strong> {{ $import->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Số loại sản phẩm:</strong> {{ $import->items->count() }}
                                </div>
                                <div class="mb-2">
                                    <strong>Tổng số lượng:</strong> {{ $import->items->sum('quantity') }} sản phẩm
                                </div>
                                <div class="mb-2">
                                    <strong>Tổng chi phí:</strong>
                                    <span class="text-success fw-bold">{{ number_format($import->total_cost) }} ₫</span>
                                </div>
                                @if($import->notes)
                                    <div class="mb-2">
                                        <strong>Ghi chú:</strong> {{ $import->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chi tiết sản phẩm --}}
                <div class="card border">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Chi tiết sản phẩm nhập
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Sản phẩm</th>
                                    <th>SKU</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($import->items as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                            @if($item->product->category)
                                                <small class="text-muted">{{ $item->product->category->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->product->sku }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6">{{ number_format($item->quantity) }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item->unit_price) }} ₫
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item->quantity * $item->unit_price) }} ₫
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="table-success">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">
                                        <i class="bi bi-calculator me-2"></i>Tổng cộng:
                                    </td>
                                    <td class="text-end fw-bold fs-5">
                                        {{ number_format($import->total_cost) }} ₫
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Thống kê phiếu nhập --}}
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <i class="bi bi-box text-primary display-6 mb-2"></i>
                                <h6 class="card-title">Tổng sản phẩm</h6>
                                <h4 class="text-primary mb-0">{{ $import->items->count() }} loại</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <i class="bi bi-boxes text-info display-6 mb-2"></i>
                                <h6 class="card-title">Tổng số lượng</h6>
                                <h4 class="text-info mb-0">{{ number_format($import->items->sum('quantity')) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <i class="bi bi-cash text-success display-6 mb-2"></i>
                                <h6 class="card-title">Giá trị trung bình</h6>
                                <h4 class="text-success mb-0">
                                    {{ number_format($import->total_cost / $import->items->sum('quantity')) }} ₫/sp
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Link nhanh --}}
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
                <a href="{{ route('admin.inventory.history') }}" class="card text-decoration-none hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-info display-6 mb-2"></i>
                        <h6 class="card-title">Lịch sử nhập hàng</h6>
                        <p class="text-muted small mb-0">Xem các lần nhập khác</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        @media print {
            .btn, .card:last-child .row {
                display: none !important;
            }
            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
