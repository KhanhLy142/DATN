@extends('admin.layouts.master')

@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="container mt-5">
        <!-- Tiêu đề trang -->
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Thêm sản phẩm</h4>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">

                        {{-- Thông báo --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                                    <div>
                                        <strong>Thành công!</strong><br>
                                        {{ session('success') }}
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                                    <div>
                                        <strong>Lỗi!</strong><br>
                                        {{ session('error') }}
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger mt-1"></i>
                                    <div>
                                        <strong>Có lỗi trong form:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Thông tin sản phẩm chính -->
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-box"></i> Thông tin cơ bản
                            </h5>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mã sản phẩm (SKU)</label>
                                <input type="text" class="form-control" name="sku" value="{{ old('sku') }}" placeholder="Để trống để tự động tạo">
                                <small class="form-text text-muted">Mã duy nhất cho sản phẩm. Nếu để trống, hệ thống sẽ tự động tạo.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Giá cơ bản <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₫</span>
                                            <input type="number" class="form-control" name="base_price" value="{{ old('base_price') }}" step="1000" min="0" required>
                                        </div>
                                        <small class="form-text text-muted">Giá cơ bản của sản phẩm. Các biến thể có thể có giá riêng.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tồn kho</label>
                                        <input type="number" class="form-control" name="stock" value="{{ old('stock', 0) }}" min="0" step="1">
                                        <small class="form-text text-muted">Số lượng tồn kho hiện tại (nếu không có biến thể).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mô tả</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hình ảnh</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="form-text text-muted">Chọn ảnh định dạng JPG, PNG, GIF. Kích thước tối đa 2MB.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Chọn danh mục</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Thương hiệu <span class="text-danger">*</span></label>
                                        <select name="brand_id" class="form-select" required>
                                            <option value="">Chọn thương hiệu</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Thêm các biến thể sản phẩm -->
                            <h5 class="fw-bold text-info mb-3">
                                <i class="bi bi-collection"></i> Biến thể sản phẩm <span class="text-muted fs-6">(không bắt buộc)</span>
                            </h5>
                            <p class="text-muted">Thêm các biến thể như màu sắc, dung tích, mùi hương cho sản phẩm.</p>

                            <!-- Phần biến thể đã cải tiến -->
                            <div id="variant-container">
                                <div class="variant-group border p-3 rounded-3 mb-3 bg-light">
                                    <!-- Dòng đầu: Tên biến thể -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Tên biến thể</label>
                                            <input type="text" name="variants[0][variant_name]" class="form-control" placeholder="VD: Son đỏ 3g">
                                        </div>
                                    </div>

                                    <!-- Dòng thứ 2: Thuộc tính sản phẩm -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Màu sắc</label>
                                            <input type="text" name="variants[0][color]" class="form-control" placeholder="Đỏ, Hồng...">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Dung tích</label>
                                            <input type="text" name="variants[0][volume]" class="form-control" placeholder="3g, 50ml...">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Mùi hương</label>
                                            <input type="text" name="variants[0][scent]" class="form-control" placeholder="Hương hoa hồng...">
                                        </div>
                                    </div>

                                    <!-- Dòng thứ 3: Giá và tồn kho -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">₫</span>
                                                <input type="number" name="variants[0][price]"
                                                       class="form-control"
                                                       step="1000" min="0" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tồn kho</label>
                                            <input type="number" name="variants[0][stock_quantity]"
                                                   class="form-control"
                                                   min="0" value="0" placeholder="0">
                                        </div>
                                    </div>

                                    <!-- Nút xóa -->
                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariant(this)">
                                                <i class="bi bi-trash"></i> Xóa biến thể
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariant()">
                                    <i class="bi bi-plus-circle"></i> Thêm biến thể
                                </button>
                            </div>

                            <hr class="my-4">

                            <!-- Trạng thái -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">
                                        Hiển thị sản phẩm
                                    </label>
                                </div>
                                <small class="form-text text-muted">Bỏ tick để ẩn sản phẩm khỏi cửa hàng</small>
                            </div>

                            <!-- Nút hành động -->
                            <div class="text-end">
                                <button class="btn btn-pink" type="submit">Lưu sản phẩm</button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
