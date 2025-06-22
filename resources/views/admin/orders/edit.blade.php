@extends('admin.layouts.master')

@section('title', 'Cập nhật trạng thái đơn hàng')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Cập nhật trạng thái đơn hàng #{{ $order->id }}</h4>

        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                {{-- Thông tin đơn hàng --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
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
                                <span class="badge bg-success">Đã giao hàng</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Form cập nhật trạng thái --}}
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
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã giao hàng</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin vận chuyển --}}
                    @if($order->shipping)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shipping_method" class="form-label fw-semibold">Phương thức vận chuyển</label>
                                    <select class="form-select" id="shipping_method" name="shipping_method">
                                        <option value="standard" {{ $order->shipping->shipping_method == 'standard' ? 'selected' : '' }}>Giao hàng tiêu chuẩn</option>
                                        <option value="express" {{ $order->shipping->shipping_method == 'express' ? 'selected' : '' }}>Giao hàng nhanh</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label fw-semibold">Địa chỉ giao hàng</label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3">{{ $order->shipping->shipping_address }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Hiển thị lỗi nếu có --}}
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
                        <button type="submit" class="btn btn-pink me-2">Cập nhật</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
