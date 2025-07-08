@extends('admin.layouts.master')

@section('title', 'Sửa vận chuyển')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm rounded-4 p-4">
            <h4 class="fw-bold text-center text-pink mb-4">Cập nhật thông tin vận chuyển #{{ $shipping->id }}</h4>

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
                    @if($shipping->order && $shipping->order->payment && in_array($shipping->order->payment->payment_method, ['vnpay', 'bank_transfer']) && $shipping->order->payment->payment_status !== 'completed')
                        <div class="alert alert-danger border-danger">
                            <h6 class="alert-heading mb-2">
                                <i class="bi bi-x-circle me-2"></i>
                                Không thể vận chuyển
                            </h6>
                            <p class="mb-0">
                                Đơn hàng này chưa được thanh toán. Vui lòng xác nhận thanh toán trước khi cập nhật trạng thái vận chuyển.
                            </p>
                        </div>
                    @endif
                    <h6 class="card-title text-muted mb-3">Thông tin vận chuyển hiện tại:</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">ID Đơn hàng:</small><br>
                            <span class="fw-semibold">#{{ $shipping->order_id }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Phương thức:</small><br>
                            @switch($shipping->shipping_method)
                                @case('standard')
                                    <span class="badge bg-info">Tiêu chuẩn</span>
                                    @break
                                @case('express')
                                    <span class="badge bg-warning text-dark">Nhanh</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_method) }}</span>
                            @endswitch
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Trạng thái hiện tại:</small><br>
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
                                @case('failed')
                                    <span class="badge bg-danger">Giao hàng thất bại</span>
                                    @break
                                @case('returned')
                                    <span class="badge bg-secondary">Hàng trả lại</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($shipping->shipping_status) }}</span>
                            @endswitch
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Phí vận chuyển:</small><br>
                            @if($shipping->shipping_fee && $shipping->shipping_fee > 0)
                                <span class="fw-bold text-success">{{ number_format($shipping->shipping_fee) }}đ</span>
                            @else
                                <span class="badge bg-success">Miễn phí</span>
                            @endif
                        </div>
                    </div>
                    @if($shipping->shipping_address)
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <small class="text-muted">Địa chỉ giao hàng:</small><br>
                                <span class="fw-semibold">{{ $shipping->shipping_address }}</span>
                                @if($shipping->ward_name || $shipping->district_name || $shipping->province_name)
                                    <br><small class="text-muted">
                                        {{ $shipping->ward_name }}, {{ $shipping->district_name }}, {{ $shipping->province_name }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($shipping->ghn_province_id || $shipping->ghn_district_id || $shipping->ghn_ward_code)
                <div class="card bg-success bg-opacity-10 border-success mb-4">
                    <div class="card-body p-3">
                        <h6 class="card-title text-success mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Thông tin địa chỉ GHN API
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Tỉnh:</small><br>
                                <span class="fw-semibold">{{ $shipping->province_name }}</span>
                                <small class="text-muted">(ID: {{ $shipping->ghn_province_id }})</small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Quận:</small><br>
                                <span class="fw-semibold">{{ $shipping->district_name }}</span>
                                <small class="text-muted">(ID: {{ $shipping->ghn_district_id }})</small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Phường:</small><br>
                                <span class="fw-semibold">{{ $shipping->ward_name }}</span>
                                <small class="text-muted">(Code: {{ $shipping->ghn_ward_code }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Chưa có thông tin GHN:</strong>
                    Đơn hàng này chưa được tích hợp với API Giao Hàng Nhanh.
                    Để sử dụng tính năng tính phí và theo dõi vận chuyển từ GHN,
                    vui lòng tạo đơn hàng mới với form địa chỉ GHN.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.shippings.update', $shipping->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng thái vận chuyển <span class="text-danger">*</span></label>
                            <select name="shipping_status" class="form-select" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="pending" {{ old('shipping_status', $shipping->shipping_status) == 'pending' ? 'selected' : '' }}>
                                    Chờ xác nhận
                                </option>
                                <option value="confirmed" {{ old('shipping_status', $shipping->shipping_status) == 'confirmed' ? 'selected' : '' }}>
                                    Đã xác nhận
                                </option>
                                <option value="shipping" {{ old('shipping_status', $shipping->shipping_status) == 'shipping' ? 'selected' : '' }}>
                                    Đang giao hàng
                                </option>
                                <option value="delivered" {{ old('shipping_status', $shipping->shipping_status) == 'delivered' ? 'selected' : '' }}>
                                    Đã giao hàng
                                </option>
                                <option value="failed" {{ old('shipping_status', $shipping->shipping_status) == 'failed' ? 'selected' : '' }}>
                                    Giao hàng thất bại
                                </option>
                                <option value="returned" {{ old('shipping_status', $shipping->shipping_status) == 'returned' ? 'selected' : '' }}>
                                    Hàng trả lại
                                </option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Cập nhật trạng thái theo tiến độ giao hàng thực tế
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phương thức vận chuyển</label>
                            <select name="shipping_method" class="form-select">
                                <option value="standard" {{ old('shipping_method', $shipping->shipping_method) == 'standard' ? 'selected' : '' }}>
                                    Giao hàng tiêu chuẩn (2-3 ngày)
                                </option>
                                <option value="express" {{ old('shipping_method', $shipping->shipping_method) == 'express' ? 'selected' : '' }}>
                                    Giao hàng nhanh (1 ngày) - +30,000đ
                                </option>
                            </select>
                            <div class="form-text">Thay đổi phương thức vận chuyển nếu cần thiết</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã vận đơn</label>
                            <input type="text" class="form-control" name="tracking_code"
                                   value="{{ old('tracking_code', $shipping->tracking_code) }}"
                                   placeholder="Nhập mã vận đơn từ đơn vị vận chuyển">
                            <div class="form-text">Mã theo dõi từ đơn vị vận chuyển (VNPost, Giao hàng nhanh, Viettel Post...)</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phí vận chuyển (đ)</label>
                            <input type="number" class="form-control" name="shipping_fee"
                                   value="{{ old('shipping_fee', $shipping->shipping_fee) }}"
                                   placeholder="0" min="0" step="1000">
                            <div class="form-text">Để trống hoặc 0 nếu miễn phí vận chuyển</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ giao hàng</label>
                            <input type="text" class="form-control" name="shipping_address"
                                   value="{{ old('shipping_address', $shipping->shipping_address) }}"
                                   placeholder="Địa chỉ chi tiết">
                            <div class="form-text">Cập nhật địa chỉ giao hàng nếu có thay đổi</div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="ghn_province_id" value="{{ $shipping->ghn_province_id }}">
                <input type="hidden" name="ghn_district_id" value="{{ $shipping->ghn_district_id }}">
                <input type="hidden" name="ghn_ward_code" value="{{ $shipping->ghn_ward_code }}">
                <input type="hidden" name="province_name" value="{{ $shipping->province_name }}">
                <input type="hidden" name="district_name" value="{{ $shipping->district_name }}">
                <input type="hidden" name="ward_name" value="{{ $shipping->ward_name }}">

                <div id="status-specific-fields">
                    <div class="alert alert-info" id="pending-info" style="display: none;">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Trạng thái "Chờ xác nhận":</strong> Đơn hàng mới được tạo, chờ xác nhận từ bộ phận vận chuyển.
                    </div>

                    <div class="alert alert-primary" id="confirmed-info" style="display: none;">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Trạng thái "Đã xác nhận":</strong> Đơn hàng đã được xác nhận và chuẩn bị giao hàng.
                    </div>

                    <div class="alert alert-warning" id="shipping-info" style="display: none;">
                        <i class="bi bi-truck me-2"></i>
                        <strong>Trạng thái "Đang giao hàng":</strong> Hàng đã được giao cho đơn vị vận chuyển và đang trên đường giao đến khách hàng.
                        <br><small>Nhớ cập nhật mã vận đơn để khách hàng có thể theo dõi.</small>
                    </div>

                    <div class="alert alert-success" id="delivered-info" style="display: none;">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Trạng thái "Đã giao hàng":</strong> Hàng đã được giao thành công đến khách hàng.
                        <br><small>Trạng thái này sẽ tự động cập nhật trạng thái đơn hàng thành "Hoàn thành".</small>
                    </div>

                    <div class="alert alert-danger" id="failed-info" style="display: none;">
                        <i class="bi bi-x-circle me-2"></i>
                        <strong>Trạng thái "Giao hàng thất bại":</strong> Không thể giao hàng đến khách hàng (địa chỉ sai, không có người nhận, từ chối nhận...).
                        <br><small>Cần liên hệ khách hàng để xác nhận thông tin và sắp xếp giao hàng lại.</small>
                    </div>

                    <div class="alert alert-warning" id="returned-info" style="display: none;">
                        <i class="bi bi-arrow-return-left me-2"></i>
                        <strong>Trạng thái "Hàng trả lại":</strong> Hàng đã được trả lại từ khách hàng hoặc từ đơn vị vận chuyển.
                        <br><small>Cần xử lý hoàn tiền hoặc giao hàng lại tùy theo chính sách.</small>
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">
                        <i class="bi bi-check-circle me-1"></i>Cập nhật vận chuyển
                    </button>
                    <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.querySelector('select[name="shipping_status"]');
            const statusInfos = {
                'pending': document.getElementById('pending-info'),
                'confirmed': document.getElementById('confirmed-info'),
                'shipping': document.getElementById('shipping-info'),
                'delivered': document.getElementById('delivered-info'),
                'failed': document.getElementById('failed-info'),
                'returned': document.getElementById('returned-info')
            };

            function updateStatusInfo() {
                Object.values(statusInfos).forEach(info => {
                    if (info) info.style.display = 'none';
                });

                const selectedStatus = statusSelect.value;
                if (statusInfos[selectedStatus]) {
                    statusInfos[selectedStatus].style.display = 'block';
                }
            }

            updateStatusInfo();
            statusSelect.addEventListener('change', updateStatusInfo);
        });
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
    </style>
@endsection
