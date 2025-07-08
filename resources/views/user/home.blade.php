@extends('user.layouts.master')

@section('title', 'Trang chủ')

@section('banner')
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-center mb-5"><i class="bi bi-fire text-danger"></i> Sản phẩm đang giảm giá</h2>

                <a href="{{ route('products.discounted') }}" class="btn btn-outline-danger">
                    Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if($discountedProducts && $discountedProducts->count() > 0)
                <div id="discountedCarousel" class="carousel slide position-relative" data-bs-ride="false">
                    <div class="carousel-inner">
                        @php
                            $chunkedProducts = $discountedProducts->chunk(4);
                        @endphp

                        @foreach($chunkedProducts as $chunkIndex => $chunk)
                            <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                <div class="row g-4">
                                    @foreach($chunk as $product)
                                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                                            <div class="card product-card h-100 border-0 shadow-sm position-relative overflow-hidden">
                                                @if(isset($product->has_discount) && $product->has_discount && $product->discount_percentage > 0)
                                                    <div class="position-absolute top-0 start-0 p-2" style="z-index: 2;">
                                                        <span class="badge bg-danger rounded-pill">
                                                            Sale -{{ abs(round($product->discount_percentage, 0)) }}%
                                                        </span>
                                                    </div>
                                                @endif

                                                <div class="card-img-container position-relative overflow-hidden">
                                                    <img src="{{ $product->image ? asset($product->image) : asset('images/product1.jpg') }}"
                                                         class="card-img-top p-3 product-img"
                                                         alt="{{ $product->name }}"
                                                         style="height: 250px; object-fit: cover; transition: transform 0.3s ease; cursor: pointer;"
                                                         onclick="window.location.href='{{ route('products.show', $product->id) }}'">
                                                </div>

                                                <div class="card-body text-center d-flex flex-column">
                                                    <h6 class="card-title mb-3 fw-bold product-title">
                                                        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h6>

                                                    <div class="price-section mb-3">
                                                        @if(isset($product->has_discount) && $product->has_discount && $product->final_price < $product->base_price)
                                                            <span class="price text-pink fw-bold fs-4">
                                                                {{ number_format($product->final_price, 0, ',', '.') }}đ
                                                            </span>
                                                            <small class="text-muted text-decoration-line-through ms-2">
                                                                {{ number_format($product->base_price, 0, ',', '.') }}đ
                                                            </small>
                                                        @else
                                                            <span class="price text-pink fw-bold fs-4">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                                                        @endif
                                                    </div>

                                                    <div class="stock-info mb-3">
                                                        @if($product->stock > 0)
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle"></i> Còn {{ $product->stock }} sản phẩm
                                                            </small>
                                                        @else
                                                            <small class="text-danger">
                                                                <i class="bi bi-x-circle"></i> Hết hàng
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <div class="d-grid gap-2 mt-auto">
                                                        @if($product->stock > 0)
                                                            <button class="btn btn-pink fw-semibold add-to-cart-btn rounded-pill"
                                                                    data-product-id="{{ $product->id }}"
                                                                    data-quantity="1">
                                                                <i class="bi bi-cart-plus me-2"></i>
                                                                Thêm vào giỏ hàng
                                                            </button>
                                                            <button class="btn btn-outline-pink fw-semibold buy-now-btn rounded-pill"
                                                                    data-product-id="{{ $product->id }}">
                                                                Mua ngay
                                                            </button>
                                                        @else
                                                            <button class="btn btn-secondary fw-semibold rounded-pill" disabled>
                                                                <i class="bi bi-x-circle me-2"></i>
                                                                Hết hàng
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($discountedProducts->count() > 4)
                        <button class="carousel-control-prev position-absolute top-50 start-0 translate-middle-y"
                                type="button" data-bs-target="#discountedCarousel" data-bs-slide="prev"
                                style="width: 50px; height: 50px; margin-left: -25px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next position-absolute top-50 end-0 translate-middle-y"
                                type="button" data-bs-target="#discountedCarousel" data-bs-slide="next"
                                style="width: 50px; height: 50px; margin-right: -25px;">
                            <span class="carousel-control-next-icon rounded-circle d-flex align-items-center justify-content-center"
                                  style="width: 40px; height: 40px;" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">Hiện tại không có sản phẩm giảm giá nào.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-center mb-5"><i class="bi bi-stars text-success"></i> Sản phẩm mới</h2>
                <a href="{{ route('products.new') }}" class="btn btn-outline-success">
                    Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if(isset($products) && $products && $products->count() > 0)
                <div id="newProductsCarousel" class="carousel slide position-relative" data-bs-ride="false">
                    <div class="carousel-inner">
                        @php
                            $chunkedNewProducts = $products->chunk(4);
                        @endphp

                        @foreach($chunkedNewProducts as $chunkIndex => $chunk)
                            <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                <div class="row g-4">
                                    @foreach($chunk as $product)
                                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                                            <div class="card product-card h-100 border-0 shadow-sm position-relative overflow-hidden">
                                                <div class="position-absolute top-0 start-0 p-2" style="z-index: 2;">
                                                    <span class="badge bg-success rounded-pill">New</span>
                                                </div>

                                                <div class="card-img-container position-relative overflow-hidden">
                                                    <img src="{{ $product->main_image_url ?? asset('images/product1.jpg') }}"
                                                         class="card-img-top p-3 product-img"
                                                         alt="{{ $product->name }}"
                                                         style="height: 250px; object-fit: cover; transition: transform 0.3s ease; cursor: pointer;"
                                                         onclick="window.location.href='{{ route('products.show', $product->id) }}'">
                                                </div>

                                                <div class="card-body text-center d-flex flex-column">
                                                    <h6 class="card-title mb-3 fw-bold product-title">
                                                        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h6>

                                                    <div class="price-section mb-3">
                                                        <span class="price text-pink fw-bold fs-4">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                                                    </div>

                                                    <div class="stock-info mb-3">
                                                        @if($product->stock > 0)
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle"></i> Còn {{ $product->stock }} sản phẩm
                                                            </small>
                                                        @else
                                                            <small class="text-danger">
                                                                <i class="bi bi-x-circle"></i> Hết hàng
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <div class="d-grid gap-2 mt-auto">
                                                        @if($product->stock > 0)
                                                            <button class="btn btn-pink fw-semibold add-to-cart-btn rounded-pill"
                                                                    data-product-id="{{ $product->id }}"
                                                                    data-quantity="1">
                                                                <i class="bi bi-cart-plus me-2"></i>
                                                                Thêm vào giỏ hàng
                                                            </button>
                                                            <button class="btn btn-outline-pink fw-semibold buy-now-btn rounded-pill"
                                                                    data-product-id="{{ $product->id }}">
                                                                Mua ngay
                                                            </button>
                                                        @else
                                                            <button class="btn btn-secondary fw-semibold rounded-pill" disabled>
                                                                <i class="bi bi-x-circle me-2"></i>
                                                                Hết hàng
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($products->count() > 4)
                        <button class="carousel-control-prev position-absolute top-50 start-0 translate-middle-y"
                                type="button" data-bs-target="#newProductsCarousel" data-bs-slide="prev"
                                style="width: 50px; height: 50px; margin-left: -25px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next position-absolute top-50 end-0 translate-middle-y"
                                type="button" data-bs-target="#newProductsCarousel" data-bs-slide="next"
                                style="width: 50px; height: 50px; margin-right: -25px;">
                            <span class="carousel-control-next-icon rounded-circle d-flex align-items-center justify-content-center"
                                  style="width: 40px; height: 40px;" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-box-open fa-4x text-muted opacity-50"></i>
                    </div>
                    <h4 class="text-muted mb-3">Chưa có sản phẩm mới</h4>
                    <p class="text-muted mb-4">Hiện tại không có sản phẩm mới nào trong 2 ngày qua.</p>
                </div>
            @endif
        </div>
    </section>

@endsection

<script>
    class CartManager {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.updateCartCount();
        }

        bindEvents() {
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-to-cart-btn') ||
                    e.target.closest('.add-to-cart-btn')) {
                    e.preventDefault();
                    const button = e.target.closest('.add-to-cart-btn');
                    const productId = button.dataset.productId;
                    const quantity = button.dataset.quantity || 1;

                    this.addToCart(productId, quantity, button);
                }

                if (e.target.classList.contains('buy-now-btn') ||
                    e.target.closest('.buy-now-btn')) {
                    e.preventDefault();
                    const button = e.target.closest('.buy-now-btn');
                    const productId = button.dataset.productId;

                    this.buyNow(productId, button);
                }
            });
        }

        async addToCart(productId, quantity = 1, button = null) {
            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: parseInt(quantity)
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    this.updateCartCount(data.cart_count);
                } else {
                    this.showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
            }
        }

        async buyNow(productId, button = null) {
            try {
                await this.addToCart(productId, 1, null);
                window.location.href = '/cart';
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
            }
        }

        async updateCartCount(count = null) {
            try {
                if (count === null) {
                    const response = await fetch('/cart/count');
                    const data = await response.json();
                    count = data.count;
                }

                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = count;
                    element.style.display = count > 0 ? 'inline' : 'none';
                });
            } catch (error) {
                console.error('Error updating cart count:', error);
            }
        }

        showNotification(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <span>${message}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        window.cartManager = new CartManager();

        const productImages = document.querySelectorAll('.product-img');
        productImages.forEach(img => {
            img.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });
            img.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
