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

            {{-- Thông tin thanh toán hiện tại --}}
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
                                @case('momo')
                                    <span class="badge bg-primary">MoMo</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái thanh toán <span class="text-danger">*</span></label>
                    <select name="payment_status" class="form-select" required>
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
                    </select>
                    <div class="form-text">
                        @if($payment->payment_method === 'cod')
                            <i class="bi bi-info-circle me-1"></i>
                            COD: "Hoàn thành" khi khách đã thanh toán, "Thất bại" khi khách từ chối nhận hàng
                        @elseif($payment->payment_method === 'momo')
                            <i class="bi bi-info-circle me-1"></i>
                            MoMo: "Hoàn thành" khi đã nhận được tiền, "Thất bại" khi giao dịch lỗi
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ghi chú</label>
                    <textarea class="form-control" name="payment_note" rows="3" placeholder="Nhập lý do cập nhật trạng thái (tùy chọn)">{{ old('payment_note', $payment->payment_note) }}</textarea>
                    <div class="form-text">Ví dụ: "Khách đã thanh toán COD", "Giao dịch MoMo thành công", "Khách từ chối nhận hàng"</div>
                </div>

                @if($payment->payment_method === 'momo')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã giao dịch MoMo</label>
                        <input type="text" class="form-control" name="momo_transaction_id"
                               value="{{ old('momo_transaction_id', $payment->momo_transaction_id) }}"
                               placeholder="Nhập mã giao dịch MoMo (nếu có)">
                        <div class="form-text">Mã giao dịch từ ví MoMo để đối chiếu</div>
                    </div>
                @endif

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Lưu ý:</strong>
                    Việc thay đổi trạng thái thanh toán có thể ảnh hưởng đến trạng thái đơn hàng.
                    Vui lòng kiểm tra kỹ trước khi cập nhật.
                </div>

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">
                        Cập nhật
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
