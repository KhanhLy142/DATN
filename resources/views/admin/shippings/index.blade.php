@extends('admin.layouts.master')

@section('title', 'Danh sách vận chuyển')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý vận chuyển</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter và Search -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.shippings.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm ID đơn hàng, địa chỉ, mã vận đơn..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="shipping_method" class="form-select">
                            <option value="">Tất cả phương thức</option>
                            <option value="standard" {{ request('shipping_method') === 'standard' ? 'selected' : '' }}>Giao hàng tiêu chuẩn</option>
                            <option value="express" {{ request('shipping_method') === 'express' ? 'selected' : '' }}>Giao hàng nhanh</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="shipping_status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('shipping_status') === 'pending' ? 'selected' : '' }}>Chờ giao hàng</option>
                            <option value="shipped" {{ request('shipping_status') === 'shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                            <option value="delivered" {{ request('shipping_status') === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('admin.shippings.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-clock text-warning fs-1"></i>
                        <h5 class="card-title mt-2">{{ $stats['pending'] ?? 0 }}</h5>
                        <p class="card-text">Chờ giao hàng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="bi bi-truck text-info fs-1"></i>
                        <h5 class="card-title mt-2">{{ $stats['shipped'] ?? 0 }}</h5>
                        <p class="card-text">Đang giao hàng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <h5 class="card-title mt-2">{{ $stats['delivered'] ?? 0 }}</h5>
                        <p class="card-text">Đã giao hàng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam text-secondary fs-1"></i>
                        <h5 class="card-title mt-2">{{ $shippings->total() }}</h5>
                        <p class="card-text">Tổng vận chuyển</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle shipping-table">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ID Đơn hàng</th>
                    <th>Địa chỉ giao hàng</th>
                    <th>Phương thức</th>
                    <th class="text-center">Trạng thái</th>
                    <th>Mã vận đơn</th>
                    <th>Ngày tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($shippings as $index => $shipping)
                    <tr>
                        <td>{{ $shippings->firstItem() + $index }}</td>
                        <td>
                            <strong>#{{ $shipping->order_id }}</strong>
                            @if($shipping->order && $shipping->order->customer_name)
                                <br><small class="text-muted">{{ $shipping->order->customer_name }}</small>
                            @endif
                        </td>
                        <td>
                            <span title="{{ $shipping->shipping_address }}">
                                {{ Str::limit($shipping->shipping_address, 40) }}
                            </span>
                            @if($shipping->province)
                                <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $shipping->province }}</small>
                            @endif
                        </td>
                        <td>
                            @switch($shipping->shipping_method)
                                @case('standard')
                                    <span class="badge bg-info">Tiêu chuẩn</span>
                                    @break
                                @case('express')
                                    <span class="badge bg-warning text-dark">Nhanh</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_method) }}</span>
                            @endswitch
                            @if($shipping->shipping_fee && $shipping->shipping_fee > 0)
                                <br><small class="text-success fw-semibold">{{ number_format($shipping->shipping_fee) }}đ</small>
                            @else
                                <br><small class="text-success">Miễn phí</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @switch($shipping->shipping_status)
                                @case('pending')
                                    <span class="badge bg-warning">Chờ giao hàng</span>
                                    @break
                                @case('shipped')
                                    <span class="badge bg-primary">Đang giao hàng</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ ucfirst($shipping->shipping_status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($shipping->tracking_code)
                                <span class="badge bg-light text-dark border fw-bold" style="font-family: 'Courier New', monospace;">
                                    {{ $shipping->tracking_code }}
                                </span>
                            @else
                                <span class="text-muted">Chưa có</span>
                            @endif
                        </td>
                        <td>
                            {{ $shipping->created_at->format('d/m/Y') }}
                            <br><small class="text-muted">{{ $shipping->created_at->format('H:i') }}</small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.shippings.show', $shipping->id) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($shipping->shipping_status !== 'delivered')
                                    <a href="{{ route('admin.shippings.edit', $shipping->id) }}"
                                       class="btn btn-outline-warning btn-sm"
                                       title="Cập nhật">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.shippings.destroy', $shipping->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá thông tin vận chuyển này không?')"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Không có thông tin vận chuyển nào</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            @include('admin.layouts.pagination', ['paginator' => $shippings, 'itemName' => 'vận chuyển'])
        </div>
    </div>

    <style>
        .shipping-table {
            margin-bottom: 0;
        }
        .shipping-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .shipping-table tr:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection
