@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa thương hiệu')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm rounded-4 p-4">
            <h4 class="fw-bold text-center text-pink mb-4">Chỉnh sửa thương hiệu: {{ $brand->name }}</h4>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Tên thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $brand->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label fw-semibold">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror"
                                    id="supplier_id" name="supplier_id" required>
                                <option value="">Chọn nhà cung cấp</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $brand->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="country" class="form-label fw-semibold">Quốc gia xuất xứ</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                   id="country" name="country" value="{{ old('country', $brand->country) }}">
                            @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Trạng thái</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="1" {{ old('status', $brand->status) == '1' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('status', $brand->status) == '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label fw-semibold">Logo thương hiệu</label>

                    <!-- Logo hiện tại -->
                    @if($brand->logo)
                        <div class="mb-2">
                            <p class="text-muted mb-1">Logo hiện tại:</p>
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
                                 class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                        </div>
                    @endif

                    <input type="file" class="form-control @error('logo') is-invalid @enderror"
                           id="logo" name="logo" accept="image/*">
                    <div class="form-text">
                        Chấp nhận file: JPG, JPEG, PNG, GIF. Kích thước tối đa: 2MB
                        @if($brand->logo)
                            <br><strong>Lưu ý:</strong> Chỉ chọn file mới nếu muốn thay đổi logo hiện tại
                        @endif
                    </div>
                    @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- Preview logo mới -->
                    <div id="logo-preview" class="mt-2" style="display: none;">
                        <p class="text-muted mb-1">Logo mới:</p>
                        <img id="preview-image" src="" alt="Logo preview"
                             class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="4"
                              placeholder="Nhập mô tả về thương hiệu...">{{ old('description', $brand->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-pink">Cập nhật</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.brands.index') }}'">Hủy</button>
                </div>
            </form>
        </div>
    </div>

@endsection
