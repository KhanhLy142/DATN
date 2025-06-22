@extends('admin.layouts.master')

@section('title', 'Danh sách thương hiệu')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý thương hiệu</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter và Search -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.brands.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm thương hiệu..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-select">
                            <option value="">Tất cả nhà cung cấp</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">Thêm mới</a>
        </div>

        <!-- Thêm class brand-table-container và brand-table -->
        <div class="table-responsive brand-table-container">
            <table class="table table-bordered table-hover align-middle brand-table">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Logo</th>
                    <th>Tên thương hiệu</th>
                    <th>Nhà cung cấp</th>
                    <th>Quốc gia</th>
                    <th>Mô tả</th>
                    <th class="text-center">Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($brands as $index => $brand)
                    <tr>
                        <td>{{ $brands->firstItem() + $index }}</td>
                        <td>
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
                                     class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $brand->name }}</strong>
                        </td>
                        <td>
                            @if($brand->supplier)
                                <span class="supplier-badge">{{ $brand->supplier->name }}</span>
                            @else
                                <span class="text-muted">Chưa có nhà cung cấp</span>
                            @endif
                        </td>
                        <td>{{ $brand->country ?: '-' }}</td>
                        <td>
                            @if($brand->description)
                                <span title="{{ $brand->description }}">
                                    {{ Str::limit($brand->description, 80) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $brand->status ? 'bg-success' : 'bg-secondary' }} cursor-pointer toggle-status"
                                  data-brand-id="{{ $brand->id }}"
                                  data-status="{{ $brand->status }}">
                                {{ $brand->status_text }}
                            </span>
                        </td>
                        <td>{{ $brand->created_at->format('d/m/Y') }}</td>
                        <td>
                            <!-- Sử dụng button group thay vì inline style -->
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('admin.brands.show', $brand->id) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                   class="btn btn-outline-warning btn-sm"
                                   title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.brands.destroy', $brand->id) }}"
                                      method="POST"
                                      style="display: inline;"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá thương hiệu này không? Thao tác này không thể hoàn tác!')">
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
                        <td colspan="9" class="text-center text-muted py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                <p class="mb-0">Không có thương hiệu nào</p>
                                <small>Nhấn "Thêm thương hiệu mới" để tạo thương hiệu đầu tiên</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            @include('admin.layouts.pagination', ['paginator' => $brands, 'itemName' => 'thương hiệu'])
        </div>
    </div>

@endsection
