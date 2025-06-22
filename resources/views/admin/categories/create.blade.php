@extends('admin.layouts.master')

@section('title', 'Thêm danh mục')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm rounded-4 p-4">
            <h4 class="fw-bold text-center text-pink mb-4">Thêm danh mục mới</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="VD: Chăm sóc da">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Danh mục cha</label>
                    <select name="parent_id" class="form-select">
                        <option value="">Danh mục gốc (không có cha)</option>
                        @foreach ($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Để trống nếu đây là danh mục chính (VD: Chăm sóc da, Trang điểm...)</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Mô tả về danh mục này...">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                    <label class="form-check-label">Hiển thị danh mục</label>
                </div>

                <div class="text-end">
                    <button class="btn btn-pink" type="submit">Lưu</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
@endsection
