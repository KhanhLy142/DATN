@extends('admin.layouts.master')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết đơn hàng #{{ $order->id }}</h4>

        {{-- Thông tin đơn hàng --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Thông tin đơn hàng</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Trạng thái:</strong>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            @elseif($order->status == 'processing')
                                <span class="badge bg-info">Đang xử lý</span>
                            @elseif($order->status == 'shipped')
                                <span class="badge bg-success">Đã giao hàng</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        @if($order->payment)
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment->payment_method_label }}</p>
                            <p><strong>Trạng thái thanh toán:</strong>
                                @if($order->payment->payment_status == 'pending')
                                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                @elseif($order->payment->payment_status == 'completed')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @elseif($order->payment->payment_status == 'failed')
                                    <span class="badge bg-danger">Thanh toán thất bại</span>
                                @endif
                            </p>
                        @endif
                        @if($order->shipping)
                            <p><strong>Phương thức vận chuyển:</strong> {{ $order->shipping->shipping_method_label }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông tin khách hàng --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($order->shipping)
                            <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping->shipping_address }}</p>
                            <p><strong>Trạng thái vận chuyển:</strong>
                                @if($order->shipping->shipping_status == 'pending')
                                    <span class="badge bg-warning text-dark">Chờ giao hàng</span>
                                @elseif($order->shipping->shipping_status == 'shipped')
                                    <span class="badge bg-info">Đang giao hàng</span>
                                @elseif($order->shipping->shipping_status == 'delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Sản phẩm đã đặt</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="bg-light">
                        <tr>
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <p class="fs-5 fw-bold text-danger">Tổng thanh toán: {{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>

        {{-- Nút quay lại --}}
        <div class="text-end">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                 Quay lại
            </a>
        </div>
    </div>
@endsection
