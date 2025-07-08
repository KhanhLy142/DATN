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

                @if($shipping->ghn_province_id || $shipping->ghn_district_id || $shipping->ghn_ward_code)
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="bi bi-geo-alt-fill me-2 text-success"></i>
                            <strong>Thông tin địa chỉ GHN:</strong>
                        </p>
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted">Mã tỉnh GHN:</small><br>
                                        <span class="fw-semibold">{{ $shipping->ghn_province_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Mã quận GHN:</small><br>
                                        <span class="fw-semibold">{{ $shipping->ghn_district_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Mã phường GHN:</small><br>
                                        <span class="fw-semibold">{{ $shipping->ghn_ward_code ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Trạng thái API:</small><br>
                                        @if($shipping->ghn_province_id && $shipping->ghn_district_id && $shipping->ghn_ward_code)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Sẵn sàng
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle"></i> Chưa đầy đủ
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small class="text-muted">Địa chỉ đầy đủ:</small><br>
                                        <span class="fw-semibold">
                                            {{ $shipping->shipping_address }}
                                            @if($shipping->ward_name || $shipping->district_name || $shipping->province_name)
                                                , {{ $shipping->ward_name }}
                                                , {{ $shipping->district_name }}
                                                , {{ $shipping->province_name }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Chưa có thông tin GHN:</strong>
                            Đơn hàng này chưa được tích hợp với API Giao Hàng Nhanh.
                            Cần cập nhật thông tin địa chỉ để sử dụng tính năng tính phí và theo dõi vận chuyển.
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-speedometer2 me-2 text-warning"></i>
                            <strong>Phương thức vận chuyển:</strong>
                            @switch($shipping->shipping_method)
                                @case('standard')
                                    <span class="badge bg-info">Giao hàng tiêu chuẩn (2-3 ngày)</span>
                                    @break
                                @case('express')
                                    <span class="badge bg-warning text-dark">Giao hàng nhanh (1 ngày)</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_method) }}</span>
                            @endswitch
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-toggle-{{ $shipping->shipping_status === 'delivered' ? 'on' : 'off' }} me-2 text-{{ $shipping->shipping_status === 'delivered' ? 'success' : ($shipping->shipping_status === 'shipping' ? 'primary' : ($shipping->shipping_status === 'confirmed' ? 'info' : 'warning')) }}"></i>
                            <strong>Trạng thái:</strong>
                            @switch($shipping->shipping_status)
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
                                @if($shipping->ghn_province_id && $shipping->ghn_district_id && $shipping->ghn_ward_code)
                                    <br><small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Tính từ API GHN
                                    </small>
                                @else
                                    <br><small class="text-muted">
                                        <i class="bi bi-calculator"></i> Phí cố định
                                    </small>
                                @endif
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
                        @if($shipping->province_name)
                            <p class="card-text">
                                <i class="bi bi-map me-2 text-primary"></i>
                                <strong>Tỉnh/Thành phố:</strong>
                                <span class="fw-semibold">{{ $shipping->province_name }}</span>
                                <span class="badge bg-success ms-1" style="font-size: 0.7em;">GHN</span>
                            </p>
                        @endif
                    </div>
                </div>

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
                                        <span class="fw-semibold">{{ $shipping->order->customer->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Trạng thái đơn hàng:</small><br>
                                        @switch($shipping->order->status ?? '')
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                @break
                                            @case('processing')
                                                <span class="badge bg-info">Đang xử lý</span>
                                                @break
                                            @case('shipped')
                                                <span class="badge bg-primary">Đã giao hàng</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">Đã nhận hàng</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Đã hủy</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($shipping->order->status ?? 'N/A') }}</span>
                                        @endswitch
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Tổng tiền:</small><br>
                                        <span class="fw-bold text-success">{{ number_format($shipping->order->total_amount ?? 0) }}đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <p class="mb-2">
                        <i class="bi bi-clock-history me-2 text-info"></i>
                        <strong>Lịch sử vận chuyển:</strong>
                    </p>
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-success rounded-circle p-2 mb-2">
                                            <i class="bi bi-plus-circle text-white"></i>
                                        </div>
                                        <small class="text-muted">Tạo đơn vận chuyển</small>
                                        <small class="fw-semibold">{{ $shipping->created_at->format('d/m H:i') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-{{ in_array($shipping->shipping_status, ['confirmed', 'shipping', 'delivered']) ? 'success' : 'secondary' }} rounded-circle p-2 mb-2">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                        <small class="text-muted">Xác nhận đơn hàng</small>
                                        @if(in_array($shipping->shipping_status, ['confirmed', 'shipping', 'delivered']))
                                            <small class="fw-semibold">{{ $shipping->updated_at->format('d/m H:i') }}</small>
                                        @else
                                            <small class="text-muted">Chưa xác nhận</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-{{ in_array($shipping->shipping_status, ['shipping', 'delivered']) ? 'success' : 'secondary' }} rounded-circle p-2 mb-2">
                                            <i class="bi bi-truck text-white"></i>
                                        </div>
                                        <small class="text-muted">Bắt đầu giao hàng</small>
                                        @if(in_array($shipping->shipping_status, ['shipping', 'delivered']))
                                            <small class="fw-semibold">{{ $shipping->updated_at->format('d/m H:i') }}</small>
                                        @else
                                            <small class="text-muted">Chưa bắt đầu</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-{{ $shipping->shipping_status === 'delivered' ? 'success' : 'secondary' }} rounded-circle p-2 mb-2">
                                            <i class="bi bi-check-circle-fill text-white"></i>
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

                @if($shipping->shipping_status !== 'delivered')
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="bi bi-lightning me-2 text-warning"></i>
                            <strong>Hành động nhanh:</strong>
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($shipping->shipping_status == 'pending')
                                <form action="{{ route('admin.shippings.mark-shipped', $shipping->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info btn-sm"
                                            onclick="return confirm('Xác nhận đơn hàng này đã sẵn sàng giao?')">
                                        <i class="bi bi-check-circle me-1"></i>Xác nhận đơn hàng
                                    </button>
                                </form>
                            @elseif($shipping->shipping_status == 'confirmed')
                                <form action="{{ route('admin.shippings.mark-shipped', $shipping->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary btn-sm"
                                            onclick="return confirm('Bắt đầu giao hàng cho đơn này?')">
                                        <i class="bi bi-truck me-1"></i>Bắt đầu giao hàng
                                    </button>
                                </form>
                            @elseif($shipping->shipping_status == 'shipping')
                                <form action="{{ route('admin.shippings.mark-delivered', $shipping->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm"
                                            onclick="return confirm('Xác nhận đã giao hàng thành công?')">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đánh dấu đã giao
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.shippings.edit', $shipping->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil me-1"></i>Chỉnh sửa
                            </a>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Đã sao chép mã vận đơn: ' + text);
            }, function() {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Đã sao chép mã vận đơn: ' + text);
            });
        }

        function trackingInfo(trackingCode) {
            alert('Chức năng tra cứu vận đơn sẽ được tích hợp với API của đơn vị vận chuyển.\nMã vận đơn: ' + trackingCode);
        }
    </script>
@endsection
