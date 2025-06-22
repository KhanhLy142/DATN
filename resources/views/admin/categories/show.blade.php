@extends('admin.layouts.master')

@section('title', 'Chi tiết danh mục')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết danh mục</h4>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-folder-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="card-title text-primary mb-0">{{ $category->name }}</h5>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-toggle-{{ $category->status ? 'on' : 'off' }} me-2 text-{{ $category->status ? 'success' : 'danger' }}"></i>
                            <strong>Trạng thái:</strong>
                            <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $category->status_text }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-diagram-3 me-2 text-info"></i>
                            <strong>Danh mục cha:</strong>
                            @if($category->parent)
                                <span class="badge bg-info">{{ $category->parent->name }}</span>
                            @else
                                <span class="text-muted">Danh mục gốc</span>
                            @endif
                        </p>
                    </div>
                </div>

                <p class="card-text">
                    <i class="bi bi-text-paragraph me-2 text-secondary"></i>
                    <strong>Mô tả:</strong> {{ $category->description ?? 'Không có mô tả' }}
                </p>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-calendar-plus me-2 text-success"></i>
                            <strong>Ngày tạo:</strong> {{ $category->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-clock-history me-2 text-warning"></i>
                            <strong>Cập nhật lần cuối:</strong> {{ $category->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                @if($category->children->count() > 0)
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="bi bi-folder2-open me-2 text-warning"></i>
                            <strong>Danh mục con ({{ $category->children->count() }}):</strong>
                        </p>
                        <div class="mt-2">
                            @foreach($category->children as $child)
                                <span class="badge bg-light text-dark me-2 mb-2 border">
                                    <i class="bi bi-folder me-1"></i>{{ $child->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Hiển thị sản phẩm nếu có --}}
                {{-- @if($category->products->count() > 0)
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="bi bi-box-seam me-2 text-success"></i>
                            <strong>Sản phẩm thuộc danh mục ({{ $category->products->count() }}):</strong>
                        </p>
                        <div class="mt-2">
                            @foreach($category->products->take(5) as $product)
                                <span class="badge bg-secondary me-2 mb-2">
                                    <i class="bi bi-box me-1"></i>{{ $product->name }}
                                </span>
                            @endforeach
                            @if($category->products->count() > 5)
                                <span class="text-muted">... và {{ $category->products->count() - 5 }} sản phẩm khác</span>
                            @endif
                        </div>
                    </div>
                @endif --}}

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
