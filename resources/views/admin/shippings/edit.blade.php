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

            {{-- Thông tin vận chuyển hiện tại --}}
            <div class="card bg-light border-0 mb-4">
                <div class="card-body p-3">
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
                                    <span class="badge bg-warning">Chờ giao hàng</span>
                                    @break
                                @case('shipped')
                                    <span class="badge bg-primary">Đang giao hàng</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                    @break
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
                            </div>
                        </div>
                    @endif
                </div>
            </div>

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
                                    Chờ giao hàng
                                </option>
                                <option value="shipped" {{ old('shipping_status', $shipping->shipping_status) == 'shipped' ? 'selected' : '' }}>
                                    Đang giao hàng
                                </option>
                                <option value="delivered" {{ old('shipping_status', $shipping->shipping_status) == 'delivered' ? 'selected' : '' }}>
                                    Đã giao hàng
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
                                    Giao hàng tiêu chuẩn
                                </option>
                                <option value="express" {{ old('shipping_method', $shipping->shipping_method) == 'express' ? 'selected' : '' }}>
                                    Giao hàng nhanh
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tỉnh/Thành phố</label>
                            <input type="text" class="form-control" name="province"
                                   value="{{ old('province', $shipping->province) }}"
                                   placeholder="Ví dụ: Hà Nội, TP.HCM, Đà Nẵng...">
                            <div class="form-text">Tỉnh/thành phố giao hàng</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ giao hàng</label>
                            <input type="text" class="form-control" name="shipping_address"
                                   value="{{ old('shipping_address', $shipping->shipping_address) }}"
                                   placeholder="Địa chỉ chi tiết">
                            <div class="form-text">Cập nhật địa chỉ giao hàng nếu có thay đổi</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ghi chú vận chuyển</label>
                    <textarea class="form-control" name="shipping_note" rows="3"
                              placeholder="Nhập ghi chú về quá trình vận chuyển...">{{ old('shipping_note', $shipping->shipping_note) }}</textarea>
                    <div class="form-text">
                        Ví dụ: "Đã giao cho bưu tá", "Khách yêu cầu giao vào buổi chiều", "Gọi trước khi giao"
                    </div>
                </div>

                {{-- Thông tin bổ sung dựa trên trạng thái --}}
                <div id="status-specific-fields">
                    <div class="alert alert-info" id="pending-info" style="display: none;">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Trạng thái "Chờ giao hàng":</strong> Đơn hàng đã được chuẩn bị và đang chờ đơn vị vận chuyển đến lấy hàng.
                    </div>

                    <div class="alert alert-warning" id="shipped-info" style="display: none;">
                        <i class="bi bi-truck me-2"></i>
                        <strong>Trạng thái "Đang giao hàng":</strong> Hàng đã được giao cho đơn vị vận chuyển và đang trên đường giao đến khách hàng.
                        <br><small>Nhớ cập nhật mã vận đơn để khách hàng có thể theo dõi.</small>
                    </div>

                    <div class="alert alert-success" id="delivered-info" style="display: none;">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Trạng thái "Đã giao hàng":</strong> Hàng đã được giao thành công đến khách hàng.
                        <br><small>Trạng thái này sẽ tự động cập nhật trạng thái đơn hàng thành "Hoàn thành".</small>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Lưu ý:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Việc thay đổi trạng thái vận chuyển có thể ảnh hưởng đến trạng thái đơn hàng</li>
                        <li>Khi đánh dấu "Đã giao hàng", hệ thống sẽ tự động thông báo cho khách hàng</li>
                        <li>Mã vận đơn giúp khách hàng theo dõi tình trạng giao hàng</li>
                    </ul>
                </div>

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">
                        Cập nhật vận chuyển
                    </button>
                    <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.querySelector('select[name="shipping_status"]');
            const pendingInfo = document.getElementById('pending-info');
            const shippedInfo = document.getElementById('shipped-info');
            const deliveredInfo = document.getElementById('delivered-info');

            function updateStatusInfo() {
                // Ẩn tất cả thông tin
                pendingInfo.style.display = 'none';
                shippedInfo.style.display = 'none';
                deliveredInfo.style.display = 'none';

                // Hiển thị thông tin tương ứng
                const selectedStatus = statusSelect.value;
                if (selectedStatus === 'pending') {
                    pendingInfo.style.display = 'block';
                } else if (selectedStatus === 'shipped') {
                    shippedInfo.style.display = 'block';
                } else if (selectedStatus === 'delivered') {
                    deliveredInfo.style.display = 'block';
                }
            }

            // Cập nhật khi trang load
            updateStatusInfo();

            // Cập nhật khi thay đổi trạng thái
            statusSelect.addEventListener('change', updateStatusInfo);
        });
    </script>
@endsection
