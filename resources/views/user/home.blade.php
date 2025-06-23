@extends('user.layouts.master')

@section('title', 'Trang chủ')

@section('banner')
    <!-- Banner chỉ xuất hiện ở trang chủ -->
    <section class="swiper banner-swiper">
        <div class="swiper-wrapper">
            @for ($i = 1; $i <= 3; $i++)
                <div class="swiper-slide">
                    <div class="banner-container">
                        <img src="{{ asset('images/banner' . $i . '.png') }}" alt="Banner {{ $i }}">
                    </div>
                </div>
            @endfor
        </div>
        <div class="swiper-pagination"></div>
    </section>
@endsection

@section('content')


    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5"><i class="bi bi-fire text-danger"></i> Sản phẩm đang giảm giá</h2>
            @if($discountedProducts->isNotEmpty())
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach($discountedProducts as $product)
                            <div class="col-md-3 mb-4 mx-1">
                                <div class="card h-100 position-relative">
                                    @if($product->has_discount)
                                        <div class="position-absolute top-0 end-0 p-2" style="z-index: 10">
                                        <span class="badge bg-danger">
                                            -{{ $product->discount_percentage }}%
                                        </span>
                                        </div>
                                    @endif

                                    @if($product->image)
                                        <img src="{{ asset('/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" class="card-img-top" alt="{{ $product->name }}">
                                    @endif

                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $product->brand->name }}</h6>
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text small">{{ $product->category->name }}</p>

                                        @if($product->has_discount)
                                            <div class="price-section">
                                            <span class="text-muted text-decoration-line-through">
                                                {{ number_format($product->base_price, 0, ',', '.') }}đ
                                            </span>
                                                <br>
                                                <span class="text-danger fw-bold fs-5">
                                                {{ number_format($product->final_price, 0, ',', '.') }}đ
                                            </span>
                                            </div>
                                            <small class="text-success">
                                                <i class="bi bi-tag-fill"></i> Mã: {{ $product->best_discount->code }}
                                            </small>
                                        @else
                                            <div class="price-section">
                                            <span class="fw-bold fs-5">
                                                {{ number_format($product->base_price, 0, ',', '.') }}đ
                                            </span>
                                            </div>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-pink">
                                                Xem chi tiết
                                            </a>
                                        </div>

                                        <div class="mt-3">
                                            <a href="" class="btn btn-pink">
                                                Thêm vào giỏ hàng
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            @endif
        </div>
    </section>
    <!-- New Products Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Sản phẩm mới</h2>
            <div class="swiper myNewSwiper">
                <div class="swiper-wrapper">
                    @if(count($products) > 0)
                        @foreach ($products as $index => $item)
                            <div class="col-md-4">
                                <div class="card product-card h-100 text-center">
                                    <img src="{{ $item->image ? asset('/' . $item->image) : asset('images/product-default.jpg') }}"
                                         class="card-img-top p-3" alt="{{ $item->name ?? 'Product' }}">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $item->name ?? 'Sản phẩm ' . ($index + 1) }}</h5>
                                        <p class="price text-pink">{{ number_format($item->base_price ?? 0, 2) }} đ</p>
                                        <button class="btn btn-pink mt-auto"
                                                onclick="addToCart({{ $item->id ?? 0 }})">
                                            Thêm vào giỏ hàng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-box-open fa-4x text-muted opacity-50"></i>
                                </div>
                                <h4 class="text-muted mb-3">Chưa có sản phẩm nào</h4>
                                <p class="text-muted mb-4">Hiện tại chưa có sản phẩm nào trong danh mục này.</p>
                                @if(auth()->check() && auth()->user()->is_admin)
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-pink">
                                        <i class="fas fa-plus me-2"></i>Thêm sản phẩm đầu tiên
                                    </a>
                                @else
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Featured Brands Section -->
    <section class="py-5 bg-white">
        <div class="container text-center">
            <h2 class="mb-4">Thương hiệu nổi bật</h2>
            <div class="row justify-content-center g-4">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="col-6 col-md-2">
                        <img src="{{ asset('images/brand' . $i . '.jpg') }}" alt="Brand {{ $i }}"
                             class="img-fluid brand-logo">
                    </div>
                @endfor
            </div>
        </div>
    </section>
@endsection
