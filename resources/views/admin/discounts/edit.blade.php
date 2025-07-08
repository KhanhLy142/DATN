@extends('admin.layouts.master')

@section('title', 'Sửa mã giảm giá')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm rounded-4 p-4">
            <h4 class="fw-bold text-center text-pink mb-4">Sửa mã giảm giá: {{ $discount->code }}</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.discounts.update', $discount) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" id="code"
                               value="{{ old('code', $discount->code) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Áp dụng cho <span class="text-danger">*</span></label>
                        <select name="applies_to" id="applies_to" class="form-select" required>
                            <option value="">Chọn loại áp dụng</option>
                            <option value="order" {{ old('applies_to', $discount->applies_to ?? 'order') == 'order' ? 'selected' : '' }}>
                                🎫 Mã giảm giá đơn hàng (Coupon)
                            </option>
                            <option value="product" {{ old('applies_to', $discount->applies_to ?? 'order') == 'product' ? 'selected' : '' }}>
                                🏷️ Sale sản phẩm
                            </option>
                            <option value="shipping" {{ old('applies_to', $discount->applies_to ?? 'order') == 'shipping' ? 'selected' : '' }}>
                                🚚 Miễn phí vận chuyển
                            </option>
                        </select>
                        <div class="form-text">
                            <small class="text-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Order:</strong> Hiển thị trong giỏ hàng cho khách áp dụng<br>
                                <strong>Product:</strong> Sale trực tiếp trên sản phẩm<br>
                                <strong>Shipping:</strong> Giảm phí vận chuyển
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Loại giảm giá <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" class="form-select" required>
                            <option value="">Chọn loại giảm giá</option>
                            <option value="percent" {{ old('discount_type', $discount->discount_type) == 'percent' ? 'selected' : '' }}>
                                Phần trăm (%)
                            </option>
                            <option value="fixed" {{ old('discount_type', $discount->discount_type) == 'fixed' ? 'selected' : '' }}>
                                Cố định (VNĐ)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá trị giảm <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="discount_value" id="discount_value"
                               value="{{ old('discount_value', $discount->discount_value) }}" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Đơn hàng tối thiểu <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="min_order_amount" id="min_order_amount"
                               value="{{ old('min_order_amount', $discount->min_order_amount ?? 0) }}" min="0" step="1000" required
                               placeholder="VD: 200000">
                        <div class="form-text">Đơn hàng phải đạt giá trị tối thiểu để áp dụng mã (VNĐ). Nhập 0 nếu không có điều kiện.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mô tả mã giảm giá</label>
                        <input type="text" class="form-control" name="description" id="description"
                               value="{{ old('description', $discount->description) }}" maxlength="255"
                               placeholder="VD: Miễn phí vận chuyển cho đơn từ 200k">
                        <div class="form-text">Mô tả sẽ hiển thị cho khách hàng trong giỏ hàng</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" id="start_date"
                               value="{{ old('start_date', $discount->start_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" id="end_date"
                               value="{{ old('end_date', $discount->end_date->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="bi bi-toggle-on text-success"></i> Kích hoạt
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3" id="products_section">
                    <label class="form-label fw-semibold">Sản phẩm áp dụng</label>
                    <select name="products[]" id="products" class="form-select select2" multiple>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ in_array($product->id, old('products', $discount->products->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }} VNĐ
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span id="products_help_text">
                            Để trống nếu muốn áp dụng cho tất cả sản phẩm
                        </span>
                    </div>
                </div>

                @if($discount->products->count() > 0)
                    <div class="mb-3" id="current_products">
                        <label class="form-label fw-semibold">Sản phẩm hiện tại:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($discount->products as $product)
                                <span class="badge bg-primary p-2">
                                    <i class="bi bi-box"></i> {{ $product->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">Cập nhật</button>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appliesTo = document.getElementById('applies_to');
            const productsSection = document.getElementById('products_section');
            const productsHelpText = document.getElementById('products_help_text');
            const currentProducts = document.getElementById('current_products');

            function toggleProductsSection() {
                const value = appliesTo.value;

                if (value === 'order') {
                    productsSection.style.display = 'none';
                    if (currentProducts) currentProducts.style.display = 'none';
                } else if (value === 'product') {
                    productsSection.style.display = 'block';
                    if (currentProducts) currentProducts.style.display = 'block';
                    productsHelpText.innerHTML = '<i class="bi bi-info-circle text-warning"></i> Chọn sản phẩm cụ thể để áp dụng sale';
                } else if (value === 'shipping') {
                    productsSection.style.display = 'none';
                    if (currentProducts) currentProducts.style.display = 'none';
                } else {
                    productsSection.style.display = 'block';
                    if (currentProducts) currentProducts.style.display = 'block';
                    productsHelpText.innerHTML = '<i class="bi bi-info-circle text-primary"></i> Để trống nếu muốn áp dụng cho tất cả sản phẩm';
                }
            }

            appliesTo.addEventListener('change', toggleProductsSection);
            toggleProductsSection();
        });
    </script>
@endsection
