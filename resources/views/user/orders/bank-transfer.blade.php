@extends('user.layouts.master')

@section('title', 'Chuyển khoản ngân hàng')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #ec8ca3;">🏦 Chuyển khoản ngân hàng</h2>
            <p class="text-muted">Vui lòng chuyển khoản trong vòng 24 giờ để giữ đơn hàng</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4" style="color: #ec8ca3;">📋 Thông tin đơn hàng</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                            <p><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold fs-4">{{ number_format($bankInfo['amount'], 0, ',', '.') }}₫</span></p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm rounded-4 p-4 mb-4" style="border-left: 4px solid #ec8ca3;">
                    <h5 class="fw-bold mb-4" style="color: #ec8ca3;">💳 Thông tin chuyển khoản</h5>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="bank-info">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên tài khoản:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold" value="{{ $bankInfo['account_name'] }}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $bankInfo['account_name'] }}')">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số tài khoản:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold text-primary" value="{{ $bankInfo['account_number'] }}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $bankInfo['account_number'] }}')">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ngân hàng:</label>
                                    <input type="text" class="form-control" value="{{ $bankInfo['bank_name'] }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chi nhánh:</label>
                                    <input type="text" class="form-control" value="{{ $bankInfo['branch'] }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số tiền:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold text-danger fs-5" value="{{ number_format($bankInfo['amount'], 0, ',', '.') }}₫" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $bankInfo['amount'] }}')">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nội dung chuyển khoản:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold text-success" value="{{ $bankInfo['transfer_note'] }}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $bankInfo['transfer_note'] }}')">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Vui lòng ghi chính xác nội dung để đơn hàng được xử lý nhanh chóng</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-center">
                            <div class="qr-code-section">
                                <h6 class="fw-bold mb-3">QR Code</h6>
                                <div class="qr-code bg-light p-3 rounded">
                                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(200)->generate('VietinBank|' . $bankInfo['account_number'] . '|' . $bankInfo['account_name'] . '|' . $bankInfo['amount'] . '|' . $bankInfo['transfer_note'])) }}"
                                         alt="QR Code" class="img-fluid">
                                </div>
                                <small class="text-muted mt-2 d-block">Quét mã QR để chuyển khoản nhanh</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <h6 class="fw-bold mb-2">⚠️ Lưu ý quan trọng:</h6>
                    <ul class="mb-0">
                        <li>Vui lòng chuyển khoản <strong>đúng số tiền</strong> và ghi <strong>đúng nội dung</strong></li>
                        <li>Đơn hàng sẽ bị hủy nếu không nhận được thanh toán trong vòng <strong>24 giờ</strong></li>
                        <li>Sau khi chuyển khoản, đơn hàng sẽ được xử lý trong vòng <strong>1-2 giờ</strong></li>
                        <li>Liên hệ hotline nếu có vấn đề về thanh toán</li>
                    </ul>
                </div>

                <div class="text-center">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary me-3">
                        <i class="bi bi-list-ul me-2"></i>Xem đơn hàng của tôi
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Đã sao chép!'
                });
            });
        }
    </script>
@endsection
