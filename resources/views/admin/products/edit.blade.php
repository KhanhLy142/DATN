@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chỉnh sửa sản phẩm</h4>

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

                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Thông tin cơ bản -->
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="bi bi-box"></i> Thông tin cơ bản
                            </h5>

                            <!-- Tên sản phẩm -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $product->name) }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div class="mb-3">
                                <label for="sku" class="form-label fw-semibold">Mã sản phẩm (SKU) <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('sku') is-invalid @enderror"
                                       id="sku"
                                       name="sku"
                                       value="{{ old('sku', $product->sku) }}"
                                       required>
                                @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Giá cơ bản và Tồn kho -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="base_price" class="form-label fw-semibold">Giá cơ bản <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₫</span>
                                            <input type="number"
                                                   class="form-control @error('base_price') is-invalid @enderror"
                                                   id="base_price"
                                                   name="base_price"
                                                   value="{{ old('base_price', $product->base_price) }}"
                                                   min="0"
                                                   step="1000"
                                                   required>
                                        </div>
                                        <small class="form-text text-muted">Giá cơ bản của sản phẩm. Các biến thể có thể có giá riêng.</small>
                                        @error('base_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label fw-semibold">Tồn kho</label>
                                        <input type="number"
                                               class="form-control @error('stock') is-invalid @enderror"
                                               id="stock"
                                               name="stock"
                                               value="{{ old('stock', $product->stock ?? 0) }}"
                                               min="0"
                                               step="1">
                                        <small class="form-text text-muted">Số lượng tồn kho hiện tại (nếu không có biến thể)</small>
                                        @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Mô tả -->
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="4"
                                          placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hình ảnh -->
                            <div class="mb-3">
                                <label for="image" class="form-label fw-semibold">Hình ảnh sản phẩm</label>
                                <input type="file"
                                       class="form-control @error('image') is-invalid @enderror"
                                       id="image"
                                       name="image"
                                       accept="image/*">
                                <small class="form-text text-muted">Chọn ảnh định dạng JPG, PNG, GIF. Kích thước tối đa 2MB.</small>
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Hiển thị ảnh hiện tại -->
                                @if($product->image)
                                    <div class="mt-2">
                                        <small class="text-muted">Ảnh hiện tại:</small><br>
                                        <img src="{{ asset($product->image) }}" alt="Ảnh hiện tại" width="100" class="img-thumbnail">
                                    </div>
                                @endif
                            </div>

                            <!-- Danh mục và Thương hiệu -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                                id="category_id"
                                                name="category_id"
                                                required>
                                            <option value="">Chọn danh mục</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="brand_id" class="form-label fw-semibold">Thương hiệu</label>
                                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                                id="brand_id"
                                                name="brand_id">
                                            <option value="">Chọn thương hiệu</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Biến thể sản phẩm -->
                            <h5 class="fw-bold text-info mb-3">
                                <i class="bi bi-collection"></i> Biến thể sản phẩm <span class="text-muted fs-6">(không bắt buộc)</span>
                            </h5>
                            <p class="text-muted">Chỉnh sửa các biến thể như màu sắc, dung tích, mùi hương cho sản phẩm.</p>

                            <!-- Container cho biến thể -->
                            <div id="variant-container">
                                @if($product->variants->count() > 0)
                                    @foreach($product->variants as $index => $variant)
                                        <div class="variant-group border p-3 rounded-3 mb-3 bg-light">
                                            <!-- Hidden input cho variant ID -->
                                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                                            <!-- Dòng đầu: Tên biến thể -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Tên biến thể</label>
                                                    <input type="text" name="variants[{{ $index }}][variant_name]"
                                                           class="form-control"
                                                           value="{{ old('variants.'.$index.'.variant_name', $variant->variant_name) }}"
                                                           placeholder="VD: Son đỏ 3g">
                                                </div>
                                            </div>

                                            <!-- Dòng thứ 2: Thuộc tính sản phẩm -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Màu sắc</label>
                                                    <input type="text" name="variants[{{ $index }}][color]"
                                                           class="form-control"
                                                           value="{{ old('variants.'.$index.'.color', $variant->color) }}"
                                                           placeholder="Đỏ, Hồng...">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Dung tích</label>
                                                    <input type="text" name="variants[{{ $index }}][volume]"
                                                           class="form-control"
                                                           value="{{ old('variants.'.$index.'.volume', $variant->volume) }}"
                                                           placeholder="3g, 50ml...">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Mùi hương</label>
                                                    <input type="text" name="variants[{{ $index }}][scent]"
                                                           class="form-control"
                                                           value="{{ old('variants.'.$index.'.scent', $variant->scent) }}"
                                                           placeholder="Hương hoa hồng...">
                                                </div>
                                            </div>

                                            <!-- Dòng thứ 3: Giá và tồn kho -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Giá <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₫</span>
                                                        <input type="number" name="variants[{{ $index }}][price]"
                                                               class="form-control"
                                                               value="{{ old('variants.'.$index.'.price', $variant->price) }}"
                                                               step="1000" min="0" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Tồn kho</label>
                                                    <input type="number" name="variants[{{ $index }}][stock_quantity]"
                                                           class="form-control"
                                                           value="{{ old('variants.'.$index.'.stock_quantity', $variant->stock_quantity) }}"
                                                           min="0" placeholder="0">
                                                </div>
                                            </div>

                                            <!-- Trạng thái và nút xóa -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="variants[{{ $index }}][status]"
                                                               value="1"
                                                            {{ old('variants.'.$index.'.status', $variant->status) ? 'checked' : '' }}>
                                                        <label class="form-check-label">
                                                            Có sẵn
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariant(this)">
                                                        <i class="bi bi-trash"></i> Xóa biến thể
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Biến thể mặc định nếu chưa có -->
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

                                        <!-- Trạng thái và nút xóa -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="variants[0][status]" value="1" checked>
                                                    <label class="form-check-label">
                                                        Có sẵn
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariant(this)">
                                                    <i class="bi bi-trash"></i> Xóa biến thể
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Nút thêm biến thể -->
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariant()">
                                    <i class="bi bi-plus-circle"></i> Thêm biến thể
                                </button>
                            </div>

                            <hr class="my-4">

                            <!-- Trạng thái -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="status"
                                           name="status"
                                           value="1"
                                        {{ old('status', $product->status) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="status">
                                        Hiển thị sản phẩm
                                    </label>
                                </div>
                                <small class="form-text text-muted">Bỏ tick để ẩn sản phẩm khỏi cửa hàng</small>
                            </div>

                            <!-- Nút hành động -->
                            <div class="text-end">
                                <button class="btn btn-pink" type="submit">Cập nhật</button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
