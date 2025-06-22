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
    <!-- Best Sellers Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Best Sellers</h2>

            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @for ($i = 1; $i <= 8; $i++)
                        <div class="swiper-slide">
                            <div class="card product-card h-100">
                                <img src="{{ asset('images/product' . $i . '.png') }}" class="card-img-top"
                                     alt="Product">
                                <div class="card-body text-center d-flex flex-column">
                                    <h5 class="card-title">Product Name</h5>
                                    <p class="price text-pink">$39.00</p>
                                    <button class="btn btn-pink mt-auto">Thêm vào giỏ hàng</button>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- New Products Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">New Products</h2>

            <div class="swiper myNewSwiper">
                <div class="swiper-wrapper">
                    @for ($i = 5; $i <= 12; $i++)
                        <div class="swiper-slide">
                            <div class="card product-card h-100">
                                <img src="{{ asset('images/product' . $i . '.jpg') }}" class="card-img-top"
                                     alt="Product">
                                <div class="card-body text-center d-flex flex-column">
                                    <h5 class="card-title">New Product Name</h5>
                                    <p class="price text-pink">$39.00</p>
                                    <button class="btn btn-pink mt-auto">Thêm vào giỏ hàng</button>
                                </div>
                            </div>
                        </div>
                    @endfor
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
