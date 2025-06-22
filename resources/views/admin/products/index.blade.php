@extends('admin.layouts.master')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách sản phẩm</h4>

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

        <!-- Filter và Search -->
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

                        {{-- Ảnh sản phẩm --}}
                        <td>
                            @if ($product->image)
                                <img src="{{ asset($product->image) }}" alt="Ảnh" width="60" class="rounded">
                            @else
                                <span class="text-muted fst-italic">Không có ảnh</span>
                            @endif
                        </td>

                        {{-- Tên sản phẩm --}}
                        <td>
                            <strong>{{ $product->name }}</strong>
                        </td>

                        {{-- SKU --}}
                        <td>
                            <span class="badge bg-secondary">{{ $product->sku }}</span>
                        </td>

                        {{-- Giá sản phẩm --}}
                        <td>
                            @if ($product->variants->count())
                                {{-- Hiển thị khoảng giá từ variants --}}
                                <span class="text-success fw-bold">
                                    Từ ₫{{ number_format($product->variants->min('price'), 0, ',', '.') }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    (₫{{ number_format($product->variants->min('price'), 0, ',', '.') }} -
                                    ₫{{ number_format($product->variants->max('price'), 0, ',', '.') }})
                                </small>
                            @else
                                {{-- Hiển thị giá chính --}}
                                <span class="fw-bold">₫{{ number_format($product->base_price, 0, ',', '.') }}</span>
                            @endif
                        </td>

                        {{-- Tồn kho --}}
                        <td>
                            @if ($product->variants->count())
                                {{-- Tồn kho từ variants --}}
                                <span class="badge bg-info">
                                    {{ $product->variants->sum('stock_quantity') }}
                                    <small>(variants)</small>
                                </span>
                            @else
                                {{-- Tồn kho chính --}}
                                <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock }}
                                </span>
                            @endif
                        </td>

                        {{-- Số lượng biến thể --}}
                        <td>
                            @if($product->variants->count())
                                <span class="badge bg-info">
                                    {{ $product->variants->count() }} biến thể
                                </span>
                            @else
                                <span class="text-muted">---</span>
                            @endif
                        </td>

                        {{-- Trạng thái --}}
                        <td>
                            @if ($product->status)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>

                        {{-- Danh mục & Thương hiệu --}}
                        <td>{{ $product->category->name ?? '---' }}</td>
                        <td>{{ $product->brand->name ?? '---' }}</td>

                        {{-- Thao tác với icon --}}
                        <td>
                            <div class="d-flex gap-2">
                                {{-- Xem chi tiết --}}
                                <a href="{{ route('admin.products.show', $product->id) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- Sửa --}}
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                   class="btn btn-outline-warning btn-sm"
                                   title="Sửa sản phẩm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                {{-- Xóa --}}
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

            {{-- Phân trang --}}
            @include('admin.layouts.pagination', ['paginator' => $products, 'itemName' => 'sản phẩm'])
        </div>
    </div>
@endsection
