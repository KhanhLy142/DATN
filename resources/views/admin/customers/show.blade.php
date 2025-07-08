@extends('admin.layouts.master')

@section('title', 'Chi tiết khách hàng')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết khách hàng</h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-circle"></i> Thông tin cá nhân
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold">Tên:</div>
                            <div class="col-8">{{ $customer->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold">Email:</div>
                            <div class="col-8">
                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                    {{ $customer->email }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold">Điện thoại:</div>
                            <div class="col-8">
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                        {{ $customer->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Chưa có</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold">Địa chỉ:</div>
                            <div class="col-8">
                                {{ $customer->address ?? 'Chưa có địa chỉ' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold">Ngày đăng ký:</div>
                            <div class="col-8">{{ $customer->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4 fw-semibold">Cập nhật:</div>
                            <div class="col-8">{{ $customer->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up"></i> Thống kê đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-pink mb-1">{{ $customer->orders->count() }}</h4>
                                    <small class="text-muted">Tổng đơn hàng</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">
                                    {{ number_format($customer->orders->sum('total_amount'), 0, ',', '.') }}đ
                                </h4>
                                <small class="text-muted">Tổng giá trị</small>
                            </div>
                        </div>

                        <hr>

                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-warning">
                                    <h6>{{ $customer->orders->where('status', 'pending')->count() }}</h6>
                                    <small>Chờ xử lý</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-info">
                                    <h6>{{ $customer->orders->where('status', 'processing')->count() }}</h6>
                                    <small>Đang xử lý</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-success">
                                    <h6>{{ $customer->orders->where('status', 'shipped')->count() }}</h6>
                                    <small>Đã giao</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history"></i> Đơn hàng gần đây
                </h5>
                @if($customer->orders->count() > 10)
                    <small class="text-muted">Hiển thị 10 đơn gần nhất</small>
                @endif
            </div>
            <div class="card-body">
                @if($customer->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="bg-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customer->orders as $order)
                                <tr>
                                    <td class="fw-semibold">#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="fw-semibold text-success">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge bg-info">Đang xử lý</span>
                                        @elseif($order->status == 'shipped')
                                            <span class="badge bg-success">Đã giao hàng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="btn btn-sm btn-outline-primary" title="Xem đơn hàng">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">Khách hàng chưa có đơn hàng nào</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </div>
@endsection
