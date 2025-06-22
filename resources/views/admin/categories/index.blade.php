@extends('admin.layouts.master')

@section('title', 'Danh sách danh mục')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý danh mục sản phẩm</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter và Search -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm danh mục..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="parent_id" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>Danh mục gốc</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                    Con của: {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Thêm mới</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle category-table">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tên danh mục</th>
                    <th>Danh mục cha</th>
                    <th>Mô tả</th>
                    <th class="text-center" style="width: 120px;">Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($categories as $index => $category)
                    <tr>
                        <td>{{ $categories->firstItem() + $index }}</td>
                        <td>
                            @if($category->parent_id)
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                            @endif
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td>
                            @if($category->parent)
                                <span class="badge bg-info">{{ $category->parent->name }}</span>
                            @else
                                <span class="text-muted">Danh mục gốc</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($category->description, 50) ?: '-' }}</td>
                        <td class="text-center">
                            @if ($category->status)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>{{ $category->created_at->format('d/m/Y') }}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.categories.show', $category->id) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                   class="btn btn-outline-warning btn-sm"
                                   title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá danh mục này không?')"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Không có danh mục nào</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            @include('admin.layouts.pagination', ['paginator' => $categories, 'itemName' => 'danh mục'])
        </div>
    </div>
@endsection
