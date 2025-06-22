@extends('admin.layouts.master')

@section('title', 'Nhập hàng')

@section('content')
    <div class="container-fluid py-4">
        {{-- Hiển thị thông báo lỗi --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm rounded-4">
            <div class="card-header border-0 bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold text-pink mb-0">
                            <i class="bi bi-box-arrow-in-down me-2"></i>Nhập hàng
                        </h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.inventory.store') }}" id="importForm">
                    @csrf

                    {{-- Thông tin phiếu nhập --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-building me-1"></i>Nhà cung cấp
                                <span class="text-danger">*</span>
                            </label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-event me-1"></i>Ngày nhập
                            </label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                    </div>

                    {{-- Danh sách sản phẩm nhập --}}
                    <div class="card border">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">
                                        <i class="bi bi-list-ul me-2"></i>Danh sách sản phẩm nhập
                                    </h5>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
                                        <i class="bi bi-plus-circle me-1"></i>Thêm sản phẩm
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle import-table" id="itemsTable">
                                    <thead class="table-light text-center">
                                    <tr>
                                        <th>Sản phẩm <span class="text-danger">*</span></th>
                                        <th>Số lượng <span class="text-danger">*</span></th>
                                        <th>Đơn giá nhập <span class="text-danger">*</span></th>
                                        <th>Thành tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="item-row">
                                        <td style="position: relative;">
                                            <select name="items[0][product_id]" class="form-select product-select" required>
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                            data-name="{{ $product->name }}"
                                                            data-sku="{{ $product->sku }}"
                                                        {{ (old('items.0.product_id') == $product->id) ? 'selected' : '' }}>
                                                        {{ $product->name }} - {{ $product->sku }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="selected-product-display" style="display: none;">
                                                <div class="product-info">
                                                    <div class="product-name-display fw-semibold"></div>
                                                    <div class="product-sku-display text-muted small"></div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary change-product-btn">
                                                    <i class="bi bi-pencil"></i> Đổi
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="items[0][quantity]"
                                                   class="form-control quantity-input text-center"
                                                   min="1"
                                                   max="999999"
                                                   value="{{ old('items.0.quantity') }}"
                                                   placeholder="0"
                                                   required>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number"
                                                       name="items[0][unit_price]"
                                                       class="form-control price-input text-end"
                                                       min="0"
                                                       step="1000"
                                                       value="{{ old('items.0.unit_price') }}"
                                                       placeholder="0"
                                                       required>
                                                <span class="input-group-text">₫</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-end total-cell">0 ₫</div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-row"
                                                    disabled
                                                    style="width: 35px; height: 35px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr class="table-info">
                                        <td colspan="3" class="text-end fw-bold">
                                            <i class="bi bi-calculator me-2"></i>Tổng cộng:
                                        </td>
                                        <td class="fw-bold text-end" id="grandTotal">0 ₫</td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Ghi chú --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-chat-left-text me-1"></i>Ghi chú
                                    </label>
                                    <textarea name="notes"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Ghi chú thêm về phiếu nhập này (tùy chọn)...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="text-end">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetImportForm()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Làm mới
                                </button>
                                <button type="submit" class="btn btn-pink" id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Lưu phiếu nhập
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Hướng dẫn nhanh --}}
        <div class="card mt-4 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Hướng dẫn nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Chọn nhà cung cấp trước khi thêm sản phẩm
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Có thể nhập nhiều sản phẩm trong một phiếu
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Hệ thống tự động cập nhật tồn kho
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Kiểm tra kỹ thông tin trước khi lưu
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
