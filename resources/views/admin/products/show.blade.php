@extends('admin.layouts.master')

@section('title', 'Chi tiết sản phẩm')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết sản phẩm</h4>

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

                <p class="card-text"><strong>Giá cơ bản:</strong>
                    ₫ {{ number_format($product->base_price, 0, ',', '.') }}
                    @if($product->variants->count() > 0)
                        <span class="text-muted fst-italic">(Có giá riêng theo biến thể)</span>
                    @endif
                </p>

                @if ($product->image)
                    <div class="mb-4">
                        <strong>Hình ảnh sản phẩm ({{ $product->image_count }} ảnh):</strong><br>

                        @if($product->image_count > 1)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="main-image-container">
                                        <img id="mainImage"
                                             src="{{ $product->main_image_url }}"
                                             alt="Ảnh chính"
                                             class="img-fluid rounded shadow-sm"
                                             style="width: 100%; max-height: 400px; object-fit: cover;">
                                        <div class="mt-2">
                                            <span class="badge bg-primary">Ảnh chính</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="thumbnail-container">
                                        <strong class="text-muted">Tất cả ảnh sản phẩm:</strong>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach($product->images_array as $index => $image)
                                                <div class="thumbnail-item position-relative"
                                                     style="cursor: pointer;"
                                                     onclick="changeMainImage('{{ asset($image) }}', {{ $index }})">
                                                    <img src="{{ asset($image) }}"
                                                         alt="Ảnh {{ $index + 1 }}"
                                                         width="80"
                                                         height="80"
                                                         class="img-thumbnail thumbnail-img {{ $index === 0 ? 'active-thumbnail' : '' }}"
                                                         style="object-fit: cover;">
                                                    <span class="position-absolute top-0 start-0 badge bg-secondary">{{ $index + 1 }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">Click vào ảnh nhỏ để xem ảnh lớn</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3">
                                <img src="{{ $product->main_image_url }}"
                                     alt="Ảnh sản phẩm"
                                     width="300"
                                     class="img-thumbnail">
                            </div>
                        @endif
                    </div>
                @endif

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

                <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted"><strong>Ngày tạo:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted"><strong>Cập nhật lần cuối:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .thumbnail-img {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .thumbnail-img:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }

        .active-thumbnail {
            border-color: #007bff !important;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .main-image-container img {
            transition: all 0.3s ease;
        }
    </style>

    <script>
        function changeMainImage(imageSrc, index) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = imageSrc;

            document.querySelectorAll('.thumbnail-img').forEach(img => {
                img.classList.remove('active-thumbnail');
            });

            document.querySelectorAll('.thumbnail-img')[index].classList.add('active-thumbnail');

            mainImage.style.opacity = '0.7';
            setTimeout(() => {
                mainImage.style.opacity = '1';
            }, 150);
        }
    </script>
@endsection
