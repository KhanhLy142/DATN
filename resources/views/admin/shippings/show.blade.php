@extends('admin.layouts.master')

@section('title', 'Chi tiết vận chuyển')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết vận chuyển</h4>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-truck-flatbed text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="card-title text-primary mb-0">Vận chuyển #{{ $shipping->id }}</h5>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-cart-fill me-2 text-info"></i>
                            <strong>ID Đơn hàng:</strong>
                            <span class="badge bg-info">#{{ $shipping->order_id }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                            <strong>Địa chỉ giao hàng:</strong>
                            <span class="fw-semibold">{{ $shipping->shipping_address }}</span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-speedometer2 me-2 text-warning"></i>
                            <strong>Phương thức vận chuyển:</strong>
                            @switch($shipping->shipping_method)
                                @case('standard')
                                    <span class="badge bg-info">Giao hàng tiêu chuẩn</span>
                                    @break
                                @case('express')
                                    <span class="badge bg-warning text-dark">Giao hàng nhanh</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_method) }}</span>
                            @endswitch
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-toggle-{{ $shipping->shipping_status === 'delivered' ? 'on' : 'off' }} me-2 text-{{ $shipping->shipping_status === 'delivered' ? 'success' : ($shipping->shipping_status === 'shipped' ? 'primary' : 'warning') }}"></i>
                            <strong>Trạng thái:</strong>
                            @switch($shipping->shipping_status)
                                @case('pending')
                                    <span class="badge bg-warning">Chờ giao hàng</span>
                                    @break
                                @case('shipped')
                                    <span class="badge bg-primary">Đang giao hàng</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_status) }}</span>
                            @endswitch
                        </p>
                    </div>
                </div>

                @if($shipping->tracking_code)
                    <p class="card-text">
                        <i class="bi bi-upc-scan me-2 text-info"></i>
                        <strong>Mã vận đơn:</strong>
                        <span class="badge bg-light text-dark border fw-bold" style="font-family: 'Courier New', monospace;">{{ $shipping->tracking_code }}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $shipping->tracking_code }}')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </p>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        @if($shipping->shipping_fee && $shipping->shipping_fee > 0)
                            <p class="card-text">
                                <i class="bi bi-cash-coin me-2 text-success"></i>
                                <strong>Phí vận chuyển:</strong>
                                <span class="fw-bold text-success fs-5">{{ number_format($shipping->shipping_fee) }}đ</span>
                            </p>
                        @else
                            <p class="card-text">
                                <i class="bi bi-gift me-2 text-success"></i>
                                <strong>Phí vận chuyển:</strong>
                                <span class="badge bg-success">Miễn phí</span>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($shipping->province)
                            <p class="card-text">
                                <i class="bi bi-map me-2 text-primary"></i>
                                <strong>Tỉnh/Thành phố:</strong>
                                <span class="fw-semibold">{{ $shipping->province }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                @if($shipping->shipping_note)
                    <div class="mb-3">
                        <p class="card-text">
                            <i class="bi bi-chat-text me-2 text-secondary"></i>
                            <strong>Ghi chú vận chuyển:</strong>
                        </p>
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                {{ $shipping->shipping_note }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-calendar-plus me-2 text-success"></i>
                            <strong>Ngày tạo:</strong> {{ $shipping->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-clock-history me-2 text-warning"></i>
                            <strong>Cập nhật lần cuối:</strong> {{ $shipping->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Thông tin đơn hàng liên quan --}}
                @if($shipping->order)
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="bi bi-box-seam me-2 text-info"></i>
                            <strong>Thông tin đơn hàng:</strong>
                        </p>
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Khách hàng:</small><br>
                                        <span class="fw-semibold">{{ $shipping->order->customer_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Trạng thái đơn hàng:</small><br>
                                        <span class="badge bg-info">{{ ucfirst($shipping->order->status ?? 'N/A') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Tổng tiền:</small><br>
                                        <span class="fw-bold text-success">{{ number_format($shipping->order->total ?? 0) }}đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Timeline trạng thái vận chuyển --}}
                <div class="mb-3">
                    <p class="mb-2">
                        <i class="bi bi-clock-history me-2 text-info"></i>
                        <strong>Lịch sử vận chuyển:</strong>
                    </p>
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-success rounded-circle p-2 mb-2">
                                            <i class="bi bi-plus-circle text-white"></i>
                                        </div>
                                        <small class="text-muted">Tạo đơn vận chuyển</small>
                                        <small class="fw-semibold">{{ $shipping->created_at->format('d/m H:i') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-{{ $shipping->shipping_status === 'shipped' || $shipping->shipping_status === 'delivered' ? 'success' : 'secondary' }} rounded-circle p-2 mb-2">
                                            <i class="bi bi-truck text-white"></i>
                                        </div>
                                        <small class="text-muted">Bắt đầu giao hàng</small>
                                        @if($shipping->shipping_status === 'shipped' || $shipping->shipping_status === 'delivered')
                                            <small class="fw-semibold">{{ $shipping->updated_at->format('d/m H:i') }}</small>
                                        @else
                                            <small class="text-muted">Chưa cập nhật</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-{{ $shipping->shipping_status === 'delivered' ? 'success' : 'secondary' }} rounded-circle p-2 mb-2">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                        <small class="text-muted">Giao hàng thành công</small>
                                        @if($shipping->shipping_status === 'delivered')
                                            <small class="fw-semibold">{{ $shipping->updated_at->format('d/m H:i') }}</small>
                                        @else
                                            <small class="text-muted">Chưa hoàn thành</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Đã sao chép mã vận đơn: ' + text);
            });
        }

        function trackingInfo(trackingCode) {
            alert('Chức năng tra cứu vận đơn sẽ được tích hợp với API của đơn vị vận chuyển.\nMã vận đơn: ' + trackingCode);
        }
    </script>
@endsection
