@extends('admin.layouts.master')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách sản phẩm</h4>

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

        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="brand_id" class="form-select">
                            <option value="">Tất cả thương hiệu</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price_min" class="form-control" placeholder="Giá từ" value="{{ request('price_min') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price_max" class="form-control" placeholder="Giá đến" value="{{ request('price_max') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Tìm</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm mới</a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Reset filter</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>SKU</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Biến thể</th>
                    <th>Trạng thái</th>
                    <th>Danh mục</th>
                    <th>Thương hiệu</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($products as $index => $product)
                    <tr>
                        <td>{{ $products->firstItem() + $index }}</td>

                        <td>
                            @if ($product->image)
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $product->main_image_url }}"
                                         alt="Ảnh {{ $product->name }}"
                                         width="60"
                                         height="60"
                                         class="rounded"
                                         style="object-fit: cover;">
                                </div>
                            @else
                                <div class="text-center text-muted" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ddd; border-radius: 4px;">
                                    <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                                </div>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->image_count > 1)
                                <br><small class="text-muted">{{ $product->image_count }} ảnh</small>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-secondary">{{ $product->sku }}</span>
                        </td>

                        <td>
                            @if ($product->variants->count())
                                <span class="text-success fw-bold">
                                    Từ ₫{{ number_format($product->variants->min('price'), 0, ',', '.') }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    (₫{{ number_format($product->variants->min('price'), 0, ',', '.') }} -
                                    ₫{{ number_format($product->variants->max('price'), 0, ',', '.') }})
                                </small>
                            @else
                                <span class="fw-bold">₫{{ number_format($product->base_price, 0, ',', '.') }}</span>
                            @endif
                        </td>

                        <td>
                            @if ($product->variants->count())
                                <span class="badge bg-info">
                                    {{ $product->variants->sum('stock_quantity') }}
                                    <small>(variants)</small>
                                </span>
                            @else
                                <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock }}
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($product->variants->count())
                                <span class="badge bg-info">
                                    {{ $product->variants->count() }} biến thể
                                </span>
                            @else
                                <span class="text-muted">---</span>
                            @endif
                        </td>

                        <td>
                            @if ($product->status)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>

                        <td>{{ $product->category->name ?? '---' }}</td>
                        <td>{{ $product->brand->name ?? '---' }}</td>

                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.products.show', $product->id) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                   class="btn btn-outline-warning btn-sm"
                                   title="Sửa sản phẩm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá sản phẩm này không?')"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Xóa sản phẩm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">Không có sản phẩm nào</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @include('admin.layouts.pagination', ['paginator' => $products, 'itemName' => 'sản phẩm'])
        </div>
    </div>

    <style>
        .table td img {
            transition: transform 0.2s ease-in-out;
        }

        .table td img:hover {
            transform: scale(1.1);
            z-index: 10;
            position: relative;
        }

        .badge {
            font-size: 0.65rem;
        }

        .table td [title] {
            cursor: help;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageContainers = document.querySelectorAll('.position-relative');

            imageContainers.forEach(container => {
                const badge = container.querySelector('.badge');
                if (badge) {
                    const img = container.querySelector('img');
                    if (img) {
                        img.setAttribute('title', `Sản phẩm có ${badge.textContent.trim()} ảnh. Click "Xem chi tiết" để xem tất cả.`);
                    }
                }
            });
        });
    </script>
@endsection
