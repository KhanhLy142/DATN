@extends('admin.layouts.master')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết đơn hàng #{{ $order->id }}</h4>

        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-body">
                @if($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer']) && $order->payment->payment_status !== 'completed')
                    <div class="alert alert-warning border-warning">
                        <h6 class="alert-heading mb-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Đơn hàng chưa được thanh toán
                        </h6>
                        <p class="mb-2">
                            Đơn hàng này sử dụng phương thức <strong>{{ $order->payment->payment_method === 'vnpay' ? 'VNPay' : 'Chuyển khoản ngân hàng' }}</strong>
                            nhưng chưa được thanh toán.
                        </p>
                        <p class="mb-0">
                            <strong>Không thể chuyển sang trạng thái vận chuyển</strong> cho đến khi khách hàng hoàn tất thanh toán.
                        </p>
                        @if($order->payment->payment_method === 'bank_transfer')
                            <hr>
                            <a href="{{ route('admin.payments.edit', $order->payment->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-check-circle me-1"></i>Xác nhận thanh toán
                            </a>
                        @endif
                    </div>
                @endif
                <h5 class="fw-bold mb-3">Thông tin đơn hàng</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                        <p><strong>Tracking Number:</strong> {{ $order->tracking_number ?? 'Chưa có' }}</p>
                        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Trạng thái:</strong>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            @elseif($order->status == 'processing')
                                <span class="badge bg-info">Đang xử lý</span>
                            @elseif($order->status == 'shipped')
                                <span class="badge bg-primary">Đang giao hàng</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success">Hoàn thành</span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger">Đã hủy</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        @if($order->payment)
                            <p><strong>Phương thức thanh toán:</strong>
                                @if($order->payment->payment_method == 'cod')
                                    Thanh toán khi nhận hàng (COD)
                                @elseif($order->payment->payment_method == 'vnpay')
                                    VNPay
                                @elseif($order->payment->payment_method == 'bank_transfer')
                                    Chuyển khoản ngân hàng
                                @else
                                    {{ ucfirst($order->payment->payment_method) }}
                                @endif
                            </p>
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
                            <p><strong>Phương thức vận chuyển:</strong>
                                @if($order->shipping->shipping_method == 'standard')
                                    Giao hàng tiêu chuẩn
                                @elseif($order->shipping->shipping_method == 'express')
                                    Giao hàng nhanh
                                @else
                                    {{ ucfirst($order->shipping->shipping_method) }}
                                @endif
                            </p>
                            <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping->shipping_fee ?? 0, 0, ',', '.') }}đ</p>
                        @endif
                    </div>
                </div>

                @php
                    $customerNote = '';
                    try {
                        $metadata = json_decode($order->note, true);
                        $customerNote = $metadata['__customer_note__'] ?? '';
                    } catch (\Exception $e) {
                        $customerNote = '';
                    }
                @endphp

                @if($customerNote)
                    <div class="row">
                        <div class="col-12 mt-3">
                            <div class="alert alert-info">
                                <h6 class="fw-bold mb-2">💬 Ghi chú từ khách hàng:</h6>
                                <p class="mb-0">{{ $customerNote }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

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
                                @elseif($order->shipping->shipping_status == 'confirmed')
                                    <span class="badge bg-info">Đã xác nhận</span>
                                @elseif($order->shipping->shipping_status == 'shipping')
                                    <span class="badge bg-primary">Đang giao hàng</span>
                                @elseif($order->shipping->shipping_status == 'delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->shipping->shipping_status) }}</span>
                                @endif
                            </p>
                            @if($order->shipping->tracking_code)
                                <p><strong>Mã vận đơn:</strong> {{ $order->shipping->tracking_code }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

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
                        @php $subtotal = 0; @endphp
                        @foreach($order->orderItems as $item)
                            @php
                                $itemTotal = $item->price * $item->quantity;
                                $subtotal += $itemTotal;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                                    @if($item->variant_id)
                                        <br><small class="text-muted">Variant ID: {{ $item->variant_id }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td>{{ number_format($itemTotal, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tạm tính:</td>
                            <td class="fw-bold">{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                        </tr>
                        @if($order->shipping && $order->shipping->shipping_fee > 0)
                            <tr>
                                <td colspan="4" class="text-end">Phí vận chuyển:</td>
                                <td>{{ number_format($order->shipping->shipping_fee, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif
                        <tr class="table-primary">
                            <td colspan="4" class="text-end fw-bold fs-5">Tổng thanh toán:</td>
                            <td class="fw-bold fs-5 text-danger">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>



        <div class="text-end">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>
@endsection
