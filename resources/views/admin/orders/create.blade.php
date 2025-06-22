@extends('admin.layouts.master')

@section('title', 'Tạo đơn hàng mới')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Tạo đơn hàng mới</h4>

        <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
            @csrf

            <div class="row">
                {{-- Thông tin khách hàng --}}
                <div class="col-md-6">
                    <div class="card mb-4 shadow-sm rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>

                            <div class="mb-3">
                                <label for="customer_id" class="form-label fw-semibold">Chọn khách hàng *</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id" required>
                                    <option value="">-- Chọn khách hàng --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone ?? $customer->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shipping_address" class="form-label fw-semibold">Địa chỉ giao hàng *</label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror"
                                          id="shipping_address" name="shipping_address" rows="3"
                                          placeholder="Nhập địa chỉ giao hàng chi tiết..." required>{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shipping_method" class="form-label fw-semibold">Phương thức vận chuyển *</label>
                                <select class="form-select @error('shipping_method') is-invalid @enderror"
                                        id="shipping_method" name="shipping_method" required>
                                    <option value="">-- Chọn phương thức --</option>
                                    <option value="standard" {{ old('shipping_method') == 'standard' ? 'selected' : '' }}>Giao hàng tiêu chuẩn (2-3 ngày)</option>
                                    <option value="express" {{ old('shipping_method') == 'express' ? 'selected' : '' }}>Giao hàng nhanh (1 ngày)</option>
                                </select>
                                @error('shipping_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label fw-semibold">Phương thức thanh toán *</label>
                                <select class="form-select @error('payment_method') is-invalid @enderror"
                                        id="payment_method" name="payment_method" required>
                                    <option value="">-- Chọn phương thức --</option>
                                    <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                                    <option value="momo" {{ old('payment_method') == 'momo' ? 'selected' : '' }}>Ví điện tử MoMo</option>
                                </select>
                                @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sản phẩm --}}
                <div class="col-md-6">
                    <div class="card mb-4 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Sản phẩm</h5>
                                <button type="button" class="btn btn-outline-pink btn-sm" id="addOrderProduct">
                                    <i class="bi bi-plus"></i> Thêm sản phẩm
                                </button>
                            </div>

                            <div id="orderProductItems">
                                <div class="order-product-item mb-3 p-3 border rounded position-relative">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label fw-semibold">Chọn sản phẩm *</label>
                                            <select class="form-select order-product-select" name="items[0][product_id]" required>
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                            data-price="{{ $product->base_price ?? $product->price ?? 0 }}"
                                                            data-stock="{{ $product->inventory->quantity ?? $product->stock ?? 0 }}"
                                                            data-name="{{ $product->name }}">
                                                        {{ $product->name }} - {{ number_format($product->base_price ?? $product->price ?? 0, 0, ',', '.') }}đ
                                                        (Còn: {{ $product->inventory->quantity ?? $product->stock ?? 0 }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Số lượng *</label>
                                            <input type="number" class="form-control order-quantity-input"
                                                   name="items[0][quantity]" min="1" value="1" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Đơn giá</label>
                                            <input type="text" class="form-control order-item-price" readonly placeholder="0đ">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Thành tiền: <span class="order-item-subtotal fw-bold text-primary">0đ</span></small>
                                    </div>
                                </div>
                            </div>

                            @error('items')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tổng tiền --}}
            <div class="card mb-4 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="text-end">
                                <h4 class="fw-bold mb-0">Tổng tiền: <span id="orderTotalAmount" class="text-danger">0đ</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hiển thị lỗi nếu có --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <h6 class="fw-bold">Có lỗi xảy ra:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Thông báo --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Nút submit --}}
            <div class="text-end">
                <button type="submit" class="btn btn-pink me-2" id="orderSubmitBtn">
                    Tạo đơn hàng
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
@endsection
