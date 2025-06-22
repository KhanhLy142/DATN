@extends('admin.layouts.master')

@section('title', 'Thêm thương hiệu mới')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="fw-bold text-center text-pink mb-4">Thêm thương hiệu mới</h4>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="supplier_id" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                                        <select class="form-select @error('supplier_id') is-invalid @enderror"
                                                id="supplier_id" name="supplier_id" required>
                                            <option value="">Chọn nhà cung cấp</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                        <label for="country" class="form-label">Quốc gia xuất xứ</label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                                               id="country" name="country" value="{{ old('country') }}">
                                        @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo thương hiệu</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                       id="logo" name="logo" accept="image/*">
                                <div class="form-text">Chấp nhận file: JPG, JPEG, PNG, GIF. Kích thước tối đa: 2MB</div>
                                @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Preview logo -->
                                <div id="logo-preview" class="mt-2" style="display: none;">
                                    <img id="preview-image" src="" alt="Logo preview"
                                         class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4"
                                          placeholder="Mô tả về thương hiệu này...">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button class="btn btn-pink" type="submit">Lưu</button>
                                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
