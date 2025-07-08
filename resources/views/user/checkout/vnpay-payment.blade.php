@extends('user.layouts.master')

@section('title', 'Thanh toán MoMo')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #ec8ca3;">📱 Thanh toán MoMo</h2>
            <p class="text-muted">Đang ở chế độ test - không thu phí thật</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm rounded-4 p-4 text-center">
                    <h5 class="fw-bold mb-4">Thông tin thanh toán</h5>

                    <div class="mb-3">
                        <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                        <p><strong>Số tiền:</strong> <span class="text-danger fw-bold fs-4">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></p>
                    </div>

                    <div class="alert alert-info">
                        <strong>🧪 Chế độ Test Mode</strong><br>
                        Đây là môi trường test, không có giao dịch thật
                    </div>

                    <div class="d-grid gap-3">
                        <a href="{{ $mockMomoUrl }}" class="btn btn-danger btn-lg">
                            <i class="bi bi-wallet2 me-2"></i>Thanh toán thành công (Test)
                        </a>

                        <a href="{{ route('order.momo.callback', ['orderId' => $order->id, 'resultCode' => 1001]) }}"
                           class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-2"></i>Mô phỏng thanh toán thất bại
                        </a>

                        <a href="{{ route('order.checkout') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
