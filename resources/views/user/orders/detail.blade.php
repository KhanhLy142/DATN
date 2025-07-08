@extends('user.layouts.master')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết #{{ $order->id }}</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Thành công!</strong><br>
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Có lỗi xảy ra!</strong><br>
                        {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-pink mb-2">Đơn hàng #{{ $order->id }}</h2>
                <p class="text-muted mb-0">
                    Đặt ngày {{ $order->created_at->format('d/m/Y') }} lúc {{ $order->created_at->format('H:i') }}
                    @if($order->tracking_number)
                        • Mã vận đơn: {{ $order->tracking_number }}
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                @if($order->status == 'pending')
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">{{ $order->status_label }}</span>
                @elseif($order->status == 'processing')
                    <span class="badge bg-info fs-6 px-3 py-2">{{ $order->status_label }}</span>
                @elseif($order->status == 'shipped')
                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $order->status_label }}</span>
                @elseif($order->status == 'completed')
                    <span class="badge bg-success fs-6 px-3 py-2">{{ $order->status_label }}</span>
                @elseif($order->status == 'cancelled')
                    <span class="badge bg-danger fs-6 px-3 py-2">{{ $order->status_label }}</span>
                @else
                    <span class="badge bg-secondary fs-6 px-3 py-2">{{ $order->status_label }}</span>
                @endif

                <div class="mt-2">
                    @if($order->canBeCancelled())
                        <button class="btn btn-outline-danger btn-sm me-2"
                                onclick="confirmCancelOrder({{ $order->id }}, '{{ $order->payment->payment_method }}', '{{ $order->payment->payment_status }}', '{{ $order->shipping ? $order->shipping->shipping_status : 'pending' }}')">
                            <i class="bi bi-x-circle me-1"></i>Hủy đơn
                        </button>
                    @endif
                    <button class="btn btn-pink btn-sm">
                        <i class="bi bi-headset me-1"></i>Liên hệ hỗ trợ
                    </button>
                </div>
            </div>
        </div>

        @if($order->status == 'completed')
            <div class="alert alert-success mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">🎉 Đơn hàng đã hoàn thành thành công!</h6>
                        <p class="mb-0">
                            Cảm ơn bạn đã mua sắm.
                            @if($order->payment && $order->payment->payment_method === 'cod')
                                Thanh toán COD đã được xác nhận.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif($order->status == 'shipped')
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-truck fs-3 text-primary me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">🚛 Đơn hàng đang được giao đến bạn!</h6>
                        <p class="mb-0">Shipper sẽ liên hệ với bạn trước khi giao hàng. Vui lòng để ý điện thoại.</p>
                    </div>
                </div>
            </div>
        @elseif($order->status == 'cancelled')
            <div class="alert alert-danger mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle fs-3 text-danger me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">❌ Đơn hàng đã bị hủy</h6>
                        <p class="mb-0">
                            Đơn hàng đã được hủy thành công.
                            @if($order->payment && $order->payment->payment_method === 'bank_transfer' && $order->payment->payment_status === 'completed')
                                Nếu đã chuyển khoản, vui lòng liên hệ hỗ trợ để được hoàn tiền.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($order->customer_note)
            <div class="alert alert-info mb-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-sticky me-2"></i>Ghi chú đơn hàng:</h6>
                <p class="mb-0">{{ $order->customer_note }}</p>
            </div>
        @endif

        <div class="card shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-4">Tiến trình đơn hàng</h5>

            <div class="order-tracking">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="tracking-step {{ in_array($order->status, ['pending', 'processing', 'shipped', 'completed']) ? 'active' : '' }}">
                            <div class="tracking-icon {{ in_array($order->status, ['pending', 'processing', 'shipped', 'completed']) ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <h6 class="mb-1">Đã đặt hàng</h6>
                            <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="tracking-step {{ in_array($order->status, ['processing', 'shipped', 'completed']) ? 'active' : '' }}">
                            <div class="tracking-icon {{ in_array($order->status, ['processing', 'shipped', 'completed']) ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h6 class="mb-1">Xác nhận</h6>
                            <small class="text-muted">
                                @if(in_array($order->status, ['processing', 'shipped', 'completed']))
                                    Đã xác nhận
                                    @if($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer']) && $order->payment->payment_status === 'completed')
                                        <br><small class="text-success">💳 Đã thanh toán</small>
                                    @endif
                                @else
                                    Đang chờ
                                    @if($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer']) && $order->payment->payment_status === 'pending')
                                        <br><small class="text-warning">⏳ Chờ thanh toán</small>
                                    @endif
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="tracking-step {{ in_array($order->status, ['shipped', 'completed']) ? 'active' : '' }}">
                            <div class="tracking-icon {{ in_array($order->status, ['shipped', 'completed']) ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-truck"></i>
                            </div>
                            <h6 class="mb-1">Đang giao</h6>
                            <small class="text-muted">
                                @if(in_array($order->status, ['shipped', 'completed']))
                                    Đang giao hàng
                                @else
                                    Chưa bắt đầu
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="tracking-step {{ $order->status == 'completed' ? 'active' : '' }}">
                            <div class="tracking-icon {{ $order->status == 'completed' ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-house-check"></i>
                            </div>
                            <h6 class="mb-1">Hoàn thành</h6>
                            <small class="text-muted">
                                @if($order->status == 'completed')
                                    Đã hoàn thành
                                    @if($order->payment && $order->payment->payment_method === 'cod')
                                        <br><small class="text-success">💰 Đã thanh toán COD</small>
                                    @endif
                                @else
                                    Chưa hoàn thành
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4">Sản phẩm đã đặt</h5>

                    @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center {{ !$loop->last ? 'border-bottom pb-4 mb-4' : '' }}">
                            <img src="{{ $item->product->main_image_url ?? asset('images/default-product.png') }}"
                                 alt="{{ $item->product->name }}"
                                 class="img-fluid rounded border me-3"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                @if($item->product->brand)
                                    <p class="text-muted mb-1">Thương hiệu: {{ $item->product->brand->name }}</p>
                                @endif
                                <p class="text-muted mb-1">
                                    @if($item->variant)
                                        @if($item->variant->color) Màu sắc: {{ $item->variant->color }} @endif
                                        @if($item->variant->volume) • Dung tích: {{ $item->variant->volume }} @endif
                                        @if($item->variant->scent) • Mùi hương: {{ $item->variant->scent }} @endif
                                        •
                                    @endif
                                    Số lượng: {{ $item->quantity }}
                                </p>
                                <div class="d-flex align-items-center">
                                    <span class="text-pink fw-bold me-2">{{ number_format($item->price, 0, ',', '.') }}₫</span>
                                    @if($item->product->base_price > $item->price)
                                        <small class="text-decoration-line-through text-muted">{{ number_format($item->product->base_price, 0, ',', '.') }}₫</small>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary mb-2">{{ number_format($item->subtotal, 0, ',', '.') }}₫</div>
                                <a href="{{ route('products.show', $item->product->id) }}" class="btn btn-outline-pink btn-sm me-1">
                                    <i class="bi bi-cart-plus me-1"></i>Mua lại
                                </a>

                                @if($order->status == 'completed')
                                    <a href="{{ route('reviews.create', ['product' => $item->product->id, 'order_id' => $order->id]) }}"
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-star me-1"></i>Đánh giá
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="border-top pt-3 mt-3">
                        <div class="text-end">
                            <p class="mb-1">Tạm tính: <span class="fw-bold">{{ number_format($order->orderItems->sum('subtotal'), 0, ',', '.') }}₫</span></p>

                            @if($order->hasDiscount())
                                <p class="mb-1 text-success">
                                    <i class="bi bi-tag-fill me-1"></i>
                                    Mã giảm giá ({{ $order->discount->code }}):
                                    <span class="fw-bold">-{{ number_format($order->discount->discount_value, 0, ',', '.') }}₫</span>
                                </p>
                            @endif

                            @if($order->shipping)
                                <p class="mb-1">Phí vận chuyển: <span class="fw-bold">{{ number_format($order->shipping->shipping_fee, 0, ',', '.') }}₫</span></p>
                            @endif

                            <h5 class="text-pink fw-bold">Tổng cộng: {{ number_format($order->total_amount, 0, ',', '.') }}₫</h5>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Thông tin giao hàng</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Người nhận</h6>
                            <p class="mb-1"><strong>{{ $order->customer->name }}</strong></p>
                            @if($order->customer->phone)
                                <p class="mb-1">SĐT: {{ $order->customer->phone }}</p>
                            @endif
                            @if($order->customer->email)
                                <p class="mb-0">Email: {{ $order->customer->email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2">Địa chỉ giao hàng</h6>
                            @if($order->shipping)
                                <p class="mb-1">{{ $order->shipping->shipping_address }}</p>
                                <p class="mb-0">
                                    <small class="text-muted">
                                        Phương thức: {{ $order->shipping->shipping_method == 'standard' ? 'Giao hàng tiêu chuẩn' : 'Giao hàng nhanh' }}
                                    </small>
                                </p>
                                @if($order->shipping->shipping_note)
                                    <p class="mt-2 mb-0">
                                        <small class="text-info">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Ghi chú giao hàng: {{ $order->shipping->shipping_note }}
                                        </small>
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">Thông tin thanh toán</h5>
                    @if($order->payment)
                        <p class="mb-2">
                            <strong>Phương thức:</strong>
                            @if($order->payment->payment_method == 'cod')
                                <span class="badge bg-warning text-dark">Thanh toán khi nhận hàng (COD)</span>
                            @elseif($order->payment->payment_method == 'vnpay')
                                <span class="badge bg-primary">Ví điện tử VNPay</span>
                            @elseif($order->payment->payment_method == 'bank_transfer')
                                <span class="badge bg-info">Chuyển khoản ngân hàng</span>
                            @else
                                {{ $order->payment->payment_method }}
                            @endif
                        </p>
                        <p class="mb-2">
                            <strong>Trạng thái:</strong>
                            @if($order->payment->payment_status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                            @elseif($order->payment->payment_status == 'completed')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment->payment_status == 'failed')
                                <span class="badge bg-danger">Thanh toán thất bại</span>
                            @endif
                        </p>
                        <p class="mb-0">
                            <strong>Số tiền:</strong>
                            <span class="text-pink fw-bold">{{ number_format($order->payment->amount, 0, ',', '.') }}₫</span>
                        </p>

                        @if($order->payment->payment_method == 'cod')
                            @if($order->status == 'completed')
                                <div class="alert alert-success mt-3 p-2">
                                    <small><i class="bi bi-check-circle me-1"></i> Đã thanh toán COD khi giao hàng</small>
                                </div>
                            @else
                                <div class="alert alert-info mt-3 p-2">
                                    <small><i class="bi bi-info-circle me-1"></i> Sẽ thanh toán khi nhận hàng</small>
                                </div>
                            @endif
                        @elseif($order->payment->payment_method == 'vnpay')
                            @if($order->payment->payment_status == 'completed')
                                <div class="alert alert-success mt-3 p-2">
                                    <small><i class="bi bi-check-circle me-1"></i> VNPay đã thanh toán thành công</small>
                                </div>
                            @else
                                <div class="alert alert-warning mt-3 p-2">
                                    <small><i class="bi bi-clock me-1"></i> Đang chờ xác nhận từ VNPay</small>
                                </div>
                            @endif
                        @elseif($order->payment->payment_method == 'bank_transfer')
                            @if($order->payment->payment_status == 'completed')
                                <div class="alert alert-success mt-3 p-2">
                                    <small><i class="bi bi-check-circle me-1"></i> Đã nhận chuyển khoản</small>
                                </div>
                            @else
                                <div class="alert alert-warning mt-3 p-2">
                                    <small><i class="bi bi-clock me-1"></i> Chờ xác nhận chuyển khoản</small>
                                </div>
                            @endif
                        @endif

                        @if($order->payment->payment_method == 'bank_transfer' && $order->payment->payment_status == 'pending')
                            <div class="mt-3">
                                <a href="{{ route('orders.bank-info', $order->id) }}" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-bank me-2"></i>Xem thông tin chuyển khoản
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="card shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3">Hành động</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>Xem tất cả đơn hàng
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-pink">
                            <i class="bi bi-cart-plus me-2"></i>Tiếp tục mua sắm
                        </a>

                        @if($order->status !== 'completed')
                            <button class="btn btn-outline-info" onclick="alert('Hotline: 1900 123 456\nEmail: support@yourstore.com')">
                                <i class="bi bi-headset me-2"></i>Liên hệ hỗ trợ
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($order->status == 'completed')
            <div id="review-section" class="container py-4">
                <div class="card shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4 text-pink">⭐ Đánh giá sản phẩm</h5>

                    <div class="row">
                        @foreach($order->orderItems as $item)
                            <div class="col-md-6 mb-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="{{ $item->product->main_image_url ?? asset('images/default-product.png') }}"
                                                 class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;"
                                                 alt="{{ $item->product->name }}">
                                            <div>
                                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                <small class="text-muted">
                                                    @if($item->variant && $item->variant->color)
                                                        {{ $item->variant->color }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <a href="{{ route('reviews.create', ['product' => $item->product->id, 'order_id' => $order->id]) }}"
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-star me-1"></i>Viết đánh giá
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Đánh giá của bạn sẽ giúp khách hàng khác có thêm thông tin hữu ích
                        </small>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <script>
        function confirmCancelOrder(orderId, paymentMethod, paymentStatus, shippingStatus) {
            console.log('=== DEBUG CANCEL ORDER ===');
            console.log('Order ID:', orderId);
            console.log('Payment Method:', paymentMethod);
            console.log('Payment Status:', paymentStatus);
            console.log('Shipping Status:', shippingStatus);

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found!');
                alert('Lỗi: Không tìm thấy CSRF token. Vui lòng refresh trang.');
                return;
            }

            let message = '⚠️ Bạn có chắc chắn muốn hủy đơn hàng này?';
            let warningText = '';

            switch (paymentMethod) {
                case 'cod':
                    if (shippingStatus === 'pending') {
                        warningText = '\n\n✅ Đơn hàng COD chưa được xử lý vận chuyển, có thể hủy được.';
                    } else {
                        warningText = '\n\n🔴 LÚU Ý: Đơn hàng COD đã được xử lý vận chuyển, không thể hủy!';
                        message += warningText;
                        alert(message);
                        return;
                    }
                    break;

                case 'vnpay':
                    if (paymentStatus === 'pending') {
                        warningText = '\n\n✅ Đơn hàng VNPay chưa được thanh toán, có thể hủy được.';
                    } else {
                        warningText = '\n\n🔴 LÚU Ý: Đơn hàng VNPay đã được thanh toán, không thể hủy! Vui lòng liên hệ hỗ trợ để được hoàn tiền.';
                        message += warningText;
                        alert(message);
                        return;
                    }
                    break;

                case 'bank_transfer':
                    if (paymentStatus === 'pending') {
                        warningText = '\n\n💡 Đơn hàng chuyển khoản chưa được xác nhận, có thể hủy được. Nếu đã chuyển khoản, vui lòng liên hệ hỗ trợ để được hoàn tiền.';
                    } else {
                        warningText = '\n\n🔴 LÚU Ý: Đơn hàng chuyển khoản đã được xác nhận, không thể hủy! Vui lòng liên hệ hỗ trợ để được hoàn tiền.';
                        message += warningText;
                        alert(message);
                        return;
                    }
                    break;

                default:
                    warningText = '\n\n⚠️ Phương thức thanh toán không xác định.';
            }

            message += warningText;
            console.log('Confirm message:', message);

            if (confirm(message)) {
                console.log('User confirmed cancellation');

                const cancelUrl = `/orders/${orderId}/cancel`;
                console.log('Cancel URL:', cancelUrl);

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = cancelUrl;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');

                form.appendChild(csrfInput);
                document.body.appendChild(form);

                console.log('Form submitting...');
                form.submit();
            } else {
                console.log('User cancelled the cancellation');
            }
        }
    </script>

    <style>
        .text-pink {
            color: #ec8ca3 !important;
        }
        .btn-pink {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
            color: white;
        }
        .btn-pink:hover {
            background-color: #e07a96;
            border-color: #e07a96;
            color: white;
        }
        .btn-outline-pink {
            color: #ec8ca3;
            border-color: #ec8ca3;
        }
        .btn-outline-pink:hover {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
            color: white;
        }
        .tracking-step.active {
            color: #28a745;
        }

        /* Animation cho alerts */
        .alert {
            animation: slideInDown 0.3s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert.alert-success {
            border-left: 4px solid #28a745;
        }

        .alert.alert-danger {
            border-left: 4px solid #dc3545;
        }

        .alert.alert-warning {
            border-left: 4px solid #ffc107;
        }

        .alert.alert-info {
            border-left: 4px solid #17a2b8;
        }
    </style>
@endsection
