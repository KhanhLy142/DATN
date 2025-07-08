@extends('admin.layouts.master')

@section('title', 'Danh sách thanh toán')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách thanh toán</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm ID đơn hàng, mã giao dịch..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="payment_method" class="form-select">
                            <option value="">Tất cả phương thức</option>
                            <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
                            <option value="vnpay" {{ request('payment_method') === 'vnpay' ? 'selected' : '' }}>VNPay</option>
                            <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="payment_status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                            <option value="completed" {{ request('payment_status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="need_confirmation" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('need_confirmation') == '1' ? 'selected' : '' }}>
                                🔔 Cần xác nhận CK
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <div class="d-inline-block me-3">
                    <small class="text-muted">Tổng số giao dịch: <strong>{{ $payments->total() }}</strong></small>
                </div>
                <div class="d-inline-block">
                    <small class="text-muted">Tổng tiền: <strong>{{ number_format($totalAmount ?? 0) }}đ</strong></small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle payment-table">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ID Đơn hàng</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th class="text-center">Trạng thái</th>
                    <th>Mã giao dịch</th>
                    <th>Ngày tạo</th>
                    <th>Ghi chú</th>
                    <th class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($payments as $index => $payment)
                    <tr>
                        <td>{{ $payments->firstItem() + $index }}</td>
                        <td>
                            <strong>#{{ $payment->order_id }}</strong>
                        </td>
                        <td>
                            <span class="fw-bold text-success">{{ number_format($payment->amount) }}đ</span>
                        </td>
                        <td>
                            @switch($payment->payment_method)
                                @case('cod')
                                    <span class="badge bg-warning text-dark">COD</span>
                                    @break
                                @case('vnpay')
                                    <span class="badge bg-primary">VNPay</span>
                                    @break
                                @case('bank_transfer')
                                    <span class="badge bg-info">Chuyển khoản</span>
                                    @if($payment->payment_status === 'pending')
                                        <br><small class="text-warning fw-bold">🔔 Cần xác nhận</small>
                                    @endif
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                            @endswitch
                        </td>
                        <td class="text-center">
                            @switch($payment->payment_status)
                                @case('pending')
                                    <span class="badge bg-warning">Đang chờ</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                    @break
                                @case('refunded')
                                    <span class="badge bg-secondary">Đã hoàn tiền</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ ucfirst($payment->payment_status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($payment->vnpay_transaction_id)
                                <span class="badge bg-light text-dark">{{ $payment->vnpay_transaction_id }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($payment->payment_note)
                                <span title="{{ $payment->payment_note }}">
                            {{ Str::limit($payment->payment_note, 30) }}
                        </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td style="text-align: center; vertical-align: middle;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.payments.show', $payment->id) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($payment->payment_status === 'pending')
                                    <a href="{{ route('admin.payments.edit', $payment->id) }}"
                                       class="btn btn-outline-warning btn-sm"
                                       title="Cập nhật trạng thái">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Không có giao dịch thanh toán nào
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @include('admin.layouts.pagination', ['paginator' => $payments, 'itemName' => 'giao dịch'])
        </div>
    </div>
@endsection
