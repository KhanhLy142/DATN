@extends('user.layouts.master')

@section('title', 'Sản phẩm')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">

            {{-- BỘ LỌC SẢN PHẨM --}}
            <aside class="col-lg-3 col-md-4 mb-4">
                <div class="p-4 border rounded shadow-sm bg-white sticky-top" style="top: 20px;">
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-pink">
                            <i class="bi bi-funnel-fill me-2"></i>Lọc sản phẩm
                        </h5>
                    </div>

                    <form method="GET" action="{{ route('products.index') }}" id="filterForm">

                        {{-- Danh mục sản phẩm --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center text-dark">
                                <i class="bi bi-grid-3x3-gap me-2 text-pink"></i>
                                Danh mục
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category" id="cat_all" value=""
                                    {{ request('category') == '' ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="cat_all">Tất cả danh mục</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category" id="cat_skincare" value="skincare"
                                    {{ request('category') == 'skincare' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_skincare">Chăm sóc da</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category" id="cat_makeup" value="makeup"
                                    {{ request('category') == 'makeup' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_makeup">Trang điểm</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category" id="cat_fragrance" value="fragrance"
                                    {{ request('category') == 'fragrance' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_fragrance">Nước hoa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="cat_bodycare" value="bodycare"
                                    {{ request('category') == 'bodycare' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_bodycare">Chăm sóc cơ thể</label>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Thương hiệu --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center text-dark">
                                <i class="bi bi-award me-2 text-pink"></i>
                                Thương hiệu
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand" id="brand_all" value=""
                                    {{ request('brand') == '' ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="brand_all">Tất cả thương hiệu</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand" id="brand1" value="Innisfree"
                                    {{ request('brand') == 'Innisfree' ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand1">Innisfree</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand" id="brand2" value="The Ordinary"
                                    {{ request('brand') == 'The Ordinary' ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand2">The Ordinary</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand" id="brand3" value="Skin1004"
                                    {{ request('brand') == 'Skin1004' ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand3">Skin1004</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand" id="brand4" value="CeraVe"
                                    {{ request('brand') == 'CeraVe' ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand4">CeraVe</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="brand" id="brand5" value="La Roche Posay"
                                    {{ request('brand') == 'La Roche Posay' ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand5">La Roche Posay</label>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Khoảng giá --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center text-dark">
                                <i class="bi bi-currency-dollar me-2 text-pink"></i>
                                Khoảng giá
                            </h6>

                            {{-- Preset price ranges --}}
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="price_range" id="price_all" value="">
                                    <label class="form-check-label fw-medium" for="price_all">Tất cả mức giá</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="price_range" id="price1" value="0-20">
                                    <label class="form-check-label" for="price1">Dưới $20</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="price_range" id="price2" value="20-50">
                                    <label class="form-check-label" for="price2">$20 - $50</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="price_range" id="price3" value="50-100">
                                    <label class="form-check-label" for="price3">$50 - $100</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="price_range" id="price4" value="100-999">
                                    <label class="form-check-label" for="price4">Trên $100</label>
                                </div>
                            </div>

                            {{-- Custom price range --}}
                            <div class="border rounded p-3 bg-light">
                                <label class="form-label small text-muted mb-2 fw-medium">Hoặc tùy chỉnh:</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" name="min_price" class="form-control form-control-sm text-center"
                                           placeholder="Từ" min="0" value="{{ request('min_price') }}" style="width: 70px;">
                                    <span class="text-muted fw-bold">-</span>
                                    <input type="number" name="max_price" class="form-control form-control-sm text-center"
                                           placeholder="Đến" min="0" value="{{ request('max_price') }}" style="width: 70px;">
                                </div>
                            </div>
                        </div>

                        {{-- Nút lọc --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-pink fw-semibold py-2 rounded-pill">
                                <i class="bi bi-search me-2"></i> Tìm sản phẩm
                            </button>
                        </div>
                    </form>

                    {{-- Active filters display --}}
                    <div id="activeFilters" class="mt-3">
                        @if(request()->hasAny(['category', 'brand', 'price_range', 'min_price', 'max_price']))
                            <div class="small text-muted mb-2 fw-medium">Bộ lọc đang áp dụng:</div>
                            @if(request('category'))
                                <span class="badge bg-pink me-1 mb-1 rounded-pill">
                                    {{ ucfirst(request('category')) }}
                                    <i class="bi bi-x ms-1"></i>
                                </span>
                            @endif
                            @if(request('brand'))
                                <span class="badge bg-pink me-1 mb-1 rounded-pill">
                                    {{ request('brand') }}
                                    <i class="bi bi-x ms-1"></i>
                                </span>
                            @endif
                            @if(request('price_range') || (request('min_price') || request('max_price')))
                                <span class="badge bg-pink me-1 mb-1 rounded-pill">
                                    Giá:
                                    @if(request('price_range'))
                                        ${{ str_replace('-', ' - $', request('price_range')) }}
                                    @else
                                        ${{ request('min_price', '0') }} - ${{ request('max_price', '∞') }}
                                    @endif
                                    <i class="bi bi-x ms-1"></i>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </aside>

            {{-- DANH SÁCH SẢN PHẨM --}}
            <section class="col-lg-9 col-md-8">
                {{-- Header --}}
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold text-dark mb-0">
                                <i class="bi bi-grid-3x3-gap me-2 text-pink"></i>
                                Sản phẩm của chúng tôi
                            </h4>
                            <p class="text-muted mb-0">
                                Hiển thị <strong>1-6</strong> trong <strong>24</strong> sản phẩm
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="row g-4" id="productsGrid">
                    @if(isset($products) && count($products) > 0)
                        @foreach($products as $i => $product)
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card product-card h-100 border-0 shadow-sm position-relative overflow-hidden">
                                    {{-- Product badges --}}
                                    <div class="position-absolute top-0 start-0 p-2 z-index-1">
                                        @if($i == 0)
                                            <span class="badge bg-danger rounded-pill">Sale -20%</span>
                                        @elseif($i == 1)
                                            <span class="badge bg-success rounded-pill">New</span>
                                        @elseif($i == 2)
                                            <span class="badge bg-warning text-dark rounded-pill">Hot</span>
                                        @endif
                                    </div>

                                    {{-- Product image --}}
                                    <div class="card-img-container position-relative overflow-hidden">
                                        <img src="{{ asset('images/product' . ($i + 1) . '.jpg') }}"
                                             class="card-img-top p-3 product-img"
                                             alt="Sản phẩm {{ $i + 1 }}"
                                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>

                                    <div class="card-body text-center d-flex flex-column">
                                        <h6 class="card-title mb-3 fw-bold">
                                            <a href="{{ route('products.show', $product->id ?? ($i + 1)) }}" class="text-decoration-none text-dark">
                                                {{ $product->name ?? ['Vitamin C Serum Brightening', 'Hyaluronic Acid Moisturizer', 'Niacinamide Serum 10%', 'Gentle Foaming Cleanser', 'Sunscreen SPF 50+', 'Anti-Aging Night Cream'][$i] }}
                                            </a>
                                        </h6>

                                        <div class="price-section mb-3">
                                            @if($i == 0)
                                                <span class="price text-pink fw-bold fs-4">${{ $product->sale_price ?? '31.20' }}</span>
                                                <small class="text-muted text-decoration-line-through ms-2">${{ $product->price ?? '39.00' }}</small>
                                            @else
                                                <span class="price text-pink fw-bold fs-4">${{ $product->price ?? number_format(rand(20, 80), 2) }}</span>
                                            @endif
                                        </div>

                                        <div class="d-grid gap-2 mt-auto">
                                            <button class="btn btn-pink fw-semibold add-to-cart-btn rounded-pill" data-product-id="{{ $product->id ?? ($i + 1) }}">
                                                <i class="bi bi-cart-plus me-2"></i>
                                                Thêm vào giỏ hàng
                                            </button>
                                            <button class="btn btn-outline-pink fw-semibold buy-now-btn rounded-pill" data-product-id="{{ $product->id ?? ($i + 1) }}">
                                                Mua ngay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback nếu không có dữ liệu --}}
                        @for ($i = 1; $i <= 6; $i++)
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card product-card h-100 border-0 shadow-sm position-relative overflow-hidden">
                                    {{-- Product badges --}}
                                    <div class="position-absolute top-0 start-0 p-2 z-index-1">
                                        @if($i == 1)
                                            <span class="badge bg-danger rounded-pill">Sale -20%</span>
                                        @elseif($i == 2)
                                            <span class="badge bg-success rounded-pill">New</span>
                                        @elseif($i == 3)
                                            <span class="badge bg-warning text-dark rounded-pill">Hot</span>
                                        @endif
                                    </div>

                                    {{-- Product image --}}
                                    <div class="card-img-container position-relative overflow-hidden">
                                        <img src="{{ asset('images/product' . $i . '.jpg') }}"
                                             class="card-img-top p-3 product-img"
                                             alt="Sản phẩm {{ $i }}"
                                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>

                                    <div class="card-body text-center d-flex flex-column">
                                        <h6 class="card-title mb-3 fw-bold">
                                            <a href="{{ route('products.show', $i) }}" class="text-decoration-none text-dark">
                                                {{ ['Vitamin C Serum Brightening', 'Hyaluronic Acid Moisturizer', 'Niacinamide Serum 10%', 'Gentle Foaming Cleanser', 'Sunscreen SPF 50+', 'Anti-Aging Night Cream'][$i-1] }}
                                            </a>
                                        </h6>

                                        <div class="price-section mb-3">
                                            @if($i == 1)
                                                <span class="price text-pink fw-bold fs-4">$31.20</span>
                                                <small class="text-muted text-decoration-line-through ms-2">$39.00</small>
                                            @else
                                                <span class="price text-pink fw-bold fs-4">${{ number_format(rand(20, 80), 2) }}</span>
                                            @endif
                                        </div>

                                        <div class="d-grid gap-2 mt-auto">
                                            <button class="btn btn-pink fw-semibold add-to-cart-btn rounded-pill" data-product-id="{{ $i }}">
                                                <i class="bi bi-cart-plus me-2"></i>
                                                Thêm vào giỏ hàng
                                            </button>
                                            <button class="btn btn-outline-pink fw-semibold buy-now-btn rounded-pill" data-product-id="{{ $i }}">
                                                Mua ngay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endif
                </div>

                {{-- PHÂN TRANG --}}
                <div class="mt-5">
                    {{-- Kiểm tra xem $products có phải là paginated collection không --}}
                    @if(isset($products) && method_exists($products, 'hasPages'))
                        @include('user.layouts.pagination', ['paginator' => $products, 'itemName' => 'sản phẩm'])
                    @else
                        {{-- Fallback pagination tĩnh nếu không có paginated data --}}
                        <div class="d-flex justify-content-center">
                            <nav aria-label="Product pagination">
                                <ul class="pagination">
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                    </li>
                                    <li class="page-item active">
                                        <span class="page-link">1</span>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">3</a>
                                    </li>
                                    <li class="page-item">
                                        <span class="page-link">...</span>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">8</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>

                {{-- Loading overlay --}}
                <div id="loadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none"
                     style="background: rgba(255,255,255,0.8); z-index: 1000;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border text-pink" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- Mobile Filter Button --}}
    <div class="d-md-none position-fixed bottom-0 start-0 end-0 p-3 bg-white border-top shadow">
        <button class="btn btn-pink w-100 rounded-pill fw-semibold" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
            <i class="bi bi-funnel me-2"></i>
            Bộ lọc sản phẩm
        </button>
    </div>

    {{-- Mobile Filters Offcanvas --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilters">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold text-pink">
                <i class="bi bi-funnel-fill me-2"></i>Bộ lọc sản phẩm
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            {{-- Copy filter form content here for mobile --}}
        </div>
    </div>

@endsection
