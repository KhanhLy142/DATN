@extends('admin.layouts.master')

@section('title', 'Danh sách vận chuyển')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách vận chuyển</h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.shippings.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search"
                               value="{{ request('search') }}" placeholder="Tìm kiếm ID đơn hàng, địa chỉ, mã vận đơn...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="shipping_method">
                            <option value="">Tất cả phương thức</option>
                            <option value="standard" {{ request('shipping_method') == 'standard' ? 'selected' : '' }}>Tiêu chuẩn</option>
                            <option value="express" {{ request('shipping_method') == 'express' ? 'selected' : '' }}>Nhanh</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="shipping_status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('shipping_status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('shipping_status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="shipping" {{ request('shipping_status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                            <option value="delivered" {{ request('shipping_status') == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                            <option value="failed" {{ request('shipping_status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                            <option value="returned" {{ request('shipping_status') == 'returned' ? 'selected' : '' }}>Trả lại</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                            <a href="{{ route('admin.shippings.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ID Đơn hàng</th>
                    <th>Địa chỉ giao hàng</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Mã vận đơn</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($shippings as $shipping)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="fw-bold">#{{ $shipping->order_id }}</span>
                            @if($shipping->order && $shipping->order->customer)
                                <br><small class="text-muted">{{ $shipping->order->customer->name }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;">
                                {{ $shipping->shipping_address }}
                            </div>
                        </td>
                        <td>
                            @if($shipping->shipping_method == 'standard')
                                <span class="badge bg-info">Tiêu chuẩn</span>
                            @else
                                <span class="badge bg-warning text-dark">Nhanh</span>
                            @endif
                        </td>
                        <td>
                            @switch($shipping->shipping_status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge bg-info">Đã xác nhận</span>
                                    @break
                                @case('shipping')
                                    <span class="badge bg-primary">Đang giao hàng</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                    @break
                                @case('returned')
                                    <span class="badge bg-secondary">Trả lại</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_status) }}</span>
                            @endswitch

                            @if($shipping->order && $shipping->order->payment &&
                                in_array($shipping->order->payment->payment_method, ['vnpay', 'bank_transfer']) &&
                                $shipping->order->payment->payment_status !== 'completed')
                                <br><small class="text-danger">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Chờ thanh toán
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($shipping->tracking_code)
                                {{ $shipping->tracking_code }}
                            @else
                                <span class="text-muted">Chưa có</span>
                            @endif
                        </td>
                        <td>{{ $shipping->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.shippings.show', $shipping->id) }}"
                                   class="btn btn-sm btn-outline-info" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($shipping->shipping_status === 'pending')
                                    <a href="{{ route('admin.shippings.edit', $shipping->id) }}"
                                       class="btn btn-outline-warning btn-sm"
                                       title="Cập nhật trạng thái">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif
                            </div>

                            <div class="mt-2">
                                @if($shipping->shipping_status == 'pending')
                                    <form method="POST" action="{{ route('admin.shippings.mark-shipped', $shipping->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-info" title="Xác nhận vận chuyển">
                                            <i class="bi bi-check me-1"></i>Xác nhận
                                        </button>
                                    </form>
                                @elseif($shipping->shipping_status == 'confirmed')
                                    <form method="POST" action="{{ route('admin.shippings.mark-shipped', $shipping->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-primary" title="Bắt đầu giao hàng">
                                            <i class="bi bi-truck me-1"></i>Bắt đầu giao
                                        </button>
                                    </form>
                                @elseif($shipping->shipping_status == 'shipping')
                                    <form method="POST" action="{{ route('admin.shippings.mark-delivered', $shipping->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Đánh dấu đã giao hàng">
                                            <i class="bi bi-check-circle me-1"></i>Đã giao
                                        </button>
                                    </form>
                                @elseif($shipping->shipping_status == 'delivered')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-all me-1"></i>Hoàn thành
                                    </span>
                                @elseif($shipping->shipping_status == 'failed')
                                    <form method="POST" action="{{ route('admin.shippings.mark-shipped', $shipping->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="retry_shipping" value="1">
                                        <button type="submit" class="btn btn-xs btn-warning" title="Thử giao lại">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Thử lại
                                        </button>
                                    </form>
                                @elseif($shipping->shipping_status == 'returned')
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-arrow-return-left me-1"></i>Đã trả lại
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Không có dữ liệu vận chuyển nào
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @include('admin.layouts.pagination', ['paginator' => $shippings, 'itemName' => 'vận chuyển'])
        </div>
    </div>
@endsection
