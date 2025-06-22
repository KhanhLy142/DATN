@extends('admin.layouts.master')

@section('title', 'Chi tiết sản phẩm')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết sản phẩm</h4>

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
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
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Lỗi!</strong><br>
                        {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card chứa thông tin sản phẩm -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $product->name }}</h5>

                <p class="card-text"><strong>Mã sản phẩm (SKU):</strong> {{ $product->sku }}</p>

                <p class="card-text"><strong>Trạng thái:</strong>
                    <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }}">
                        {{ $product->status == 1 ? 'Hiển thị' : 'Ẩn' }}
                    </span>
                </p>

                <p class="card-text"><strong>Danh mục:</strong> {{ $product->category->name ?? 'Không có' }}</p>
                <p class="card-text"><strong>Thương hiệu:</strong> {{ $product->brand->name ?? 'Không có' }}</p>
                <p class="card-text"><strong>Mô tả:</strong> {{ $product->description ?? 'Không có mô tả' }}</p>

                <!-- Giá sản phẩm cơ bản -->
                <p class="card-text"><strong>Giá cơ bản:</strong>
                    ₫ {{ number_format($product->base_price, 0, ',', '.') }}
                    @if($product->variants->count() > 0)
                        <span class="text-muted fst-italic">(Có giá riêng theo biến thể)</span>
                    @endif
                </p>

                <!-- Hình ảnh -->
                @if ($product->image)
                    <div class="mb-3">
                        <strong>Hình ảnh sản phẩm:</strong><br>
                        <img src="{{ asset($product->image) }}" alt="Ảnh sản phẩm" width="200" class="img-thumbnail mb-3">
                    </div>
                @endif

                <!-- Thông tin tổng quan về biến thể -->
                @if($product->variants->count())
                    <div class="mb-3">
                        <p class="card-text"><strong>Tổng số biến thể:</strong>
                            <span class="badge bg-info">{{ $product->variants->count() }} biến thể</span>
                        </p>
                        <p class="card-text"><strong>Tổng tồn kho (từ biến thể):</strong>
                            <span class="badge bg-success">{{ $product->variants->sum('stock_quantity') }} sản phẩm</span>
                        </p>
                        <p class="card-text"><strong>Khoảng giá:</strong>
                            ₫ {{ number_format($product->variants->min('price'), 0, ',', '.') }} -
                            ₫ {{ number_format($product->variants->max('price'), 0, ',', '.') }}
                        </p>
                    </div>
                @endif

                <!-- Biến thể sản phẩm -->
                @if($product->variants->count())
                    <div class="mb-3">
                        <strong>Chi tiết biến thể sản phẩm:</strong>
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Tên biến thể</th>
                                    <th>Màu sắc</th>
                                    <th>Dung tích</th>
                                    <th>Mùi hương</th>
                                    <th>Giá</th>
                                    <th>Tồn kho</th>
                                    <th>Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($product->variants as $variant)
                                    <tr>
                                        <td>{{ $variant->variant_name ?? '-' }}</td>
                                        <td>
                                            @if($variant->color)
                                                <span class="badge bg-secondary">{{ $variant->color }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $variant->volume ?? '-' }}</td>
                                        <td>{{ $variant->scent ?? '-' }}</td>
                                        <td class="fw-bold text-success">₫ {{ number_format($variant->price, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $variant->stock_quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $variant->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $variant->status ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $variant->status ? 'Có sẵn' : 'Hết hàng' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Sản phẩm này chưa có biến thể nào. Bạn có thể chỉnh sửa sản phẩm để thêm biến thể.
                    </div>
                @endif

                <!-- Thông tin thời gian -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted"><strong>Ngày tạo:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted"><strong>Cập nhật lần cuối:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Hành động -->
                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
