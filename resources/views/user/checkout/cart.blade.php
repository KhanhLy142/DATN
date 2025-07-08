@extends('user.layouts.master')

@section('title', 'Giỏ hàng của bạn')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="text-center mb-4 cart-header">
        <h2 class="fs-2 fw-bold" style="color: #ec8ca3;">🛒 Giỏ hàng của bạn</h2>
        <p class="text-muted">Xem lại các sản phẩm yêu thích trước khi thanh toán</p>
    </div>
    <div class="container py-5 cart-page">
        @if ($cartItems && $cartItems->count() > 0)
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-bold text-dark">Sản phẩm trong giỏ hàng (<span id="cartProductsCount">{{ $cartItems->count() }}</span>)</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll" checked style="accent-color: #ec8ca3;">
                                <label class="form-check-label" for="selectAll">
                                    Chọn tất cả
                                </label>
                            </div>
                        </div>

                        <div id="cartItemList">
                            @foreach ($cartItems as $item)
                                <div class="cart-item-modern mb-4" data-cart-id="{{ $item->id }}" data-price="{{ $item->price }}">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="row g-4 align-items-center">
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input item-checkbox" type="checkbox" checked
                                                               data-cart-id="{{ $item->id }}" style="accent-color: #ec8ca3; transform: scale(1.2);">
                                                    </div>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="product-image-wrapper">
                                                        @if($item->product->image)
                                                            <img src="{{ $item->product->main_image_url }}"
                                                                 class="product-image"
                                                                 alt="{{ $item->product->name }}">
                                                        @else
                                                            <img src="{{ asset('images/default-product.png') }}"
                                                                 class="product-image"
                                                                 alt="{{ $item->product->name }}">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col">
                                                    <div class="product-info">
                                                        <h6 class="product-name mb-2">
                                                            <a href="{{ route('products.show', $item->product->id) }}"
                                                               class="text-decoration-none text-dark fw-bold">
                                                                {{ $item->product->name }}
                                                            </a>
                                                        </h6>

                                                        @if($item->variant)
                                                            <div class="variant-tags mb-2">
                                                                @if($item->variant->color)
                                                                    <span class="variant-tag-simple">
                                                                        Màu sắc: {{ $item->variant->color }}
                                                                    </span>
                                                                @endif
                                                                @if($item->variant->volume)
                                                                    <span class="variant-tag-simple">
                                                                        Dung tích: {{ $item->variant->volume }}
                                                                    </span>
                                                                @endif
                                                                @if($item->variant->scent)
                                                                    <span class="variant-tag-simple">
                                                                        Mùi hương: {{ $item->variant->scent }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <div class="price-section">
                                                            <span class="current-price-large">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                                                            @php
                                                                $originalPrice = $item->variant ? $item->variant->price : $item->product->base_price;
                                                                $hasDiscount = $originalPrice > $item->price;
                                                            @endphp
                                                            @if($hasDiscount)
                                                                <br>
                                                                <span class="original-price-strike">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="quantity-wrapper">
                                                        <label class="form-label small text-muted mb-1">Số lượng</label>
                                                        <div class="quantity-control-modern">
                                                            <button class="qty-btn qty-decrease" type="button"
                                                                    data-action="decrease" data-cart-id="{{ $item->id }}">
                                                                <i class="bi bi-dash"></i>
                                                            </button>
                                                            <input type="number" class="qty-input quantity-input"
                                                                   value="{{ $item->quantity }}" min="1"
                                                                   max="{{ $item->variant ? $item->variant->stock_quantity : $item->product->stock }}"
                                                                   data-price="{{ $item->price }}" data-cart-id="{{ $item->id }}">
                                                            <button class="qty-btn qty-increase" type="button"
                                                                    data-action="increase" data-cart-id="{{ $item->id }}">
                                                                <i class="bi bi-plus"></i>
                                                            </button>
                                                        </div>
                                                        <small class="text-muted mt-1 d-block">
                                                            Còn {{ $item->variant ? $item->variant->stock_quantity : $item->product->stock }} SP
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="item-actions">
                                                        <button class="btn btn-outline-danger btn-sm remove-item"
                                                                data-cart-id="{{ $item->id }}"
                                                                title="Xóa sản phẩm">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm rounded-4 p-4 cart-summary">
                        <h5 class="fw-bold mb-4" style="color: #ec8ca3;">Tóm tắt đơn hàng</h5>

                        @if($availableDiscounts && $availableDiscounts->count() > 0)
                            <div class="available-coupons mb-4">
                                <h6 class="fw-bold mb-3">🎯 Mã giảm giá có thể sử dụng:</h6>
                                <div class="row g-2">
                                    @foreach($availableDiscounts as $discount)
                                        <div class="col-12">
                                            <div class="coupon-item border rounded p-3 position-relative"
                                                 style="cursor: pointer; transition: all 0.3s ease; border-left: 4px solid #ec8ca3 !important;"
                                                 onclick="applyCouponCode('{{ $discount->code }}')">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <span class="badge bg-danger text-white fw-bold">{{ $discount->code }}</span>
                                                        <div class="mt-1">
                                                            <small class="text-dark fw-semibold">
                                                                {{ $discount->description ?:
                                                                    ($discount->discount_type === 'fixed' ?
                                                                        'Giảm ' . number_format($discount->discount_value, 0, ',', '.') . 'đ' :
                                                                        'Giảm ' . $discount->discount_value . '%') }}
                                                            </small>
                                                        </div>
                                                        @if($discount->min_order_amount > 0)
                                                            <div class="mt-1">
                                                                <small class="text-muted">Đơn từ {{ number_format($discount->min_order_amount, 0, ',', '.') }}đ</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <i class="bi bi-arrow-right text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Hoặc nhập mã khác</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Nhập mã giảm giá" id="couponCode" style="border-color: #ec8ca3;">
                                <button class="btn btn-outline-primary" type="button" id="applyCoupon" style="border-color: #ec8ca3; color: #ec8ca3;">Áp dụng</button>
                            </div>
                            <div id="couponMessage" class="small mt-1"></div>

                            <div id="appliedCouponInfo" class="mt-2 d-none">
                                <div class="alert alert-success d-flex justify-content-between align-items-center p-2">
                                    <div>
                                        <small class="fw-bold">Mã đã áp dụng: <span id="appliedCouponCode"></span></small>
                                        <br>
                                        <small class="text-muted" id="appliedCouponDescription"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeCoupon">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính (<span id="itemCount">0</span> sản phẩm):</span>
                                <span id="subtotal">0₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="discount_line" style="display: none !important;">
                                <span>Giảm giá:</span>
                                <span class="text-success" id="discount">-0₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span id="shipping">30.000₫</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Tổng cộng:</span>
                                <span class="fs-5" style="color: #ec8ca3;" id="total">0₫</span>
                            </div>
                        </div>

                        <div id="freeShippingNotice" class="alert alert-info mt-3 d-none">
                            <small><i class="bi bi-info-circle"></i> Bạn được miễn phí vận chuyển cho đơn hàng trên 750.000₫</small>
                        </div>

                        <div id="almostFreeShippingNotice" class="alert alert-warning mt-3 d-none">
                            <small><i class="bi bi-truck"></i> Thêm <span id="remainingAmount">0₫</span> để được miễn phí vận chuyển!</small>
                        </div>

                        <div class="mt-4 d-grid gap-2">
                            <a href="{{ route('order.checkout') }}" class="btn btn-lg" id="checkoutBtn" style="background-color: #ec8ca3; color: white; border: none; border-radius: 25px; font-weight: 600; padding: 12px 24px;">
                                <i class="bi bi-credit-card me-2"></i>Đặt hàng
                            </a>
                            <a href="{{ route('products.index') }}" class="btn" style="border: 2px solid #ec8ca3; color: #ec8ca3; border-radius: 25px; font-weight: 600; padding: 10px 24px;">
                                <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center">
                    <div class="card shadow-sm rounded-4 p-5">
                        <div class="mb-4">
                            <i class="bi bi-cart3 empty-cart-icon"></i>
                        </div>
                        <h4 class="fw-bold">Giỏ hàng của bạn đang trống</h4>
                        <p class="text-muted">Hãy bắt đầu mua sắm để lấp đầy giỏ hàng nào!</p>
                        <div class="mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-lg rounded-pill" style="background-color: #ec8ca3; color: white; border: none; font-weight: 600; padding: 12px 30px;">
                                <i class="bi bi-cart-plus me-2"></i> Bắt đầu mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== CART INITIALIZATION ===');

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                showNotification('Lỗi bảo mật, vui lòng tải lại trang', 'danger');
                return;
            }

            const SHIPPING_FEE = 30000;

            let appliedCoupon = null;
            let updateTimeouts = new Map();


            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(price)) + '₫';
            }

            function showNotification(message, type = 'success') {
                document.querySelectorAll('.cart-notification').forEach(n => n.remove());

                const notification = document.createElement('div');
                notification.className = `alert alert-${type} position-fixed cart-notification`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
                notification.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${message}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                    </div>
                `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 4000);
            }

            // === UPDATE CART COUNTER FUNCTION ===
            function updateCartCounter() {
                fetch('/cart/count', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // SỬA: Chỉ tìm cart counter, không phải tất cả badge
                            const cartCounters = document.querySelectorAll('.cart-counter, #cart-count, .cart-badge, [data-cart-count]');

                            // THÊM: Loại trừ badge trong coupon-item
                            const filteredCounters = Array.from(cartCounters).filter(counter =>
                                !counter.closest('.coupon-item')
                            );

                            filteredCounters.forEach(counter => {
                                if (counter) {
                                    counter.textContent = data.count;

                                    if (data.count > 0) {
                                        counter.style.display = 'inline-block';
                                        counter.style.visibility = 'visible';
                                    } else {
                                        counter.style.display = 'none';
                                        counter.style.visibility = 'hidden';
                                    }
                                }
                            });

                            console.log('Cart counter updated:', data.count);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating cart counter:', error);
                    });
            }

            // === COUPON FUNCTIONS ===

            window.applyCouponCode = function(code) {
                const couponInput = document.getElementById('couponCode');
                if (couponInput) {
                    couponInput.value = code;
                    applyCoupon();
                }
            }

            function applyCoupon() {
                const couponInput = document.getElementById('couponCode');
                const couponCode = couponInput.value.trim().toUpperCase();
                const couponMessage = document.getElementById('couponMessage');

                if (!couponCode) {
                    couponMessage.innerHTML = '<span class="text-danger">Vui lòng nhập mã giảm giá</span>';
                    return;
                }

                // Disable button to prevent double submission
                const applyBtn = document.getElementById('applyCoupon');
                if (applyBtn) {
                    applyBtn.disabled = true;
                    applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
                }

                // Calculate current subtotal
                const subtotal = calculateSubtotal();

                fetch('/cart/apply-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: couponCode,
                        subtotal: subtotal
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            appliedCoupon = {
                                code: couponCode,
                                discount_amount: data.discount_amount || 0,
                                shipping_discount: data.shipping_discount || 0,
                                min_order_threshold: data.min_order_threshold || 0,
                                description: data.description || ''
                            };

                            // Show applied coupon info
                            showAppliedCouponInfo(couponCode, data.description || '');

                            couponMessage.innerHTML = `<span class="text-success">✓ ${data.message}</span>`;
                            couponInput.value = '';

                            updateCartSummary();
                            showNotification(data.message, 'success');
                            highlightAppliedCoupon(couponCode);
                        } else {
                            couponMessage.innerHTML = `<span class="text-danger">✗ ${data.message}</span>`;
                            showNotification(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Apply coupon error:', error);
                        couponMessage.innerHTML = '<span class="text-danger">Có lỗi xảy ra khi áp dụng mã giảm giá</span>';
                        showNotification('Có lỗi xảy ra khi áp dụng mã giảm giá', 'danger');
                    })
                    .finally(() => {
                        // Re-enable button
                        if (applyBtn) {
                            applyBtn.disabled = false;
                            applyBtn.innerHTML = 'Áp dụng';
                        }
                    });
            }

            function removeCoupon() {
                fetch('/cart/remove-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            appliedCoupon = null;

                            hideAppliedCouponInfo();
                            updateCartSummary();
                            resetCouponHighlight();

                            showNotification(data.message, 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Remove coupon error:', error);
                        showNotification('Có lỗi xảy ra khi xóa mã giảm giá', 'danger');
                    });
            }

            function showAppliedCouponInfo(code, description) {
                const appliedInfo = document.getElementById('appliedCouponInfo');
                const codeElement = document.getElementById('appliedCouponCode');
                const descElement = document.getElementById('appliedCouponDescription');

                if (appliedInfo && codeElement && descElement) {
                    codeElement.textContent = code;
                    descElement.textContent = description;
                    appliedInfo.classList.remove('d-none');
                }
            }

            function hideAppliedCouponInfo() {
                const appliedInfo = document.getElementById('appliedCouponInfo');
                if (appliedInfo) {
                    appliedInfo.classList.add('d-none');
                }
            }

            function highlightAppliedCoupon(code) {
                document.querySelectorAll('.coupon-item').forEach(item => {
                    if (item.textContent.includes(code)) {
                        item.style.background = 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)';
                        item.style.borderColor = '#28a745';
                    } else {
                        item.style.opacity = '0.6';
                    }
                });
            }

            function resetCouponHighlight() {
                document.querySelectorAll('.coupon-item').forEach(item => {
                    item.style.opacity = '1';
                    item.style.background = 'linear-gradient(135deg, #fff 0%, #f8f9fa 100%)';
                    item.style.borderColor = '#dee2e6';
                });
            }

            // === CART FUNCTIONS ===

            function calculateSubtotal() {
                let subtotal = 0;
                document.querySelectorAll('.cart-item-modern').forEach(item => {
                    const checkbox = item.querySelector('.item-checkbox');
                    if (checkbox && checkbox.checked) {
                        const price = parseFloat(item.dataset.price || 0);
                        const quantityInput = item.querySelector('.quantity-input');
                        const quantity = parseInt(quantityInput ? quantityInput.value : 0);
                        subtotal += price * quantity;
                    }
                });
                return subtotal;
            }

            function updateCartSummary() {
                const subtotal = calculateSubtotal();
                let itemCount = 0;

                // Count selected items
                document.querySelectorAll('.cart-item-modern').forEach(item => {
                    const checkbox = item.querySelector('.item-checkbox');
                    if (checkbox && checkbox.checked) {
                        const quantityInput = item.querySelector('.quantity-input');
                        const quantity = parseInt(quantityInput ? quantityInput.value : 0);
                        itemCount += quantity;
                    }
                });

                // Update display
                const subtotalElement = document.getElementById('subtotal');
                const itemCountElement = document.getElementById('itemCount');

                if (subtotalElement) subtotalElement.textContent = formatPrice(subtotal);
                if (itemCountElement) itemCountElement.textContent = itemCount;

                // Calculate shipping - Chỉ dựa vào mã giảm giá
                let shipping = SHIPPING_FEE; // Default shipping
                let isShippingFree = false;

                // Check if applied coupon gives free shipping
                if (appliedCoupon && appliedCoupon.shipping_discount > 0) {
                    shipping = Math.max(0, shipping - appliedCoupon.shipping_discount);
                    isShippingFree = shipping === 0;
                }

                // Calculate discount
                let discount = 0;
                if (appliedCoupon) {
                    discount = appliedCoupon.discount_amount || 0;
                }

                // Update shipping display
                const shippingElement = document.getElementById('shipping');
                if (shippingElement) {
                    if (isShippingFree) {
                        shippingElement.innerHTML = '<span class="text-success fw-bold">Miễn phí</span>';
                    } else {
                        shippingElement.textContent = formatPrice(shipping);
                    }
                }

                // Update discount display
                const discountElement = document.getElementById('discount');
                const discountLine = document.getElementById('discount_line');
                if (discountElement && discountLine) {
                    if (discount > 0) {
                        discountElement.textContent = '-' + formatPrice(discount);
                        discountLine.style.display = 'flex';
                    } else {
                        discountLine.style.display = 'none';
                    }
                }

                // Calculate total
                const total = Math.max(0, subtotal + shipping - discount);
                const totalElement = document.getElementById('total');
                if (totalElement) {
                    totalElement.textContent = formatPrice(total);
                }

                // Update shipping notices
                updateShippingNotices(subtotal, isShippingFree);
            }

            function updateShippingNotices(subtotal, isShippingFree) {
                const couponFreeShippingNotice = document.getElementById('couponFreeShippingNotice');

                // Hide notice first
                if (couponFreeShippingNotice) couponFreeShippingNotice.classList.add('d-none');

                // Chỉ hiển thị thông báo khi có mã freeship được áp dụng
                if (appliedCoupon && appliedCoupon.shipping_discount > 0) {
                    if (couponFreeShippingNotice) {
                        couponFreeShippingNotice.classList.remove('d-none');
                    }
                }
            }

            function removeFromCart(cartId) {
                fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ cart_id: cartId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const cartItem = document.querySelector(`.cart-item-modern[data-cart-id="${cartId}"]`);
                            if (cartItem) {
                                cartItem.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                cartItem.style.opacity = '0';
                                cartItem.style.transform = 'translateX(100%)';

                                setTimeout(() => {
                                    cartItem.remove();
                                    updateCartProductCount();
                                    updateCartSummary();
                                    updateSelectAllState();
                                    updateCartCounter(); // THÊM dòng này

                                    // Reload if no items left
                                    if (document.querySelectorAll('.cart-item-modern').length === 0) {
                                        setTimeout(() => location.reload(), 500);
                                    }
                                }, 300);
                            }
                            showNotification(data.message, 'success');
                        } else {
                            showNotification(data.message || 'Có lỗi xảy ra khi xóa sản phẩm', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Remove error:', error);
                        showNotification('Có lỗi xảy ra khi xóa sản phẩm', 'danger');
                    });
            }

            function updateQuantity(cartId, newQuantity) {
                // Clear existing timeout for this cart item
                if (updateTimeouts.has(cartId)) {
                    clearTimeout(updateTimeouts.get(cartId));
                }

                // Set new timeout
                const timeoutId = setTimeout(() => {
                    fetch('/cart/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            cart_id: cartId,
                            quantity: newQuantity
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateCartSummary();
                                updateCartCounter(); // THÊM dòng này
                            } else {
                                showNotification(data.message || 'Có lỗi xảy ra khi cập nhật số lượng', 'danger');
                                // Reset input value on error
                                const input = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
                                if (input) {
                                    input.value = input.getAttribute('data-original-value') || 1;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Update error:', error);
                            showNotification('Có lỗi xảy ra khi cập nhật số lượng', 'danger');
                        });

                    updateTimeouts.delete(cartId);
                }, 800); // 800ms delay

                updateTimeouts.set(cartId, timeoutId);
            }

            function updateCartProductCount() {
                const cartProductsCount = document.getElementById('cartProductsCount');
                if (cartProductsCount) {
                    const newCount = document.querySelectorAll('.cart-item-modern').length;
                    cartProductsCount.textContent = newCount;
                }
            }

            function updateSelectAllState() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const itemCheckboxes = document.querySelectorAll('.item-checkbox');

                if (!selectAllCheckbox || itemCheckboxes.length === 0) return;

                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === itemCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
            }

            // === EVENT LISTENERS ===

            // Remove item buttons
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const cartId = this.dataset.cartId;
                    if (cartId && confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                        removeFromCart(cartId);
                    }
                });
            });

            // Quantity buttons
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.dataset.action;
                    const cartId = this.dataset.cartId;
                    const input = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);

                    if (input) {
                        let currentValue = parseInt(input.value) || 1;
                        const min = parseInt(input.getAttribute('min')) || 1;
                        const max = parseInt(input.getAttribute('max')) || 999;

                        if (action === 'increase' && currentValue < max) {
                            input.value = currentValue + 1;
                            updateQuantity(cartId, parseInt(input.value));
                            updateCartSummary(); // Update immediately for UX
                        } else if (action === 'decrease' && currentValue > min) {
                            input.value = currentValue - 1;
                            updateQuantity(cartId, parseInt(input.value));
                            updateCartSummary(); // Update immediately for UX
                        }
                    }
                });
            });

            // Quantity input changes
            document.querySelectorAll('.quantity-input').forEach(input => {
                // Store original value
                input.setAttribute('data-original-value', input.value);

                input.addEventListener('input', function() {
                    const cartId = this.dataset.cartId;
                    const newQuantity = parseInt(this.value);
                    const min = parseInt(this.getAttribute('min')) || 1;
                    const max = parseInt(this.getAttribute('max')) || 999;

                    // Validate input
                    if (newQuantity < min) {
                        this.value = min;
                    } else if (newQuantity > max) {
                        this.value = max;
                    }

                    updateCartSummary(); // Update immediately for UX

                    if (this.value && parseInt(this.value) > 0) {
                        updateQuantity(cartId, parseInt(this.value));
                    }
                });

                input.addEventListener('blur', function() {
                    // Ensure valid value on blur
                    if (!this.value || parseInt(this.value) < 1) {
                        this.value = this.getAttribute('data-original-value') || 1;
                        updateCartSummary();
                    }
                });
            });

            // Checkbox changes
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateCartSummary();
                });
            });

            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateCartSummary();
                });
            }

            // Apply coupon button
            const applyCouponBtn = document.getElementById('applyCoupon');
            if (applyCouponBtn) {
                applyCouponBtn.addEventListener('click', applyCoupon);
            }

            // Remove coupon button
            const removeCouponBtn = document.getElementById('removeCoupon');
            if (removeCouponBtn) {
                removeCouponBtn.addEventListener('click', removeCoupon);
            }

            // Enter key in coupon input
            const couponInput = document.getElementById('couponCode');
            if (couponInput) {
                couponInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyCoupon();
                    }
                });
            }

            // === INITIALIZATION ===
            if (document.querySelectorAll('.cart-item-modern').length > 0) {
                updateCartSummary();
                updateSelectAllState();
                console.log('Cart initialized successfully');
            }

            // Update cart counter on page load
            updateCartCounter();
        });
    </script>

@endsection
