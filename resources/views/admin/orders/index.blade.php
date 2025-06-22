@extends('admin.layouts.master')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý đơn hàng</h4>
                    <div class="card-body">
                        {{-- Thông báo --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Form tìm kiếm và lọc -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Tìm kiếm đơn hàng..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="status" class="form-select">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                                Chờ xử lý
                                            </option>
                                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                                Đang xử lý
                                            </option>
                                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>
                                                Đã giao hàng
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Nút thêm mới -->
                        <div class="mb-3">
                            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                                Thêm mới
                            </a>
                        </div>

                        <!-- Bảng danh sách đơn hàng -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $index => $order)
                                    <tr>
                                        <td>{{ $orders->firstItem() + $index }}</td>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $order->customer->phone ?? '-' }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning">Chờ xử lý</span>
                                            @elseif($order->status == 'processing')
                                                <span class="badge bg-info">Đang xử lý</span>
                                            @elseif($order->status == 'shipped')
                                                <span class="badge bg-success">Đã giao hàng</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.orders.show', $order) }}"
                                                   class="btn btn-sm btn-outline-info" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.orders.edit', $order) }}"
                                                   class="btn btn-sm btn-outline-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if($order->status == 'pending')
                                                    <form action="{{ route('admin.orders.cancel', $order) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hủy">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            style="cursor: not-allowed;" title="Không thể hủy đơn hàng này"
                                                            disabled>
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <p>Không có đơn hàng nào được tìm thấy</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        @include('admin.layouts.pagination', ['paginator' => $orders, 'itemName' => 'đơn hàng'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
