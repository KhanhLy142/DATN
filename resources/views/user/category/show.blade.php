@extends('user.layouts.master')

@section('title', $category->name . ' - DaisyBeauty')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">

            @include('user.layouts.products-filter', [
                'actionRoute' => route('category.show', $category->id),
                'categories' => [],
                'brands' => $filterData['brands'] ?? [],
                'priceRanges' => collect($filterData['priceRanges'] ?? [])
            ])

            <section class="col-lg-9 col-md-8">
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold text-dark mb-0 text-uppercase">
                                <i class="bi bi-grid-3x3-gap me-2 text-pink"></i>
                                {{ $category->name }} ({{ $products->total() }} KẾT QUẢ)
                            </h4>
                            <p class="text-muted mb-0">
                                Hiển thị <strong>{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong>
                                trong <strong>{{ $products->total() ?? 0 }}</strong> sản phẩm
                            </p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        @if(isset($hasFilters) && $hasFilters)
                            <span class="badge bg-pink me-2">
                                <i class="bi bi-funnel-fill me-1"></i>Đã lọc
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">Sắp xếp:</label>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="changeSort(this.value)">
                            <option value="">Mặc định</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4" id="productsGrid">
                    @if($products && $products->count() > 0)
                        @foreach($products as $product)
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card product-card h-100 border-0 shadow-sm position-relative overflow-hidden">
                                    @if(isset($product->has_discount) && $product->has_discount && $product->discount_percentage > 0)
                                        <div class="position-absolute top-0 start-0 p-2 z-index-1">
                                            <span class="badge bg-danger rounded-pill">
                                                Sale -{{ abs(round($product->discount_percentage, 0)) }}%
                                            </span>
                                        </div>
                                    @elseif(isset($product->is_new) && $product->is_new)
                                        <div class="position-absolute top-0 start-0 p-2 z-index-1">
                                            <span class="badge bg-success rounded-pill">New</span>
                                        </div>
                                    @endif

                                    <div class="card-img-container position-relative overflow-hidden">
                                        @if($product->main_image)
                                            <img src="{{ asset($product->main_image) }}"
                                                 class="card-img-top p-3 product-img"
                                                 alt="{{ $product->name }}"
                                                 style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                        @else
                                            <img src="{{ asset('images/product1.jpg') }}"
                                                 class="card-img-top p-3 product-img"
                                                 alt="{{ $product->name }}"
                                                 style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                        @endif
                                    </div>

                                    <div class="card-body text-center d-flex flex-column">
                                        <h6 class="card-title mb-3 fw-bold product-title">
                                            <a href="{{ route('products.show', $product->id) }}"
                                               class="text-decoration-none text-dark">
                                                {{ $product->name }}
                                            </a>
                                        </h6>

                                        <div class="price-section mb-3">
                                            @if(isset($product->has_discount) && $product->has_discount && isset($product->final_price) && $product->final_price < $product->base_price)
                                                <span class="price text-pink fw-bold fs-4">
                                                    {{ number_format($product->final_price, 0, ',', '.') }}đ
                                                </span>
                                                <small class="text-muted text-decoration-line-through ms-2">
                                                    {{ number_format($product->base_price, 0, ',', '.') }}đ
                                                </small>
                                            @else
                                                <span class="price text-pink fw-bold fs-4">
                                                    {{ number_format($product->base_price, 0, ',', '.') }}đ
                                                </span>
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

                    @elseif(isset($hasFilters) && $hasFilters)
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-search" style="font-size: 4rem; color: #dee2e6;"></i>
                                </div>
                                <h4 class="text-muted mb-3">Không tìm thấy sản phẩm phù hợp</h4>
                                <p class="text-muted mb-4">
                                    Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác.
                                </p>
                                <a href="{{ route('category.show', $category->id) }}" class="btn btn-pink">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Xóa bộ lọc
                                </a>
                            </div>
                        </div>

                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-box-seam" style="font-size: 4rem; color: #dee2e6;"></i>
                                </div>
                                <h4 class="text-muted mb-3">Danh mục này chưa có sản phẩm</h4>
                                <p class="text-muted mb-4">
                                    Vui lòng quay lại sau hoặc xem các danh mục khác.
                                </p>
                                <a href="{{ route('products.index') }}" class="btn btn-pink">
                                    <i class="bi bi-grid me-1"></i>Xem tất cả sản phẩm
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                @if($products && method_exists($products, 'hasPages') && $products->hasPages())
                    <div class="mt-5">
                        @include('user.layouts.pagination', ['paginator' => $products, 'itemName' => 'sản phẩm'])
                    </div>
                @endif
            </section>
        </div>
    </div>

    <script>
        function changeSort(value) {
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set('sort', value);
            } else {
                url.searchParams.delete('sort');
            }
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('#filterForm input[type="radio"]');
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });
        });

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
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang thêm...';
                    }

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

                        if (button) {
                            button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đã thêm!';
                            button.classList.add('btn-success');
                            button.classList.remove('btn-pink');
                        }
                    } else {
                        this.showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
                } finally {
                    if (button) {
                        setTimeout(() => {
                            button.disabled = false;
                            button.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng';
                            button.classList.remove('btn-success');
                            button.classList.add('btn-pink');
                        }, 2000);
                    }
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
        });
    </script>
@endsection
