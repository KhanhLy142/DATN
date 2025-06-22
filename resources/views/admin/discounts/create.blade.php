@extends('admin.layouts.master')

@section('title', 'Thêm mã giảm giá')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm rounded-4 p-4">
            <h4 class="fw-bold text-center text-pink mb-4">Thêm mã giảm giá mới</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.discounts.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" id="code"
                               value="{{ old('code') }}" required placeholder="VD: SUMMER2024">
                        <div class="form-text">Mã sẽ tự động tạo khi bạn focus vào ô này</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Loại giảm giá <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" class="form-select" required>
                            <option value="">Chọn loại giảm giá</option>
                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>
                                <i class="bi bi-percent"></i> Phần trăm (%)
                            </option>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>
                                <i class="bi bi-currency-dollar"></i> Cố định (VNĐ)
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá trị giảm <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="discount_value" id="discount_value"
                               value="{{ old('discount_value') }}" step="0.01" min="0" required
                               placeholder="Nhập giá trị giảm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="bi bi-toggle-on text-success"></i> Kích hoạt ngay
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" id="start_date"
                               value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" id="end_date"
                               value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sản phẩm áp dụng</label>
                    <select name="products[]" id="products" class="form-select select2" multiple>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ in_array($product->id, old('products', [])) ? 'selected' : '' }}>
                                {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }} VNĐ
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        <i class="bi bi-info-circle text-primary"></i>
                        Để trống nếu muốn áp dụng cho tất cả sản phẩm. Có thể chọn nhiều sản phẩm bằng cách giữ Ctrl + Click
                    </div>
                </div>

                <!-- Preview sản phẩm đã chọn -->
                <div id="selected-products-preview" class="mb-3" style="display: none;">
                    <label class="form-label fw-semibold">Sản phẩm đã chọn:</label>
                    <div id="selected-products-list" class="d-flex flex-wrap gap-2"></div>
                </div>

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">Lưu mã giảm giá</button>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom CSS cho Select2 -->
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            padding: 2px 8px;
            margin: 2px;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffcccc;
        }

        .product-badge {
            background: linear-gradient(45deg, #0d6efd, #6610f2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            margin: 0.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-badge .remove-product {
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .product-badge .remove-product:hover {
            opacity: 1;
        }
    </style>
@endsection
