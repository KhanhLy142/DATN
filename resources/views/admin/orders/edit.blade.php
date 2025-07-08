@extends('admin.layouts.master')

@section('title', 'Cập nhật trạng thái đơn hàng')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Cập nhật trạng thái đơn hàng #{{ $order->id }}</h4>

        <div class="card shadow-sm rounded-4">
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
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                        <p><strong>Tracking Number:</strong> {{ $order->tracking_number ?? 'Chưa có' }}</p>
                        <p><strong>Khách hàng:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                        <p><strong>Trạng thái hiện tại:</strong>
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
                            @endif
                        </p>
                        @if($order->payment)
                            <p><strong>Thanh toán:</strong>
                                @if($order->payment->payment_status == 'pending')
                                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                @elseif($order->payment->payment_status == 'completed')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @elseif($order->payment->payment_status == 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Chọn trạng thái mới</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    @if($order->payment)
                                        @if(in_array($order->payment->payment_method, ['vnpay', 'bank_transfer']))
                                            <div class="alert alert-info mt-2 p-2">
                                                <i class="bi bi-info-circle me-1"></i>
                                                <strong>Thanh toán {{ $order->payment->payment_method == 'vnpay' ? 'VNPay' : 'Chuyển khoản' }}:</strong>
                                                <br>• Cần hoàn thành thanh toán trước khi chuyển sang "Đang xử lý" hoặc "Đang giao hàng"
                                                <br>• Trạng thái thanh toán hiện tại:
                                                @if($order->payment->payment_status == 'pending')
                                                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                                @elseif($order->payment->payment_status == 'completed')
                                                    <span class="badge bg-success">Đã thanh toán ✓</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($order->payment->payment_status) }}</span>
                                                @endif
                                            </div>
                                        @elseif($order->payment->payment_method == 'cod')
                                            <div class="alert alert-warning mt-2 p-2">
                                                <i class="bi bi-cash-coin me-1"></i>
                                                <strong>Thanh toán COD:</strong>
                                                <br>• Thanh toán sẽ tự động hoàn thành khi đánh dấu "Đã giao hàng"
                                                <br>• Có thể chuyển trạng thái vận chuyển mà không cần thanh toán trước
                                                @if($order->shipping)
                                                    <br>• Trạng thái vận chuyển hiện tại:
                                                    @switch($order->shipping->shipping_status)
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
                                                            <span class="badge bg-success">Đã giao hàng ✓</span>
                                                            @break
                                                    @endswitch
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="shipping_method" class="form-label fw-semibold">Phương thức vận chuyển</label>
                                <select class="form-select" id="shipping_method" name="shipping_method">
                                    <option value="standard" {{ ($order->shipping && $order->shipping->shipping_method == 'standard') ? 'selected' : '' }}>Giao hàng tiêu chuẩn</option>
                                    <option value="express" {{ ($order->shipping && $order->shipping->shipping_method == 'express') ? 'selected' : '' }}>Giao hàng nhanh</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($order->shipping)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label fw-semibold">Địa chỉ giao hàng</label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3">{{ $order->shipping->shipping_address }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="text-end">
                        <button type="submit" class="btn btn-pink me-2">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const paymentMethod = '{{ $order->payment ? $order->payment->payment_method : "" }}';
            const paymentStatus = '{{ $order->payment ? $order->payment->payment_status : "" }}';
            const shippingStatus = '{{ $order->shipping ? $order->shipping->shipping_status : "" }}';

            statusSelect.addEventListener('change', function() {
                const selectedStatus = this.value;

                if (['vnpay', 'bank_transfer'].includes(paymentMethod) && paymentStatus !== 'completed') {
                    if (['processing', 'shipped'].includes(selectedStatus)) {
                        const paymentMethodText = paymentMethod === 'vnpay' ? 'VNPay' : 'Chuyển khoản';
                        if (!confirm('⚠️ Cảnh báo: Đơn hàng thanh toán bằng ' + paymentMethodText +
                            ' chưa được thanh toán.\n\nCần hoàn thành thanh toán trước khi chuyển sang trạng thái này!\n\nBạn có muốn tiếp tục không?')) {
                            this.value = '{{ $order->status }}';
                            return;
                        }
                    }
                }

                if (paymentMethod === 'cod') {
                    if (selectedStatus === 'completed') {
                        if (shippingStatus !== 'delivered') {
                            alert('💡 Lưu ý: Đơn hàng COD sẽ tự động được đánh dấu đã thanh toán khi hoàn thành.\n\nCần đánh dấu "Đã giao hàng" trước để hoàn thành thanh toán COD.');
                        } else {
                            if (!confirm('✅ Xác nhận: Đơn hàng COD sẽ tự động được đánh dấu đã thanh toán khi hoàn thành.\n\nTiếp tục?')) {
                                this.value = '{{ $order->status }}';
                            }
                        }
                    }
                }

                showStatusInfo(selectedStatus);
            });

            function showStatusInfo(status) {
                const existingAlert = document.querySelector('.status-info-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                let message = '';
                let alertClass = 'alert-info';

                switch (status) {
                    case 'processing':
                        message = '<i class="bi bi-gear me-1"></i> <strong>Đang xử lý:</strong> Đơn hàng được xác nhận, chuẩn bị hàng hóa và đóng gói.';
                        break;
                    case 'shipped':
                        message = '<i class="bi bi-truck me-1"></i> <strong>Đang giao hàng:</strong> Hàng đã được gửi cho đơn vị vận chuyển.';
                        alertClass = 'alert-primary';
                        break;
                    case 'completed':
                        message = '<i class="bi bi-check-circle me-1"></i> <strong>Hoàn thành:</strong> Đơn hàng đã được giao thành công và thanh toán xong.';
                        alertClass = 'alert-success';
                        break;
                    case 'cancelled':
                        message = '<i class="bi bi-x-circle me-1"></i> <strong>Đã hủy:</strong> Đơn hàng bị hủy, hàng hóa sẽ được hoàn trả về kho.';
                        alertClass = 'alert-danger';
                        break;
                }

                if (message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = `alert ${alertClass} status-info-alert mt-2 p-2`;
                    alertDiv.innerHTML = message;
                    statusSelect.parentNode.appendChild(alertDiv);
                }
            }
        });
    </script>
@endsection
