@extends('user.layouts.master')

@section('title', 'Thanh toán đơn hàng')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đặt hàng</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <h2 class="fw-bold mb-4 text-center" style="color: #ec8ca3;">Thông tin đặt hàng</h2>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="fw-bold mb-2">Vui lòng kiểm tra lại thông tin:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('order.place') }}" id="checkoutForm">
            @csrf
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4" style="color: #ec8ca3;">📍 Thông tin giao hàng</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label fw-semibold">Họ và tên *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                       id="customer_name" name="customer_name"
                                       value="{{ old('customer_name', Auth::guard('customer')->user()->name ?? '') }}"
                                       placeholder="Nhập họ tên" required>
                                @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label fw-semibold">Số điện thoại *</label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                       id="customer_phone" name="customer_phone"
                                       value="{{ old('customer_phone', Auth::guard('customer')->user()->phone ?? '') }}"
                                       placeholder="0123 456 789" required>
                                @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                   id="customer_email" name="customer_email"
                                   value="{{ old('customer_email', Auth::guard('customer')->user()->email ?? '') }}"
                                   placeholder="email@example.com">
                            @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chọn địa chỉ giao hàng *</label>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-2">
                                    <select class="form-select @error('ghn_province_id') is-invalid @enderror"
                                            id="province_select" name="ghn_province_id" required>
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                    </select>
                                    @error('ghn_province_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select @error('ghn_district_id') is-invalid @enderror"
                                            id="district_select" name="ghn_district_id" disabled required>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                    @error('ghn_district_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select @error('ghn_ward_code') is-invalid @enderror"
                                            id="ward_select" name="ghn_ward_code" disabled required>
                                        <option value="">-- Chọn Phường/Xã --</option>
                                    </select>
                                    @error('ghn_ward_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <textarea class="form-control @error('shipping_address') is-invalid @enderror"
                                      id="shipping_address" name="shipping_address" rows="3"
                                      placeholder="Nhập địa chỉ chi tiết (số nhà, tên đường...)" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="alert alert-info mt-3" id="full_address_display" style="display: none;">
                                <h6 class="mb-1"><i class="bi bi-geo-alt-fill me-2"></i>Địa chỉ giao hàng:</h6>
                                <p class="mb-0 fw-semibold" id="full_address_text"></p>
                            </div>

                            <input type="hidden" id="province_name" name="province" value="{{ old('province') }}">
                            <input type="hidden" id="district_name" name="district" value="{{ old('district') }}">
                            <input type="hidden" id="ward_name" name="ward" value="{{ old('ward') }}">
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label fw-semibold">Ghi chú đơn hàng</label>
                            <textarea class="form-control" id="note" name="note" rows="2"
                                      placeholder="Ghi chú cho người giao hàng (tùy chọn)">{{ old('note') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phương thức vận chuyển *</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded shipping-option" data-method="standard">
                                        <input class="form-check-input" type="radio" name="shipping_method"
                                               id="standard" value="standard" {{ old('shipping_method', 'standard') == 'standard' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="standard">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>Giao hàng tiêu chuẩn</strong>
                                                    <br><small class="text-muted">2-3 ngày làm việc</small>
                                                </div>
                                                <span class="fw-bold text-success" id="standard_fee">30.000₫</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded shipping-option" data-method="express">
                                        <input class="form-check-input" type="radio" name="shipping_method"
                                               id="express" value="express" {{ old('shipping_method') == 'express' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="express">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>Giao hàng nhanh</strong>
                                                    <br><small class="text-muted">1 ngày làm việc</small>
                                                </div>
                                                <span class="fw-bold text-warning" id="express_fee">50.000₫</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 mb-4">
                    <div class="card shadow-sm rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-4" style="color: #ec8ca3;"> Đơn hàng của bạn</h5>

                        <div class="order-items mb-3">
                            @foreach($cartItems as $item)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <img src="{{ $item->product->main_image_url ?? asset('images/default-product.png') }}"
                                         class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;"
                                         alt="{{ $item->product->name }}">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                        @if($item->variant)
                                            <small class="text-muted">
                                                @if($item->variant->color) Màu: {{ $item->variant->color }} @endif
                                                @if($item->variant->volume) - {{ $item->variant->volume }} @endif
                                            </small>
                                        @endif
                                        <div class="d-flex justify-content-between">
                                            <span>SL: {{ $item->quantity }}</span>
                                            <span class="fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                            </div>

                            @if($appliedDiscount && $discount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Giảm giá ({{ $appliedDiscount['code'] }}):</span>
                                    <span class="text-success">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span id="shipping-fee-display">{{ number_format($finalShippingFee, 0, ',', '.') }}₫</span>
                            </div>

                            @if($appliedDiscount && isset($appliedDiscount['shipping_discount']) && $appliedDiscount['shipping_discount'] > 0)
                                <div class="d-flex justify-content-between mb-2" id="shipping-discount-line">
                                    <span>Giảm phí ship:</span>
                                    <span class="text-success" id="shipping-discount">-{{ number_format($appliedDiscount['shipping_discount'], 0, ',', '.') }}₫</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-2" id="shipping-discount-line" style="display: none !important;">
                                    <span>Giảm phí ship:</span>
                                    <span class="text-success" id="shipping-discount">-0₫</span>
                                </div>
                            @endif

                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Tổng cộng:</span>
                                <span style="color: #ec8ca3;" id="total-amount">{{ number_format($total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3" id="delivery-info" style="display: none;">
                            <h6 class="mb-1"><i class="bi bi-truck me-2"></i>Thông tin giao hàng:</h6>
                            <p class="mb-0" id="delivery-text"></p>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4" style="color: #ec8ca3;"> Phương thức thanh toán</h5>

                        <div class="payment-methods">
                            <div class="form-check mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="cod" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="cod">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-cash-coin me-3 fs-4 text-success"></i>
                                        <div>
                                            <strong>Thanh toán khi nhận hàng (COD)</strong>
                                            <br><small class="text-muted">Thanh toán bằng tiền mặt khi nhận hàng</small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="bank_transfer">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-bank me-3 fs-4 text-primary"></i>
                                        <div>
                                            <strong>Chuyển khoản ngân hàng</strong>
                                            <br><small class="text-muted">Chuyển khoản qua VietinBank</small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="vnpay" value="vnpay" {{ old('payment_method') == 'vnpay' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="vnpay">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card me-3 fs-4 text-primary"></i>
                                        <div>
                                            <strong>Cổng thanh toán VNPay</strong>
                                            <br><small class="text-muted">Thanh toán qua VNPay</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-lg w-100 mt-4" id="checkout-btn"
                                style="background-color: #ec8ca3; color: white; border: none; border-radius: 25px; font-weight: 600; padding: 12px 24px;">
                            <i class="bi bi-check-circle me-2"></i>Xác nhận đặt hàng
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Quay lại giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const GHN_CONFIG = {
                token: '316ea3e3-53fc-11f0-989d-42259a3f1d4c',
                baseUrl: 'https://online-gateway.ghn.vn/shiip/public-api/master-data/'
            };

            const provinceSelect = document.getElementById('province_select');
            const districtSelect = document.getElementById('district_select');
            const wardSelect = document.getElementById('ward_select');
            const shippingAddress = document.getElementById('shipping_address');
            const fullAddressDisplay = document.getElementById('full_address_display');
            const fullAddressText = document.getElementById('full_address_text');

            const provinceNameInput = document.getElementById('province_name');
            const districtNameInput = document.getElementById('district_name');
            const wardNameInput = document.getElementById('ward_name');

            const shippingFeeDisplay = document.getElementById('shipping-fee-display');
            const totalAmountElement = document.getElementById('total-amount');
            const standardFeeElement = document.getElementById('standard_fee');
            const expressFeeElement = document.getElementById('express_fee');
            const deliveryInfoElement = document.getElementById('delivery-info');
            const deliveryTextElement = document.getElementById('delivery-text');
            const checkoutBtn = document.getElementById('checkout-btn');
            const shippingDiscountLine = document.getElementById('shipping-discount-line');
            const shippingDiscountElement = document.getElementById('shipping-discount');


            let provinceData = [];
            let districtData = [];
            let wardData = [];
            let currentShippingFees = {
                standard: 30000,
                express: 50000
            };

            const baseSubtotal = {{ $subtotal }};
            const discount = {{ $discount }};
            const shippingDiscount = {{ isset($appliedDiscount['shipping_discount']) ? $appliedDiscount['shipping_discount'] : 0 }};

            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN').format(price) + '₫';
            }

            async function callGHNAPI(endpoint) {
                try {
                    const response = await fetch(GHN_CONFIG.baseUrl + endpoint, {
                        method: 'GET',
                        headers: {
                            'Token': GHN_CONFIG.token,
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.code !== 200) {
                        throw new Error(result.message || 'API Error');
                    }

                    return result.data;
                } catch (error) {
                    console.error('GHN API Error:', error);
                    return getFallbackData(endpoint);
                }
            }

            function getFallbackData(endpoint) {
                if (endpoint === 'province') {
                    return [
                        { ProvinceID: 201, ProvinceName: "Hà Nội" },
                        { ProvinceID: 202, ProvinceName: "Hồ Chí Minh" },
                        { ProvinceID: 203, ProvinceName: "Hải Phòng" },
                        { ProvinceID: 204, ProvinceName: "Đà Nẵng" },
                        { ProvinceID: 205, ProvinceName: "Cần Thơ" },
                        { ProvinceID: 206, ProvinceName: "Bình Dương" },
                        { ProvinceID: 207, ProvinceName: "Đồng Nai" },
                        { ProvinceID: 208, ProvinceName: "Khánh Hòa" },
                        { ProvinceID: 209, ProvinceName: "Lâm Đồng" },
                        { ProvinceID: 210, ProvinceName: "Bà Rịa - Vũng Tàu" }
                    ];
                } else if (endpoint.includes('district')) {
                    return [
                        { DistrictID: 1, DistrictName: "Quận 1" },
                        { DistrictID: 2, DistrictName: "Quận 2" },
                        { DistrictID: 3, DistrictName: "Quận 3" },
                        { DistrictID: 4, DistrictName: "Quận 4" },
                        { DistrictID: 5, DistrictName: "Quận 5" }
                    ];
                } else if (endpoint.includes('ward')) {
                    return [
                        { WardCode: "001", WardName: "Phường 1" },
                        { WardCode: "002", WardName: "Phường 2" },
                        { WardCode: "003", WardName: "Phường 3" },
                        { WardCode: "004", WardName: "Phường 4" },
                        { WardCode: "005", WardName: "Phường 5" }
                    ];
                }
                return [];
            }

            async function loadProvinces() {
                try {
                    provinceSelect.innerHTML = '<option value="">Đang tải...</option>';
                    provinceData = await callGHNAPI('province');
                    provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';

                    provinceData.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.ProvinceID;
                        option.textContent = province.ProvinceName;
                        option.dataset.name = province.ProvinceName;
                        provinceSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Load provinces error:', error);
                    provinceSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                }
            }

            async function loadDistricts(provinceId) {
                try {
                    districtSelect.innerHTML = '<option value="">Đang tải...</option>';
                    districtSelect.disabled = true;
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;

                    districtData = await callGHNAPI(`district?province_id=${provinceId}`);
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                    districtData.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.DistrictID;
                        option.textContent = district.DistrictName;
                        option.dataset.name = district.DistrictName;
                        districtSelect.appendChild(option);
                    });

                    districtSelect.disabled = false;
                } catch (error) {
                    console.error('Load districts error:', error);
                    districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                }
            }

            async function loadWards(districtId) {
                try {
                    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                    wardSelect.disabled = true;

                    wardData = await callGHNAPI(`ward?district_id=${districtId}`);
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                    wardData.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.WardCode;
                        option.textContent = ward.WardName;
                        option.dataset.name = ward.WardName;
                        wardSelect.appendChild(option);
                    });

                    wardSelect.disabled = false;
                } catch (error) {
                    console.error('Load wards error:', error);
                    wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                }
            }

            async function calculateShippingFee() {
                if (!provinceSelect.value || !districtSelect.value || !wardSelect.value) {
                    updateTotalAmount();
                    return;
                }

                try {
                    standardFeeElement.textContent = 'Đang tính...';
                    expressFeeElement.textContent = 'Đang tính...';

                    const [standardResult, expressResult] = await Promise.all([
                        fetch('/order/calculate-shipping', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                province_id: parseInt(provinceSelect.value),
                                district_id: parseInt(districtSelect.value),
                                ward_code: wardSelect.value,
                                shipping_method: 'standard'
                            })
                        }).then(res => res.json()),

                        fetch('/order/calculate-shipping', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                province_id: parseInt(provinceSelect.value),
                                district_id: parseInt(districtSelect.value),
                                ward_code: wardSelect.value,
                                shipping_method: 'express'
                            })
                        }).then(res => res.json())
                    ]);

                    if (standardResult.success && expressResult.success) {
                        currentShippingFees.standard = standardResult.shipping_fee;
                        currentShippingFees.express = expressResult.shipping_fee;

                        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.dataset?.name || '';
                        const districtName = districtSelect.options[districtSelect.selectedIndex]?.dataset?.name || '';

                        const isHanoiCenter = provinceName === 'Hà Nội' &&
                            ['Quận Ba Đình', 'Quận Hoàn Kiếm', 'Quận Hai Bà Trưng', 'Quận Đống Đa', 'Quận Tây Hồ', 'Quận Cầu Giấy', 'Quận Thanh Xuân', 'Quận Hoàng Mai', 'Quận Long Biên'].includes(districtName);

                        const expressOption = document.querySelector('.shipping-option[data-method="express"]');
                        if (!isHanoiCenter) {
                            expressOption.style.display = 'none';
                            document.getElementById('standard').checked = true;
                        } else {
                            expressOption.style.display = 'block';
                        }

                        if (standardResult.is_free_shipping) {
                            standardFeeElement.innerHTML = '<span class="text-success fw-bold">Miễn phí</span>';
                        } else {
                            standardFeeElement.textContent = formatPrice(standardResult.shipping_fee);
                        }

                        if (expressResult.is_free_shipping) {
                            expressFeeElement.innerHTML = '<span class="text-success fw-bold">Miễn phí</span>';
                        } else {
                            expressFeeElement.textContent = formatPrice(expressResult.shipping_fee);
                        }

                        deliveryTextElement.textContent = `Ước tính giao hàng: ${standardResult.estimated_delivery}`;
                        deliveryInfoElement.style.display = 'block';

                    } else {
                        throw new Error('Không thể tính phí vận chuyển');
                    }

                } catch (error) {
                    console.error('Calculate shipping error:', error);

                    standardFeeElement.textContent = formatPrice(30000);
                    expressFeeElement.textContent = formatPrice(50000);
                } finally {
                    updateTotalAmount();
                }
            }

            function updateFullAddress() {
                const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.dataset?.name || '';
                const districtName = districtSelect.options[districtSelect.selectedIndex]?.dataset?.name || '';
                const wardName = wardSelect.options[wardSelect.selectedIndex]?.dataset?.name || '';
                const address = shippingAddress.value.trim();

                provinceNameInput.value = provinceName;
                districtNameInput.value = districtName;
                wardNameInput.value = wardName;

                if (provinceName && districtName && wardName) {
                    const parts = [address, wardName, districtName, provinceName].filter(part => part);
                    fullAddressText.textContent = parts.join(', ');
                    fullAddressDisplay.style.display = 'block';
                } else {
                    fullAddressDisplay.style.display = 'none';
                }

                if (provinceName && districtName && wardName) {
                    calculateShippingFee();
                }
            }

            function updateTotalAmount() {
                const selectedMethod = document.querySelector('input[name="shipping_method"]:checked')?.value || 'standard';
                const currentShippingFee = currentShippingFees[selectedMethod] || currentShippingFees.standard;
                const finalShippingFee = Math.max(0, currentShippingFee - shippingDiscount);
                const total = baseSubtotal + finalShippingFee - discount;

                if (finalShippingFee === 0 && shippingDiscount > 0) {
                    shippingFeeDisplay.innerHTML = '<span class="text-success fw-bold">Miễn phí</span>';
                } else {
                    shippingFeeDisplay.textContent = formatPrice(finalShippingFee);
                }

                totalAmountElement.textContent = formatPrice(total);

                document.querySelectorAll('.shipping-option').forEach(option => {
                    option.classList.remove('border-primary', 'bg-light');
                });
                const selectedOption = document.querySelector(`.shipping-option[data-method="${selectedMethod}"]`);
                if (selectedOption) {
                    selectedOption.classList.add('border-primary', 'bg-light');
                }
            }

            provinceSelect.addEventListener('change', function() {
                if (this.value) {
                    loadDistricts(this.value);
                } else {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    districtSelect.disabled = true;
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;
                }
                updateFullAddress();
            });

            districtSelect.addEventListener('change', function() {
                if (this.value) {
                    loadWards(this.value);
                } else {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;
                }
                updateFullAddress();
            });

            wardSelect.addEventListener('change', updateFullAddress);
            shippingAddress.addEventListener('input', updateFullAddress);

            document.querySelectorAll('input[name="shipping_method"]').forEach(input => {
                input.addEventListener('change', updateTotalAmount);
            });

            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                const requiredFields = [
                    { id: 'customer_name', name: 'Họ tên' },
                    { id: 'customer_phone', name: 'Số điện thoại' },
                    { id: 'province_select', name: 'Tỉnh/Thành phố' },
                    { id: 'district_select', name: 'Quận/Huyện' },
                    { id: 'ward_select', name: 'Phường/Xã' },
                    { id: 'shipping_address', name: 'Địa chỉ chi tiết' }
                ];

                let isValid = true;
                let firstErrorField = null;
                let errorMessages = [];

                requiredFields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (element && !element.value.trim()) {
                        element.classList.add('is-invalid');
                        errorMessages.push(`- ${field.name} không được để trống`);
                        if (!firstErrorField) {
                            firstErrorField = element;
                        }
                        isValid = false;
                    } else if (element) {
                        element.classList.remove('is-invalid');
                    }
                });

                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                if (!paymentMethod) {
                    errorMessages.push('- Vui lòng chọn phương thức thanh toán');
                    isValid = false;
                }

                const shippingMethod = document.querySelector('input[name="shipping_method"]:checked');
                if (!shippingMethod) {
                    errorMessages.push('- Vui lòng chọn phương thức vận chuyển');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();

                    let errorHtml = '<div class="alert alert-danger alert-dismissible fade show"><h6 class="fw-bold mb-2">Vui lòng kiểm tra lại thông tin:</h6><ul class="mb-0">';
                    errorMessages.forEach(msg => {
                        errorHtml += `<li>${msg}</li>`;
                    });
                    errorHtml += '</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';

                    const form = document.getElementById('checkoutForm');
                    const existingAlert = form.querySelector('.alert-danger');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                    form.insertAdjacentHTML('afterbegin', errorHtml);

                    if (firstErrorField) {
                        firstErrorField.focus();
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            });

            document.querySelectorAll('input, select, textarea').forEach(element => {
                element.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
                element.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                });
            });

            loadProvinces();
            updateTotalAmount();

            const oldProvinceId = '{{ old("ghn_province_id") }}';
            const oldDistrictId = '{{ old("ghn_district_id") }}';
            const oldWardCode = '{{ old("ghn_ward_code") }}';

            if (oldProvinceId) {
                setTimeout(() => {
                    provinceSelect.value = oldProvinceId;
                    loadDistricts(oldProvinceId).then(() => {
                        if (oldDistrictId) {
                            districtSelect.value = oldDistrictId;
                            loadWards(oldDistrictId).then(() => {
                                if (oldWardCode) {
                                    wardSelect.value = oldWardCode;
                                    updateFullAddress();
                                }
                            });
                        }
                    });
                }, 1000);
            }
        });
    </script>

    <style>
        .shipping-option {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .shipping-option:hover {
            border-color: #ec8ca3 !important;
            background-color: #f8f9fa;
        }

        .shipping-option.border-primary {
            border-color: #ec8ca3 !important;
            background-color: #f8f9fa;
        }

        .form-check-input:checked {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection
