@extends('admin.layouts.master')

@section('title', 'Chi tiết thương hiệu')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
                                     class="img-thumbnail mb-3" style="max-width: 200px; max-height: 200px;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center mb-3"
                                     style="width: 200px; height: 200px; margin: 0 auto;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>

                        <h4 class="text-primary mb-3">{{ $brand->name }}</h4>

                        <div class="mb-2">
                            <span class="badge {{ $brand->status ? 'bg-success' : 'bg-secondary' }} fs-6">
                                {{ $brand->status_text }}
                            </span>
                        </div>

                        @if($brand->country)
                            <p class="text-muted mb-2">
                                <i class="bi bi-globe me-1"></i>{{ $brand->country }}
                            </p>
                        @endif

                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar me-1"></i>
                            Tạo ngày: {{ $brand->created_at->format('d/m/Y H:i') }}
                        </p>

                        @if($brand->updated_at != $brand->created_at)
                            <p class="text-muted mb-0">
                                <i class="bi bi-clock me-1"></i>
                                Cập nhật: {{ $brand->updated_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-building me-2"></i>Nhà cung cấp
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($brand->supplier)
                            <h6>{{ $brand->supplier->name }}</h6>
                            @if($brand->supplier->email)
                                <p class="mb-1">
                                    <i class="bi bi-envelope me-1"></i>
                                    <a href="mailto:{{ $brand->supplier->email }}">{{ $brand->supplier->email }}</a>
                                </p>
                            @endif
                            @if($brand->supplier->phone)
                                <p class="mb-1">
                                    <i class="bi bi-telephone me-1"></i>
                                    <a href="tel:{{ $brand->supplier->phone }}">{{ $brand->supplier->phone }}</a>
                                </p>
                            @endif
                            @if($brand->supplier->address)
                                <p class="mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $brand->supplier->address }}
                                </p>
                            @endif
                        @else
                            <p class="text-muted mb-0">Chưa có thông tin nhà cung cấp</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-card-text me-2"></i>Mô tả thương hiệu
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $brand->description ?? 'Không có mô tả.' }}</p>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-box-seam me-2"></i>Sản phẩm thuộc thương hiệu này
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($brand->products->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach($brand->products as $product)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">Giá: {{ number_format($product->price, 0, ',', '.') }}đ</small>
                                        </div>
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                            Xem
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-0">Chưa có sản phẩm nào.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>
    </div>
@endsection
