@extends('user.layouts.master')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success animate-bounce" style="font-size: 5rem;"></i>
            </div>
            <h2 class="fw-bold text-success mb-3">🎉 Đặt hàng thành công!</h2>
            <p class="text-muted">Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4" style="color: #ec8ca3;">📋 Thông tin đơn hàng</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                            <p><strong>Mã tracking:</strong> {{ $order->tracking_number }}</p>
                            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tổng tiền:</strong> <span class="text-success fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></p>
                            <p><strong>Phương thức thanh toán:</strong>
                                @if($order->payment->payment_method === 'cod')
                                    <span class="badge bg-info">Thanh toán khi nhận hàng (COD)</span>
                                @elseif($order->payment->payment_method === 'vnpay')
                                    <span class="badge bg-primary">VNPay</span>
                                @elseif($order->payment->payment_method === 'bank_transfer')
                                    <span class="badge bg-warning">Chuyển khoản ngân hàng</span>
                                @endif
                            </p>
                            <p><strong>Trạng thái:</strong>
                                @if($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                @elseif($order->status === 'processing')
                                    <span class="badge bg-info">Đang xử lý</span>
                                @else
                                    <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($order->payment->payment_method === 'cod')
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-coin fs-3 text-warning me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-2">💰 Thanh toán khi nhận hàng (COD)</h6>
                                    <p class="mb-1">Bạn sẽ thanh toán <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong> khi nhận hàng.</p>
                                    <small class="text-muted">
                                        ✅ Vui lòng chuẩn bị đúng số tiền và kiểm tra hàng trước khi thanh toán.<br>
                                        📞 Shipper sẽ liên hệ với bạn trước khi giao hàng.
                                    </small>
                                </div>
                            </div>
                        </div>
                    @elseif($order->payment->payment_method === 'bank_transfer')
                        @if($order->payment->payment_status === 'completed')
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">🏦 Chuyển khoản thành công!</h6>
                                        <p class="mb-0">
                                            ✅ Chúng tôi đã nhận được chuyển khoản của bạn.<br>
                                            📦 Đơn hàng sẽ được chuẩn bị và giao đến bạn sớm nhất có thể.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bank fs-3 text-primary me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">🏦 Chuyển khoản ngân hàng</h6>
                                        <p class="mb-2">
                                            ⏰ Vui lòng chuyển khoản trong vòng <strong>24 giờ</strong> để giữ đơn hàng.<br>
                                            💡 Sau khi chuyển khoản, đơn hàng sẽ được xử lý trong vòng 1-2 giờ.
                                        </p>
                                        <a href="{{ route('order.bank-transfer', $order->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-bank me-1"></i>Xem thông tin chuyển khoản
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif($order->payment->payment_method === 'vnpay')
                        @if($order->payment->payment_status === 'completed')
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">💳 Thanh toán VNPay thành công!</h6>
                                        <p class="mb-0">
                                            ✅ Giao dịch đã được xử lý thành công qua VNPay.<br>
                                            📦 Đơn hàng sẽ được chuẩn bị và giao đến bạn sớm nhất.<br>
                                            📱 Chúng tôi sẽ gửi thông báo cập nhật trạng thái đơn hàng qua SMS/Email.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-history fs-3 text-warning me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">💳 Đang xử lý thanh toán VNPay</h6>
                                        <p class="mb-1">🔄 Hệ thống đang xác nhận giao dịch từ VNPay...</p>
                                        <small class="text-muted">
                                            Nếu đã thanh toán mà chưa cập nhật, vui lòng liên hệ hỗ trợ: <strong>1900 123 456</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @php
                        $metadata = json_decode($order->note, true) ?? [];
                        $customerNote = $metadata['__customer_note__'] ?? '';
                    @endphp
                    @if($customerNote)
                        <div class="alert alert-light mt-3">
                            <h6 class="fw-bold mb-2"><i class="bi bi-sticky me-2"></i>Ghi chú đơn hàng:</h6>
                            <p class="mb-0 text-muted">{{ $customerNote }}</p>
                        </div>
                    @endif
                </div>

                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4" style="color: #ec8ca3;">📦 Sản phẩm đã đặt</h5>

                    @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="{{ $item->product->main_image_url ?? asset('images/default-product.png') }}"
                                 class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;"
                                 alt="{{ $item->product->name }}">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                @if($item->variant)
                                    <small class="text-muted">
                                        @if($item->variant->color) Màu: {{ $item->variant->color }} @endif
                                        @if($item->variant->volume) - {{ $item->variant->volume }} @endif
                                    </small>
                                @endif
                                <div class="d-flex justify-content-between mt-2">
                                    <span>SL: {{ $item->quantity }}</span>
                                    <span class="fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3" style="color: #ec8ca3;">🚚 Thông tin giao hàng</h5>

                    @if($order->shipping)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Phương thức:</strong>
                                    @if($order->shipping->shipping_method == 'standard')
                                        Giao hàng tiêu chuẩn (2-3 ngày)
                                    @else
                                        Giao hàng nhanh (1 ngày)
                                    @endif
                                </p>
                                <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping->shipping_fee, 0, ',', '.') }}₫</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Địa chỉ giao hàng:</strong></p>
                                <p class="text-muted">{{ $order->shipping->shipping_address }}</p>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    <strong>Lưu ý quan trọng:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>📱 Vui lòng để ý điện thoại, shipper sẽ liên hệ trước khi giao</li>
                                        <li>🏠 Đảm bảo có người nhận hàng tại địa chỉ giao hàng</li>
                                        <li>📋 Kiểm tra hàng hóa trước khi thanh toán (với COD)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3" style="color: #ec8ca3;">🎯 Bước tiếp theo</h5>

                    <div class="row">
                        @if($order->payment->payment_method === 'bank_transfer' && $order->payment->payment_status === 'pending')
                            <div class="col-md-4 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-1-circle-fill text-primary fs-3"></i>
                                    <h6 class="mt-2">Chuyển khoản</h6>
                                    <small class="text-muted">Trong 24 giờ</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-2-circle-fill text-info fs-3"></i>
                                    <h6 class="mt-2">Xác nhận</h6>
                                    <small class="text-muted">1-2 giờ sau khi CK</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-3-circle-fill text-success fs-3"></i>
                                    <h6 class="mt-2">Giao hàng</h6>
                                    <small class="text-muted">2-3 ngày làm việc</small>
                                </div>
                            </div>
                        @elseif($order->payment->payment_method === 'bank_transfer' && $order->payment->payment_status === 'completed')
                            <div class="col-md-6 mb-3">
                                <div class="text-center p-3 border rounded bg-light-success">
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                    <h6 class="mt-2 text-success">Đã nhận chuyển khoản</h6>
                                    <small class="text-success">Thanh toán hoàn tất</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-truck text-primary fs-3"></i>
                                    <h6 class="mt-2">Chuẩn bị giao hàng</h6>
                                    <small class="text-muted">Trong 1-2 giờ tới</small>
                                </div>
                            </div>
                        @elseif($order->payment->payment_method === 'vnpay')
                            @if($order->payment->payment_status === 'completed')
                                <div class="col-md-6 mb-3">
                                    <div class="text-center p-3 border rounded bg-light-success">
                                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                        <h6 class="mt-2 text-success">Đã thanh toán VNPay</h6>
                                        <small class="text-success">Giao dịch thành công</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <i class="bi bi-truck text-primary fs-3"></i>
                                        <h6 class="mt-2">Chuẩn bị giao hàng</h6>
                                        <small class="text-muted">Trong 1-2 giờ tới</small>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <i class="bi bi-clock text-warning fs-3"></i>
                                        <h6 class="mt-2">Chờ xác nhận VNPay</h6>
                                        <small class="text-muted">Đang xử lý</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <i class="bi bi-2-circle text-muted fs-3"></i>
                                        <h6 class="mt-2">Xử lý đơn</h6>
                                        <small class="text-muted">Sau khi thanh toán</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <i class="bi bi-3-circle text-muted fs-3"></i>
                                        <h6 class="mt-2">Giao hàng</h6>
                                        <small class="text-muted">2-3 ngày</small>
                                    </div>
                                </div>
                            @endif
                        @else
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <i class="bi bi-gear text-info fs-3"></i>
                                <h6 class="mt-2">Chuẩn bị hàng</h6>
                                <small class="text-muted">1-2 giờ</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <i class="bi bi-truck text-primary fs-3"></i>
                                <h6 class="mt-2">Giao hàng & Thanh toán</h6>
                                <small class="text-muted">2-3 ngày</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary me-3">
                        <i class="bi bi-list-ul me-2"></i>Xem đơn hàng của tôi
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        Cần hỗ trợ? Liên hệ với chúng tôi qua:
                        <strong>Hotline: 1900 123 456</strong> |
                        <strong>Email: support@yourstore.com</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== SUCCESS PAGE SCRIPT LOADED ===');

            const urlParams = new URLSearchParams(window.location.search);
            const paymentConfirmed = urlParams.get('payment_confirmed');

            const sessionSuccess = '{{ session("success") }}';
            const isFromVNPay = sessionSuccess.includes('VNPay') || paymentConfirmed === '1';

            console.log('Payment confirmed:', paymentConfirmed);
            console.log('Session success:', sessionSuccess);
            console.log('Is from VNPay:', isFromVNPay);

            if (isFromVNPay) {
                console.log('🎉 User came from VNPay payment success');

                const successNotification = document.createElement('div');
                successNotification.className = 'alert alert-success position-fixed';
                successNotification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; animation: slideInRight 0.5s ease-out; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
                successNotification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                    <div>
                        <h6 class="mb-1">🎉 Thanh toán VNPay thành công!</h6>
                        <p class="mb-0">Đơn hàng của bạn đã được xác nhận và sẽ được xử lý ngay!</p>
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2" onclick="this.parentElement.remove()"></button>
            `;

                document.body.appendChild(successNotification);

                setTimeout(() => {
                    if (successNotification.parentElement) {
                        successNotification.remove();
                    }
                }, 8000);

                if (paymentConfirmed) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }

            const bankTransferConfirmed = urlParams.get('bank_transfer_confirmed');
            if (bankTransferConfirmed === '1') {
                const bankNotification = document.createElement('div');
                bankNotification.className = 'alert alert-success position-fixed';
                bankNotification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; animation: slideInRight 0.5s ease-out;';
                bankNotification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-bank fs-3 text-success me-3"></i>
                    <div>
                        <h6 class="mb-1">🏦 Chuyển khoản đã được xác nhận!</h6>
                        <p class="mb-0">Đơn hàng của bạn đang được xử lý và sẽ được giao sớm nhất.</p>
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2" onclick="this.parentElement.remove()"></button>
            `;

                document.body.appendChild(bankNotification);

                setTimeout(() => {
                    if (bankNotification.parentElement) {
                        bankNotification.remove();
                    }
                }, 8000);

                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

    <style>
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .animate-bounce {
            animation: bounce 2s infinite;
        }

        .text-pink {
            color: #ec8ca3 !important;
        }

        .alert {
            animation: slideInDown 0.5s ease-out;
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

        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .alert.alert-success {
            border-left: 4px solid #28a745;
        }

        .alert.alert-warning {
            border-left: 4px solid #ffc107;
        }

        .alert.alert-info {
            border-left: 4px solid #17a2b8;
        }

        .alert.alert-light {
            border-left: 4px solid #f8f9fa;
            background-color: #f8f9fa;
        }

        .bg-light-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }
    </style>
@endsection
