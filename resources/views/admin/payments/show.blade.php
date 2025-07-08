@extends('admin.layouts.master')

@section('title', 'Chi tiết thanh toán')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết thanh toán</h4>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-credit-card-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="card-title text-primary mb-0">Giao dịch #{{ $payment->id }}</h5>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-cart-fill me-2 text-info"></i>
                            <strong>ID Đơn hàng:</strong>
                            <span class="badge bg-info">#{{ $payment->order_id }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-cash-coin me-2 text-success"></i>
                            <strong>Số tiền:</strong>
                            <span class="fw-bold text-success fs-5">{{ number_format($payment->amount) }}đ</span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-wallet2 me-2 text-warning"></i>
                            <strong>Phương thức:</strong>
                            @switch($payment->payment_method)
                                @case('cod')
                                    <span class="badge bg-warning text-dark">COD - Thanh toán khi nhận hàng</span>
                                    @break
                                @case('vnpay')
                                    <span class="badge bg-primary">VNPay - Cổng thanh toán</span>
                                    @break
                                @case('bank_transfer')
                                    <span class="badge bg-info">Chuyển khoản ngân hàng</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                            @endswitch
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-toggle-{{ $payment->payment_status === 'completed' ? 'on' : 'off' }} me-2 text-{{ $payment->payment_status === 'completed' ? 'success' : ($payment->payment_status === 'failed' ? 'danger' : 'warning') }}"></i>
                            <strong>Trạng thái:</strong>
                            @switch($payment->payment_status)
                                @case('pending')
                                    <span class="badge bg-warning">Đang chờ xử lý</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Đã hoàn thành</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                    @break
                                @case('refunded')
                                    <span class="badge bg-secondary">Đã hoàn tiền</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($payment->payment_status) }}</span>
                            @endswitch
                        </p>
                    </div>
                </div>

                @if($payment->vnpay_transaction_id)
                    <p class="card-text">
                        <i class="bi bi-qr-code me-2 text-info"></i>
                        <strong>Mã giao dịch VNPay:</strong>
                        <span class="badge bg-light text-dark border">{{ $payment->vnpay_transaction_id }}</span>
                    </p>
                @endif

                @if($payment->payment_note)
                    <p class="card-text">
                        <i class="bi bi-chat-text me-2 text-secondary"></i>
                        <strong>Ghi chú:</strong> {{ $payment->payment_note }}
                    </p>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-calendar-plus me-2 text-success"></i>
                            <strong>Ngày tạo:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-clock-history me-2 text-warning"></i>
                            <strong>Cập nhật lần cuối:</strong> {{ $payment->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                @if($payment->order)
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="bi bi-box-seam me-2 text-info"></i>
                            <strong>Thông tin đơn hàng:</strong>
                        </p>
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Khách hàng:</small><br>
                                        <span class="fw-semibold">{{ $payment->order->customer_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Trạng thái đơn hàng:</small><br>
                                        <span class="badge bg-info">{{ ucfirst($payment->order->status ?? 'N/A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
