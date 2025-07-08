@extends('admin.layouts.master')

@section('title', 'Sửa thanh toán')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm rounded-4 p-4">
            <h4 class="fw-bold text-center text-pink mb-4">Cập nhật trạng thái thanh toán #{{ $payment->id }}</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card bg-light border-0 mb-4">
                <div class="card-body p-3">
                    <h6 class="card-title text-muted mb-3">Thông tin thanh toán hiện tại:</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">ID Đơn hàng:</small><br>
                            <span class="fw-semibold">#{{ $payment->order_id }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Số tiền:</small><br>
                            <span class="fw-bold text-success">{{ number_format($payment->amount) }}đ</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Phương thức:</small><br>
                            @switch($payment->payment_method)
                                @case('cod')
                                    <span class="badge bg-warning text-dark">COD</span>
                                    @break
                                @case('vnpay')
                                    <span class="badge bg-primary">VNPay</span>
                                    @break
                                @case('bank_transfer')
                                    <span class="badge bg-info">Chuyển khoản</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                            @endswitch
                        </div>
                    </div>
                    @if($payment->order)
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <small class="text-muted">Trạng thái đơn hàng:</small><br>
                                @switch($payment->order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-info">Đang xử lý</span>
                                        @break
                                    @case('shipped')
                                        <span class="badge bg-primary">Đang giao hàng</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                        @break
                                @endswitch
                            </div>
                            @if($payment->order->shipping)
                                <div class="col-md-4">
                                    <small class="text-muted">Trạng thái vận chuyển:</small><br>
                                    @switch($payment->order->shipping->shipping_status)
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
                                    @endswitch
                                </div>
                            @endif
                            <div class="col-md-4">
                                <small class="text-muted">Khách hàng:</small><br>
                                <span class="fw-semibold">{{ $payment->order->customer->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái thanh toán <span class="text-danger">*</span></label>
                    <select name="payment_status" class="form-select" id="payment_status_select" required>
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="pending" {{ old('payment_status', $payment->payment_status) == 'pending' ? 'selected' : '' }}>
                            Đang chờ xử lý
                        </option>
                        <option value="completed" {{ old('payment_status', $payment->payment_status) == 'completed' ? 'selected' : '' }}>
                            Đã hoàn thành
                        </option>
                        <option value="failed" {{ old('payment_status', $payment->payment_status) == 'failed' ? 'selected' : '' }}>
                            Thất bại
                        </option>
                        <option value="refunded" {{ old('payment_status', $payment->payment_status) == 'refunded' ? 'selected' : '' }}>
                            Đã hoàn tiền
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ghi chú</label>
                    <textarea class="form-control" name="payment_note" rows="3" placeholder="Nhập lý do cập nhật trạng thái (tùy chọn)">{{ old('payment_note', $payment->payment_note) }}</textarea>
                </div>

                @if($payment->payment_method === 'vnpay')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã giao dịch VNPay</label>
                        <input type="text" class="form-control" name="vnpay_transaction_id"
                               value="{{ old('vnpay_transaction_id', $payment->vnpay_transaction_id) }}"
                               placeholder="Nhập mã giao dịch VNPay (nếu có)">
                        <div class="form-text">Mã giao dịch từ VNPay để đối chiếu</div>
                    </div>
                @endif

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">
                        <i class="bi bi-check-circle me-1"></i>Cập nhật
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('payment_status_select');
            const paymentMethod = '{{ $payment->payment_method }}';
            const shippingStatus = '{{ $payment->order && $payment->order->shipping ? $payment->order->shipping->shipping_status : "" }}';
            const currentStatus = '{{ $payment->payment_status }}';

            statusSelect.addEventListener('change', function() {
                const selectedStatus = this.value;

                if (paymentMethod === 'cod' && selectedStatus === 'completed') {
                    if (shippingStatus !== 'delivered') {
                        const shippingStatusText = {
                            'pending': 'Chờ xác nhận',
                            'confirmed': 'Đã xác nhận',
                            'shipping': 'Đang giao hàng',
                            'delivered': 'Đã giao hàng'
                        };

                        alert('⚠️ Không thể hoàn thành thanh toán COD!\n\n' +
                            'Trạng thái vận chuyển hiện tại: ' + (shippingStatusText[shippingStatus] || 'Chưa rõ') + '\n' +
                            'Cần đánh dấu "Đã giao hàng" trước khi có thể hoàn thành thanh toán COD.\n\n' +
                            'Vui lòng cập nhật trạng thái vận chuyển trước!');

                        this.value = currentStatus;
                        return;
                    }
                }

                if (selectedStatus !== currentStatus) {
                    showStatusConfirmation(selectedStatus);
                }
            });

            function showStatusConfirmation(status) {
                let message = '';
                let needConfirm = false;

                switch (status) {
                    case 'completed':
                        if (paymentMethod === 'vnpay') {
                            message = '✅ Xác nhận hoàn thành thanh toán VNPay?\n\n' +
                                'Điều này sẽ:\n' +
                                '• Đánh dấu đã nhận được tiền\n' +
                                '• Chuyển đơn hàng sang "Đang xử lý"';
                            needConfirm = true;
                        } else if (paymentMethod === 'bank_transfer') {
                            message = '✅ Xác nhận hoàn thành chuyển khoản?\n\n' +
                                'Điều này sẽ:\n' +
                                '• Đánh dấu đã nhận được chuyển khoản\n' +
                                '• Chuyển đơn hàng sang "Đang xử lý"';
                            needConfirm = true;
                        } else if (paymentMethod === 'cod') {
                            message = '✅ Xác nhận hoàn thành thanh toán COD?\n\n' +
                                'Khách hàng đã thanh toán khi nhận hàng.';
                            needConfirm = true;
                        }
                        break;
                    case 'failed':
                        message = '❌ Xác nhận thanh toán thất bại?\n\n' +
                            '⚠️ Cảnh báo: Đơn hàng sẽ chuyển thành "Đã hủy"';
                        needConfirm = true;
                        break;
                    case 'refunded':
                        message = '↩️ Xác nhận đã hoàn tiền?\n\n' +
                            'Tiền đã được hoàn trả cho khách hàng.';
                        needConfirm = true;
                        break;
                }

                if (needConfirm && message) {
                    if (!confirm(message)) {
                        statusSelect.value = currentStatus;
                    }
                }
            }

            function updatePaymentMethodInfo() {
                const existingInfo = document.querySelector('.payment-method-info');
                if (existingInfo) existingInfo.remove();

                let infoHtml = '';
                let alertClass = 'alert-info';

                switch (paymentMethod) {
                    case 'cod':
                        infoHtml = '<i class="bi bi-cash-coin me-1"></i> <strong>COD:</strong> Thanh toán khi giao hàng - cần giao hàng thành công trước';
                        alertClass = 'alert-warning';
                        break;
                    case 'vnpay':
                        infoHtml = '<i class="bi bi-credit-card me-1"></i> <strong>VNPay:</strong> Cổng thanh toán trực tuyến - kiểm tra callback/webhook';
                        break;
                    case 'bank_transfer':
                        infoHtml = '<i class="bi bi-bank me-1"></i> <strong>Chuyển khoản:</strong> Kiểm tra sao kê ngân hàng trước khi xác nhận';
                        break;
                }

                if (infoHtml) {
                    const infoDiv = document.createElement('div');
                    infoDiv.className = `alert ${alertClass} payment-method-info mt-2 p-2`;
                    infoDiv.innerHTML = infoHtml;
                    statusSelect.parentNode.appendChild(infoDiv);
                }
            }

            updatePaymentMethodInfo();
        });
    </script>
@endsection
