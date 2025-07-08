@extends('user.layouts.master')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="position-sticky" style="top: 20px;">
                    <div class="main-image-container mb-3">
                        <img id="main-image"
                             src="{{ $product->main_image_url ?? asset('images/default-product.jpg') }}"
                             class="img-fluid rounded shadow-sm w-100"
                             alt="{{ $product->name ?? 'Sản phẩm' }}"
                             style="height: 350px; object-fit: contain; cursor: pointer;">
                    </div>

                    @if($product->image_count > 1)
                        <div class="thumbnail-container d-flex gap-2 flex-wrap">
                            @foreach($product->images_array as $index => $image)
                                <img src="{{ asset($image) }}"
                                     class="thumbnail-img border rounded cursor-pointer {{ $index === 0 ? 'active' : '' }}"
                                     alt="{{ $product->name }} - {{ $index + 1 }}"
                                     style="width: 80px; height: 80px; object-fit: cover;"
                                     onclick="changeMainImage('{{ asset($image) }}', this)">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">

                <div class="d-flex align-items-center mb-3">
                    <h3 class="fw-bold mb-0 me-3">{{ $product->name }}</h3>

                    <div class="badges-container">
                        @if(isset($product->has_discount) && $product->has_discount && $product->discount_percentage > 0)
                            <span class="badge bg-danger rounded-pill me-2">
                                Sale -{{ abs(round($product->discount_percentage, 0)) }}%
                            </span>
                        @endif

                        @if(isset($product->is_new) && $product->is_new)
                            <span class="badge bg-success rounded-pill">
                                New
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted">Thương hiệu: </span>
                    <a href="#" class="text-pink fw-semibold text-decoration-none">
                        {{ $product->brand->name ?? 'N/A' }}
                    </a>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center">
                        @if(isset($product->has_discount) && $product->has_discount && $product->final_price < $product->base_price)
                            <span id="product-price" class="text-price-primary fs-3 fw-bold me-3" data-base-price="{{ $product->final_price }}">
                                {{ number_format($product->final_price, 0, ',', '.') }}đ
                            </span>
                            <span class="text-muted text-decoration-line-through fs-5">
                                {{ number_format($product->base_price, 0, ',', '.') }}đ
                            </span>
                            <span class="badge bg-danger ms-2">
                                Tiết kiệm {{ number_format($product->base_price - $product->final_price, 0, ',', '.') }}đ
                            </span>
                        @else
                            <span id="product-price" class="text-pink fs-3 fw-bold" data-base-price="{{ $product->base_price }}">
                                {{ number_format($product->base_price, 0, ',', '.') }}đ
                            </span>
                        @endif
                    </div>

                    @if(isset($product->has_discount) && $product->has_discount && isset($product->best_discount))
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Khuyến mãi có hiệu lực từ {{ \Carbon\Carbon::parse($product->best_discount->start_date)->format('d/m/Y') }}
                                đến {{ \Carbon\Carbon::parse($product->best_discount->end_date)->format('d/m/Y') }}
                            </small>
                        </div>
                    @endif
                </div>

                <form id="product-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if(isset($product->grouped_variants['colors']) && count($product->grouped_variants['colors']) > 0)
                        <div class="mb-4">
                            <label class="fw-semibold mb-2">Màu sắc:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($product->grouped_variants['colors'] as $index => $color)
                                    <input type="radio" class="btn-check variant-option"
                                           name="color"
                                           id="color_{{ $color['id'] }}"
                                           value="{{ $color['value'] }}"
                                           data-variant-id="{{ $color['id'] }}"
                                           data-price="{{ $color['price'] }}"
                                           data-price-adjustment="{{ $color['price_adjustment'] }}"
                                           data-stock="{{ $color['stock'] }}"
                                           data-color-name="{{ $color['name'] }}"
                                        {{ $index == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="color_{{ $color['id'] }}">
                                        {{ $color['name'] }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($product->grouped_variants['volumes']) && count($product->grouped_variants['volumes']) > 0)
                        <div class="mb-4">
                            <label class="fw-semibold mb-2">Dung tích:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($product->grouped_variants['volumes'] as $index => $volume)
                                    <input type="radio" class="btn-check variant-option"
                                           name="volume"
                                           id="volume_{{ $volume['id'] }}"
                                           value="{{ $volume['value'] }}"
                                           data-variant-id="{{ $volume['id'] }}"
                                           data-price="{{ $volume['price'] }}"
                                           data-price-adjustment="{{ $volume['price_adjustment'] }}"
                                           data-stock="{{ $volume['stock'] }}"
                                        {{ $index == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="volume_{{ $volume['id'] }}">
                                        {{ $volume['name'] }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($product->grouped_variants['scents']) && count($product->grouped_variants['scents']) > 0)
                        <div class="mb-4">
                            <label class="fw-semibold mb-2">Mùi hương:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($product->grouped_variants['scents'] as $index => $scent)
                                    <input type="radio" class="btn-check variant-option"
                                           name="scent"
                                           id="scent_{{ $scent['id'] }}"
                                           value="{{ $scent['value'] }}"
                                           data-variant-id="{{ $scent['id'] }}"
                                           data-price="{{ $scent['price'] }}"
                                           data-price-adjustment="{{ $scent['price_adjustment'] }}"
                                           data-stock="{{ $scent['stock'] }}"
                                        {{ $index == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="scent_{{ $scent['id'] }}">
                                        {{ $scent['name'] }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="fw-semibold mb-2">Số lượng:</label>
                        <div class="quantity-box d-flex align-items-center">
                            <button type="button" class="btn-qty" onclick="decreaseQuantity()">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock ?? 999 }}" class="qty-input text-center mx-2">
                            <button type="button" class="btn-qty" onclick="increaseQuantity()">+</button>
                            <span class="ms-3 text-muted">
                                Còn lại: <span id="available-stock">{{ $product->stock ?? 999 }}</span> sản phẩm
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <button type="button" class="btn btn-pink w-50 fw-semibold py-2 add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                onclick="addToCart({{ $product->id }})">
                            <i class="bi bi-cart-plus me-2"></i>
                            Thêm vào giỏ hàng
                        </button>

                        <button type="button" class="btn btn-outline-pink w-50 fw-semibold py-2"
                                onclick="buyNow({{ $product->id }})">
                            Mua ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5">
            <ul class="nav nav-tabs justify-content-center mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">
                        <i class="bi bi-file-text me-2"></i>Mô tả sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reviews">
                        <i class="bi bi-star me-2"></i>Đánh giá khách hàng
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="description">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <h5 class="fw-bold mb-3">Về sản phẩm</h5>

                            @if($product->description)
                                <div class="product-description mb-4">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            @else
                                <p class="text-muted mb-4">
                                    Chưa có mô tả chi tiết cho sản phẩm này.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="reviews">
                    <div class="row">
                        <div class="col-md-10 mx-auto">

                            @if($reviewStats['total_reviews'] > 0)
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-center">
                                                <h2 class="display-4 fw-bold text-pink mb-0">
                                                    {{ number_format($reviewStats['average_rating'], 1) }}
                                                </h2>
                                                <div class="rating mb-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="text-warning fs-4">
                                                            {{ $i <= round($reviewStats['average_rating']) ? '★' : '☆' }}
                                                        </span>
                                                    @endfor
                                                </div>
                                                <p class="text-muted mb-0">
                                                    Dựa trên {{ $reviewStats['total_reviews'] }} đánh giá
                                                </p>
                                            </div>
                                            <div class="col-md-8">
                                                @for($i = 5; $i >= 1; $i--)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="me-2">{{ $i }} sao</span>
                                                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                                            <div class="progress-bar bg-warning"
                                                                 style="width: {{ $reviewStats['rating_breakdown'][$i]['percentage'] }}%">
                                                            </div>
                                                        </div>
                                                        <span class="text-muted small">
                                                            {{ $reviewStats['rating_breakdown'][$i]['count'] }}
                                                        </span>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                @auth
                                    @if($canReview)
                                        <a href="{{ route('reviews.create', ['product' => $product->id]) }}"
                                           class="btn btn-pink fw-semibold">
                                            <i class="bi bi-star me-2"></i>Viết đánh giá cho sản phẩm này
                                        </a>
                                        <small class="d-block text-success mt-2">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Bạn đã mua sản phẩm này và có thể đánh giá
                                        </small>
                                    @elseif($hasPurchased)
                                        <button class="btn btn-secondary fw-semibold" disabled>
                                            <i class="bi bi-check-circle me-2"></i>Bạn đã đánh giá sản phẩm này
                                        </button>
                                        <small class="d-block text-muted mt-2">
                                            Cảm ơn bạn đã chia sẻ đánh giá!
                                        </small>
                                    @else
                                        <button class="btn btn-outline-secondary fw-semibold" disabled>
                                            <i class="bi bi-lock me-2"></i>Chỉ khách hàng đã mua mới có thể đánh giá
                                        </button>
                                        <small class="d-block text-muted mt-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Hãy mua và sử dụng sản phẩm để chia sẻ trải nghiệm của bạn
                                        </small>
                                    @endif
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Muốn đánh giá sản phẩm?</strong><br>
                                        <a href="{{ route('login') }}" class="text-pink fw-semibold">Đăng nhập</a>
                                        và mua sản phẩm để chia sẻ trải nghiệm của bạn
                                    </div>
                                @endauth
                            </div>

                            <h5 class="fw-bold mb-4">
                                Đánh giá từ khách hàng
                                @if($reviewStats['total_reviews'] > 0)
                                    <span class="badge bg-pink">{{ $reviewStats['total_reviews'] }}</span>
                                @endif
                            </h5>

                            @if($reviews->count() > 0)
                                @foreach($reviews as $review)
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                {{ $review->customer->name ?? 'Khách hàng' }}
                                            </h6>
                                            <div class="d-flex align-items-center">
                                                <div class="rating me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="text-warning">
                                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                                        </span>
                                                    @endfor
                                                </div>
                                                <small class="text-muted">
                                                    {{ $review->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-bag-check me-1"></i>Đã mua hàng
                                                </span>
                                            @if($review->created_at >= now()->subDays(7))
                                                <span class="badge bg-info">Mới</span>
                                            @endif
                                        </div>
                                    </div>

                                        <p class="mb-0 text-dark">{{ $review->comment }}</p>

                                        @if($review->reply)
                                            <div class="mt-3 ps-3 border-start border-pink border-3">
                                                <small class="text-muted fw-semibold">Phản hồi từ shop:</small>
                                                <p class="mb-0 mt-1 text-muted">{{ $review->reply }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $reviews->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-star text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">Chưa có đánh giá nào</h5>
                                    <p class="text-muted">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                                    @auth
                                        <a href="{{ route('reviews.create', ['product' => $product->id]) }}"
                                           class="btn btn-outline-pink">
                                            <i class="bi bi-star me-2"></i>Viết đánh giá đầu tiên
                                        </a>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xem ảnh sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modal-image"
                         src="{{ asset('images/' . ($product->image ?? 'default-product.jpg')) }}"
                         class="img-fluid"
                         alt="{{ $product->name }}">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let basePrice = {{ $product->base_price }};
        @if(isset($product->has_discount) && $product->has_discount)
        let discountedBasePrice = {{ $product->final_price }};
        let hasDiscount = true;
        let discountRatio = {{ $product->final_price }} / {{ $product->base_price }};
        @else
        let discountedBasePrice = {{ $product->base_price }};
        let hasDiscount = false;
        let discountRatio = 1;
        @endif

        const baseStock = {{ $product->stock ?? 999 }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const variants = @json($product->variants ?? []);

        function changeMainImage(src, thumbnail) {
            document.getElementById('main-image').src = src;
            document.getElementById('modal-image').src = src;

            document.querySelectorAll('.thumbnail-img').forEach(img => img.classList.remove('active'));
            if (thumbnail) thumbnail.classList.add('active');
        }

        function increaseQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.getAttribute('max'));

            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        }

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const minValue = parseInt(input.getAttribute('min'));

            if (currentValue > minValue) {
                input.value = currentValue - 1;
            }
        }

        function findMatchingVariant() {
            const selectedColor = document.querySelector('input[name="color"]:checked');
            const selectedVolume = document.querySelector('input[name="volume"]:checked');
            const selectedScent = document.querySelector('input[name="scent"]:checked');

            if (!selectedColor && !selectedVolume && !selectedScent) {
                return null;
            }

            return variants.find(variant => {
                const colorMatch = !selectedColor || variant.color === selectedColor.value;
                const volumeMatch = !selectedVolume || variant.volume === selectedVolume.value;
                const scentMatch = !selectedScent || variant.scent === selectedScent.value;

                return colorMatch && volumeMatch && scentMatch;
            });
        }

        function updateProductInfo() {
            const matchingVariant = findMatchingVariant();
            let finalPrice = hasDiscount ? discountedBasePrice : basePrice;
            let availableStock = baseStock;

            if (matchingVariant) {
                if (hasDiscount) {
                    finalPrice = matchingVariant.price * discountRatio;
                } else {
                    finalPrice = matchingVariant.price;
                }
                availableStock = matchingVariant.stock_quantity;
            }

            const priceElement = document.getElementById('product-price');
            if (priceElement) {
                priceElement.textContent = new Intl.NumberFormat('vi-VN').format(Math.round(finalPrice)) + 'đ';
                priceElement.setAttribute('data-base-price', finalPrice);
            }

            const stockElement = document.getElementById('available-stock');
            if (stockElement) {
                stockElement.textContent = availableStock;
            }

            const quantityInput = document.getElementById('quantity');
            if (quantityInput) {
                quantityInput.setAttribute('max', availableStock);

                const currentQuantity = parseInt(quantityInput.value);
                if (currentQuantity > availableStock) {
                    quantityInput.value = Math.min(1, availableStock);
                }
            }

            const addButton = document.querySelector('.add-to-cart-btn');
            if (addButton) {
                if (availableStock === 0) {
                    addButton.disabled = true;
                    addButton.innerHTML = '<i class="bi bi-x-circle me-2"></i>Hết hàng';
                    addButton.classList.remove('btn-pink');
                    addButton.classList.add('btn-secondary');
                } else {
                    addButton.disabled = false;
                    addButton.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng';
                    addButton.classList.remove('btn-secondary');
                    addButton.classList.add('btn-pink');
                }
            }
        }

        function showNotification(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';

            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} position-fixed`;
            notification.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                animation: slideInRight 0.3s ease-out;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            `;
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi ${iconClass} me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close ms-2" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideOutRight 0.3s ease-out';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 4000);
        }

        async function addToCart(productId) {
            const quantityInput = document.getElementById('quantity');
            if (!quantityInput) {
                showNotification('Không tìm thấy input số lượng!', 'error');
                return;
            }

            const quantity = parseInt(quantityInput.value);
            const matchingVariant = findMatchingVariant();

            if (quantity <= 0) {
                showNotification('Vui lòng chọn số lượng hợp lệ!', 'error');
                return;
            }

            const maxStock = parseInt(quantityInput.getAttribute('max'));
            if (quantity > maxStock) {
                showNotification('Số lượng vượt quá tồn kho!', 'error');
                return;
            }

            if (maxStock === 0) {
                showNotification('Sản phẩm hiện đã hết hàng!', 'error');
                return;
            }

            const data = {
                product_id: productId,
                variant_id: matchingVariant ? matchingVariant.id : null,
                quantity: quantity
            };

            try {
                const addButton = document.querySelector('.add-to-cart-btn');
                if (!addButton) {
                    showNotification('Không tìm thấy nút thêm vào giỏ hàng!', 'error');
                    return;
                }

                const originalText = addButton.innerHTML;
                addButton.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Đang thêm...';
                addButton.disabled = true;

                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                addButton.innerHTML = originalText;
                addButton.disabled = false;

                if (!response.ok) {
                    throw new Error(result.message || 'Có lỗi xảy ra');
                }

                let message = `Đã thêm ${quantity} sản phẩm vào giỏ hàng!`;

                if (matchingVariant) {
                    const variantDetails = [];
                    if (matchingVariant.color) variantDetails.push(`Màu: ${matchingVariant.color}`);
                    if (matchingVariant.volume) variantDetails.push(`Dung tích: ${matchingVariant.volume}`);
                    if (matchingVariant.scent) variantDetails.push(`Mùi hương: ${matchingVariant.scent}`);

                    if (variantDetails.length > 0) {
                        message += `<br><small class="text-muted">${variantDetails.join(', ')}</small>`;
                    }
                }

                showNotification(message, 'success');

                if (result.cart_count !== undefined) {
                    updateCartCount(result.cart_count);
                }

            } catch (error) {
                console.error('Error adding to cart:', error);
                showNotification(error.message, 'error');

                const addButton = document.querySelector('.add-to-cart-btn');
                if (addButton) {
                    addButton.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng';
                    addButton.disabled = false;
                }
            }
        }

        async function buyNow(productId) {
            await addToCart(productId);

            setTimeout(() => {
                window.location.href = '/cart';
            }, 1500);
        }

        function updateCartCount(count) {
            const cartCountElements = document.querySelectorAll('.cart-count, #cart-count, .badge.cart-badge, [data-cart-count]');
            cartCountElements.forEach(element => {
                element.textContent = count;
                if (count > 0) {
                    element.style.display = 'inline-block';
                    element.classList.remove('d-none');
                } else {
                    element.classList.add('d-none');
                }
            });
        }


        function getSelectedVariantId() {
            const variant = findMatchingVariant();
            return variant ? variant.id : null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
                .cursor-pointer { cursor: pointer; }

                /* Product image styles */
                .main-image-container img {
                    transition: transform 0.3s ease;
                }

                .main-image-container img:hover {
                    transform: scale(1.02);
                }

                .thumbnail-img {
                    transition: all 0.3s ease;
                    opacity: 0.7;
                    cursor: pointer;
                }

                .thumbnail-img:hover,
                .thumbnail-img.active {
                    opacity: 1;
                    border-color: #e91e63 !important;
                    transform: scale(1.05);
                }

                /* Badge styles */
                .badges-container {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }

                .badge.bg-danger {
                    animation: pulse-sale 2s infinite;
                }

                .badge.bg-success {
                    animation: pulse-new 2s infinite;
                }

                @keyframes pulse-sale {
                    0%, 100% {
                        transform: scale(1);
                        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
                    }
                    50% {
                        transform: scale(1.05);
                        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
                    }
                }

                @keyframes pulse-new {
                    0%, 100% {
                        transform: scale(1);
                        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
                    }
                    50% {
                        transform: scale(1.05);
                        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
                    }
                }

                /* Responsive styles */
                @media (max-width: 768px) {
                    .badges-container {
                        gap: 0.25rem;
                    }

                    .badge {
                        font-size: 0.65rem;
                        padding: 0.375rem 0.625rem;
                    }

                    .thumbnail-img {
                        width: 60px !important;
                        height: 60px !important;
                    }
                }
            `;
            document.head.appendChild(style);

            const colorInputs = document.querySelectorAll('input[name="color"]');
            colorInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateProductInfo();
                });
            });

            document.querySelectorAll('input[name="volume"], input[name="scent"]').forEach(input => {
                input.addEventListener('change', function() {
                    updateProductInfo();
                });
            });

            const mainImage = document.getElementById('main-image');
            if (mainImage) {
                mainImage.addEventListener('click', function() {
                    const modalImage = document.getElementById('modal-image');
                    if (modalImage) {
                        modalImage.src = this.src;
                    }

                    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                    imageModal.show();
                });
            }

            const modalImage = document.getElementById('modal-image');
            if (modalImage && mainImage) {
                modalImage.src = mainImage.src;
            }

            document.querySelectorAll('.btn-qty').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.textContent === '+') {
                        increaseQuantity();
                    } else {
                        decreaseQuantity();
                    }
                });
            });

            const quantityInput = document.getElementById('quantity');
            if (quantityInput) {
                quantityInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const productId = document.querySelector('input[name="product_id"]').value;
                        addToCart(productId);
                    }
                });

                quantityInput.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    const max = parseInt(this.getAttribute('max'));
                    const min = parseInt(this.getAttribute('min'));

                    if (value < min) {
                        this.value = min;
                    } else if (value > max) {
                        this.value = max;
                        showNotification('Số lượng không được vượt quá tồn kho!', 'error');
                    }
                });
            }

            document.querySelectorAll('.thumbnail-img').forEach(img => {
                img.addEventListener('click', function() {
                    const src = this.src;
                    changeMainImage(src, this);
                });
            });

            updateProductInfo();

            console.log('Product detail page initialized');
            console.log('Has discount:', hasDiscount);
            console.log('Variants:', variants);
        });
    </script>
@endsection
