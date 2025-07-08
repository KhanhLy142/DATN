@extends('user.layouts.master')

@section('title', 'Đơn hàng của tôi')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/account">Tài khoản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <h2 class="fw-bold mb-4 text-center text-pink">Đơn hàng của tôi</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Thành công!</strong><br>
                        {!! session('success') !!}
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
                        {!! session('error') !!}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Thông tin:</strong><br>
                        {!! session('info') !!}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm rounded-4 p-4 mb-4">
            <form method="GET" action="{{ route('orders.index') }}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="status" id="all" value="all"
                                   {{ !request('status') || request('status') == 'all' ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="btn btn-outline-pink" for="all">
                                Tất cả
                                @if(isset($orderCounts))
                                    <span class="badge bg-secondary ms-1">{{ array_sum($orderCounts) }}</span>
                                @endif
                            </label>

                            <input type="radio" class="btn-check" name="status" id="pending" value="pending"
                                   {{ request('status') == 'pending' ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="btn btn-outline-pink" for="pending">
                                Chờ xử lý
                                @if(isset($orderCounts['pending']))
                                    <span class="badge bg-warning ms-1">{{ $orderCounts['pending'] }}</span>
                                @endif
                            </label>

                            <input type="radio" class="btn-check" name="status" id="processing" value="processing"
                                   {{ request('status') == 'processing' ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="btn btn-outline-pink" for="processing">
                                Đã xác nhận
                                @if(isset($orderCounts['processing']))
                                    <span class="badge bg-info ms-1">{{ $orderCounts['processing'] }}</span>
                                @endif
                            </label>

                            <input type="radio" class="btn-check" name="status" id="shipped" value="shipped"
                                   {{ request('status') == 'shipped' ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="btn btn-outline-pink" for="shipped">
                                Đang giao
                                @if(isset($orderCounts['shipped']))
                                    <span class="badge bg-primary ms-1">{{ $orderCounts['shipped'] }}</span>
                                @endif
                            </label>

                            <input type="radio" class="btn-check" name="status" id="completed" value="completed"
                                   {{ request('status') == 'completed' ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="btn btn-outline-pink" for="completed">
                                Hoàn thành
                                @if(isset($orderCounts['completed']))
                                    <span class="badge bg-success ms-1">{{ $orderCounts['completed'] }}</span>
                                @endif
                            </label>

                            <input type="radio" class="btn-check" name="status" id="cancelled" value="cancelled"
                                   {{ request('status') == 'cancelled' ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="btn btn-outline-pink" for="cancelled">
                                Đã hủy
                                @if(isset($orderCounts['cancelled']))
                                    <span class="badge bg-danger ms-1">{{ $orderCounts['cancelled'] }}</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                   placeholder="Tìm mã đơn hàng..." value="{{ request('search') }}">
                            <button class="btn btn-pink" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if($orders && $orders->count() > 0)
            <div class="row g-4">
                @foreach($orders as $order)
                    <div class="col-12">
                        <div class="card shadow-sm rounded-4 p-4 hover-shadow">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-1">Đơn hàng #{{ $order->id }}</h5>
                                    <small class="text-muted">
                                        Đặt ngày: {{ $order->created_at->format('d/m/Y') }} lúc {{ $order->created_at->format('H:i') }}
                                        @if($order->tracking_number)
                                            • Mã vận đơn: <span class="fw-bold">{{ $order->tracking_number }}</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="text-end">
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning text-dark fs-6">{{ $order->status_label }}</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info fs-6">{{ $order->status_label }}</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="badge bg-primary fs-6">{{ $order->status_label }}</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge bg-success fs-6">{{ $order->status_label }}</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger fs-6">{{ $order->status_label }}</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">{{ $order->status_label }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    @php $firstItem = $order->orderItems->first(); @endphp
                                    @if($firstItem && $firstItem->product)
                                        <img src="{{ $firstItem->product->main_image_url ?? asset('images/default-product.png') }}"
                                             class="img-fluid rounded border"
                                             alt="{{ $firstItem->product->name }}"
                                             style="height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="bi bi-image text-muted fs-3"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($firstItem && $firstItem->product)
                                        <h6 class="mb-1">{{ $firstItem->product->name }}</h6>
                                        <p class="text-muted mb-1 small">
                                            @if($firstItem->variant)
                                                @if($firstItem->variant->color) Màu sắc: {{ $firstItem->variant->color }} @endif
                                                @if($firstItem->variant->volume) • Dung tích: {{ $firstItem->variant->volume }} @endif
                                            @endif
                                            • Số lượng: {{ $firstItem->quantity }}
                                        </p>
                                        @if($order->orderItems->count() > 1)
                                            <p class="text-muted mb-0 small">+ {{ $order->orderItems->count() - 1 }} sản phẩm khác</p>
                                        @endif
                                    @else
                                        <h6 class="mb-1 text-muted">Sản phẩm không tồn tại</h6>
                                    @endif

                                    @if($order->status == 'completed')
                                        <p class="mb-0 mt-2">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                Đã giao thành công
                                                @if($order->payment && $order->payment->payment_method === 'cod')
                                                    • Đã thanh toán COD
                                                @endif
                                            </small>
                                        </p>
                                    @elseif($order->status == 'shipped')
                                        <p class="mb-0 mt-2">
                                            <small class="text-info">
                                                <i class="bi bi-truck me-1"></i>
                                                Đơn hàng đang được giao đến bạn
                                            </small>
                                        </p>
                                    @elseif($order->status == 'processing')
                                        <p class="mb-0 mt-2">
                                            <small class="text-primary">
                                                <i class="bi bi-gear me-1"></i>
                                                @if($order->payment && in_array($order->payment->payment_method, ['vnpay', 'bank_transfer']) && $order->payment->payment_status === 'completed')
                                                    Đã thanh toán - Đang chuẩn bị hàng
                                                @else
                                                    Đang xử lý đơn hàng
                                                @endif
                                            </small>
                                        </p>
                                    @elseif($order->status == 'cancelled')
                                        <p class="mb-0 mt-2">
                                            <small class="text-danger">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Đơn hàng đã bị hủy
                                            </small>
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1"><strong>Tổng tiền:</strong></p>
                                    <p class="text-pink fw-bold fs-5 mb-0">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>

                                    @if($order->payment)
                                        <small class="text-muted">
                                            @if($order->payment->payment_status === 'pending')
                                                <i class="bi bi-clock text-warning"></i> Chờ thanh toán
                                            @elseif($order->payment->payment_status === 'completed')
                                                <i class="bi bi-check-circle text-success"></i> Đã thanh toán
                                            @elseif($order->payment->payment_status === 'failed')
                                                <i class="bi bi-x-circle text-danger"></i> Thanh toán thất bại
                                            @endif
                                        </small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-end">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-pink btn-sm mb-2 w-100">
                                        @if($order->status == 'shipped')
                                            <i class="bi bi-truck me-1"></i>Theo dõi
                                        @else
                                            <i class="bi bi-eye me-1"></i>Xem chi tiết
                                        @endif
                                    </a>

                                    @if($order->canBeCancelled())
                                        <button class="btn btn-outline-danger btn-sm w-100"
                                                onclick="confirmCancelOrder({{ $order->id }}, '{{ $order->payment ? $order->payment->payment_method : 'none' }}', '{{ $order->payment ? $order->payment->payment_status : 'none' }}', '{{ $order->shipping ? $order->shipping->shipping_status : 'pending' }}')">
                                            <i class="bi bi-x-circle me-1"></i>Hủy đơn
                                        </button>
                                    @elseif($order->status == 'completed')
                                        <a href="{{ route('orders.show', $order->id) }}#review-section" class="btn btn-outline-warning btn-sm w-100">
                                            <i class="bi bi-star me-1"></i>Đánh giá
                                        </a>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm w-100" onclick="alert('Hotline: 1900 123 456\nEmail: support@yourstore.com')">
                                            <i class="bi bi-headset me-1"></i>Liên hệ
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="border-top pt-3 mt-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        @if($order->status == 'shipped')
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 75%"></div>
                                            </div>
                                            <small class="text-muted">Đang giao hàng • Dự kiến giao sớm</small>
                                        @else
                                            <small class="text-muted">
                                                <i class="bi bi-truck me-1"></i>
                                                @if($order->shipping)
                                                    {{ $order->shipping->shipping_method == 'standard' ? 'Giao hàng tiêu chuẩn' : 'Giao hàng nhanh' }}
                                                @endif
                                                @if($order->status == 'completed')
                                                    • Đã giao thành công
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <small class="text-muted">
                                            Thanh toán:
                                            @if($order->payment)
                                                @if($order->payment->payment_method == 'cod')
                                                    <span class="badge bg-warning text-dark">COD</span>
                                                @elseif($order->payment->payment_method == 'bank_transfer')
                                                    <span class="badge bg-info">Chuyển khoản</span>
                                                @elseif($order->payment->payment_method == 'vnpay')
                                                    <span class="badge bg-primary">VNPay</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->payment->payment_method }}</span>
                                                @endif
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>



            <div class="mt-5">
                @include('user.layouts.pagination', ['paginator' => $orders, 'itemName' => 'đơn hàng'])
            </div>
        @else
            <div class="text-center py-5">
                <div class="card shadow-sm rounded-4 p-5">
                    <i class="bi bi-cart3 text-muted" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold mt-3 mb-2">
                        @if(request('search'))
                            Không tìm thấy đơn hàng nào
                        @elseif(request('status') && request('status') != 'all')
                            Không có đơn hàng {{ request('status') }}
                        @else
                            Chưa có đơn hàng nào
                        @endif
                    </h4>
                    <p class="text-muted mb-4">
                        @if(request('search'))
                            Không tìm thấy đơn hàng với từ khóa "{{ request('search') }}". Hãy thử từ khóa khác.
                        @elseif(request('status') && request('status') != 'all')
                            Bạn chưa có đơn hàng nào ở trạng thái này.
                        @else
                            Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!
                        @endif
                    </p>
                    @if(request('search') || (request('status') && request('status') != 'all'))
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-pink me-2">
                            <i class="bi bi-arrow-left me-2"></i>Xem tất cả đơn hàng
                        </a>
                    @endif
                    <a href="{{ route('products.index') }}" class="btn btn-pink btn-lg">
                        <i class="bi bi-cart-plus me-2"></i>Bắt đầu mua sắm
                    </a>
                </div>
            </div>
        @endif

        @if(isset($orderCounts) && array_sum($orderCounts) > 0)
            <div class="row mt-5">
                <div class="col-12 mb-3">
                    <h5 class="text-center text-muted">Thống kê đơn hàng</h5>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center p-3 border-warning">
                        <h4 class="text-warning mb-1">{{ $orderCounts['pending'] ?? 0 }}</h4>
                        <small class="text-muted">Chờ xử lý</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center p-3 border-info">
                        <h4 class="text-info mb-1">{{ $orderCounts['shipped'] ?? 0 }}</h4>
                        <small class="text-muted">Đang giao</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center p-3 border-success">
                        <h4 class="text-success mb-1">{{ $orderCounts['completed'] ?? 0 }}</h4>
                        <small class="text-muted">Hoàn thành</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center p-3 border-danger">
                        <h4 class="text-danger mb-1">{{ $orderCounts['cancelled'] ?? 0 }}</h4>
                        <small class="text-muted">Đã hủy</small>
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
        .btn-outline-pink:hover, .btn-outline-pink:active, .btn-outline-pink.active {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
            color: white;
        }

        /* Hover effect cho cards */
        .hover-shadow {
            transition: box-shadow 0.15s ease-in-out;
        }
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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

        /* Custom pagination styles */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: #ec8ca3;
            border-color: #dee2e6;
        }

        .page-link:hover {
            color: #e07a96;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .page-item.active .page-link {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
        }

        @media (max-width: 576px) {
            .pagination-wrapper {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
@endsection
