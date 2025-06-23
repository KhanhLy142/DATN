@extends('user.layouts.master')

@section('title', 'Giỏ hàng của bạn')

@section('content')
    <div class="container py-5">
        <h2 class="fw-bold mb-4 text-center text-pink">Giỏ hàng của bạn</h2>

        <div class="row">
            <!-- Danh sách sản phẩm -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm rounded-4 p-4">
                    <!-- Header giỏ hàng -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Sản phẩm trong giỏ hàng (3)</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Chọn tất cả
                            </label>
                        </div>
                    </div>

                    <!-- Sản phẩm 1 -->
                    <div class="cart-item border-bottom pb-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <input class="form-check-input item-checkbox" type="checkbox" checked>
                            </div>
                            <div class="col-md-3">
                                <img src="{{ asset('images/product1.jpg') }}" class="img-fluid rounded"
                                     style="width: 100px; height: 100px; object-fit: cover;" alt="Product">
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1">Son dưỡng môi chống nắng</h6>
                                <small class="text-muted">Thương hiệu: Innisfree</small><br>
                                <small class="text-muted">Màu sắc: Hồng nhạt</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <p class="text-pink fw-bold mb-1">120.000₫</p>
                                <small class="text-decoration-line-through text-muted">150.000₫</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="quantity-control d-flex align-items-center justify-content-center">
                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button" data-action="decrease">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control text-center mx-2 quantity-input" value="1" min="1" style="width: 60px;" data-price="120000">
                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button" data-action="increase">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-link text-danger p-0 small remove-item">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sản phẩm 2 -->
                    <div class="cart-item border-bottom pb-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <input class="form-check-input item-checkbox" type="checkbox" checked>
                            </div>
                            <div class="col-md-3">
                                <img src="{{ asset('images/product2.jpg') }}" class="img-fluid rounded"
                                     style="width: 100px; height: 100px; object-fit: cover;" alt="Product">
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1">Kem chống nắng SPF50+</h6>
                                <small class="text-muted">Thương hiệu: The Ordinary</small><br>
                                <small class="text-muted">Dung tích: 50ml</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <p class="text-pink fw-bold mb-0">220.000₫</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="quantity-control d-flex align-items-center justify-content-center">
                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button" data-action="decrease">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control text-center mx-2 quantity-input" value="2" min="1" style="width: 60px;" data-price="220000">
                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button" data-action="increase">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-link text-danger p-0 small remove-item">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sản phẩm 3 -->
                    <div class="cart-item border-bottom pb-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <input class="form-check-input item-checkbox" type="checkbox" checked>
                            </div>
                            <div class="col-md-3">
                                <img src="{{ asset('images/product3.jpg') }}" class="img-fluid rounded"
                                     style="width: 100px; height: 100px; object-fit: cover;" alt="Product">
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1">Serum Vitamin C</h6>
                                <small class="text-muted">Thương hiệu: Ordinary</small><br>
                                <small class="text-muted">Dung tích: 30ml</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <p class="text-pink fw-bold mb-0">180.000₫</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="quantity-control d-flex align-items-center justify-content-center">
                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button" data-action="decrease">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control text-center mx-2 quantity-input" value="1" min="1" style="width: 60px;" data-price="180000">
                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button" data-action="increase">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-link text-danger p-0 small remove-item">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sản phẩm gợi ý -->
                    <div class="mt-4">
                        <h6 class="mb-3">Có thể bạn cũng thích</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/product4.jpg') }}" class="rounded me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover;" alt="Suggest">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Toner làm sạch</h6>
                                                <p class="text-pink mb-0">145.000₫</p>
                                            </div>
                                            <button class="btn btn-outline-pink btn-sm add-to-cart">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/product5.jpg') }}" class="rounded me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover;" alt="Suggest">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Kem dưỡng ẩm</h6>
                                                <p class="text-pink mb-0">165.000₫</p>
                                            </div>
                                            <button class="btn btn-outline-pink btn-sm add-to-cart">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card shadow-sm rounded-4 p-4 position-sticky" style="top: 20px;">
                    <h5 class="fw-bold mb-4 text-pink">Tóm tắt đơn hàng</h5>

                    <!-- Mã giảm giá -->
                    <div class="mb-4">
                        <label class="form-label">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nhập mã giảm giá" id="couponCode">
                            <button class="btn btn-outline-pink" type="button" id="applyCoupon">Áp dụng</button>
                        </div>
                    </div>

                    <!-- Chi tiết thanh toán -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính (<span id="itemCount">3</span> sản phẩm):</span>
                            <span id="subtotal">520.000₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
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
                            <span class="text-pink fs-5" id="total">550.000₫</span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 d-grid gap-2">
                        <a href="/order-checkout" class="btn btn-pink btn-lg" id="checkoutBtn">
                            <i class="bi bi-credit-card me-2"></i>Thanh toán ngay
                        </a>
                        <a href="/san-pham" class="btn btn-outline-pink">
                            <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>

                    <!-- Hỗ trợ -->
                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Thanh toán an toàn & bảo mật
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khuyến mãi -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0 p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2 text-pink">🎉 Ưu đãi đặc biệt</h5>
                            <p class="mb-0">Mua thêm <strong id="neededAmount">200.000₫</strong> để được <strong>FREESHIP</strong> và nhận thêm 1 gift!</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-pink" role="progressbar" style="width: 69%" id="progressBar"></div>
                            </div>
                            <small class="text-muted">Còn thiếu <span id="remainingAmount">200.000₫</span></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const FREE_SHIPPING_THRESHOLD = 750000; // 750.000₫
            const SHIPPING_FEE = 30000; // 30.000₫

            // Coupons data
            const coupons = {
                'SAVE50': { discount: 50000, type: 'fixed' },
                'SAVE10': { discount: 10, type: 'percent' },
                'FREESHIP': { discount: 30000, type: 'shipping' }
            };

            let appliedCoupon = null;

            // Chọn tất cả sản phẩm
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateCartSummary();
            });

            // Xử lý checkbox từng sản phẩm
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateCartSummary();
                });
            });

            // Cập nhật trạng thái "Chọn tất cả"
            function updateSelectAllState() {
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                const selectAllCheckbox = document.getElementById('selectAll');

                selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
            }

            // Xử lý thay đổi số lượng
            document.querySelectorAll('.quantity-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    const quantityInput = this.parentElement.querySelector('.quantity-input');
                    let currentValue = parseInt(quantityInput.value);

                    if (action === 'increase') {
                        quantityInput.value = currentValue + 1;
                    } else if (action === 'decrease' && currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }

                    updateCartSummary();
                });
            });

            // Xử lý thay đổi số lượng trực tiếp trong input
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    if (parseInt(this.value) < 1) {
                        this.value = 1;
                    }
                    updateCartSummary();
                });
            });

            // Xóa sản phẩm
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                        this.closest('.cart-item').remove();
                        updateCartSummary();
                        updateSelectAllState();
                    }
                });
            });

            // Áp dụng mã giảm giá
            document.getElementById('applyCoupon').addEventListener('click', function() {
                const couponCode = document.getElementById('couponCode').value.trim().toUpperCase();

                if (coupons[couponCode]) {
                    appliedCoupon = coupons[couponCode];
                    updateCartSummary();
                    showNotification('Áp dụng mã giảm giá thành công!', 'success');
                } else {
                    showNotification('Mã giảm giá không hợp lệ!', 'error');
                }
            });

            // Thêm sản phẩm gợi ý vào giỏ hàng
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    // Có thể thêm logic thêm sản phẩm vào giỏ hàng ở đây
                });
            });

            // Cập nhật tóm tắt giỏ hàng
            function updateCartSummary() {
                let subtotal = 0;
                let itemCount = 0;

                // Tính tổng tiền các sản phẩm được chọn
                document.querySelectorAll('.cart-item').forEach(item => {
                    const checkbox = item.querySelector('.item-checkbox');
                    if (checkbox && checkbox.checked) {
                        const quantityInput = item.querySelector('.quantity-input');
                        const price = parseInt(quantityInput.dataset.price);
                        const quantity = parseInt(quantityInput.value);

                        subtotal += price * quantity;
                        itemCount += quantity;
                    }
                });

                // Tính giảm giá
                let discount = 0;
                let shippingFee = SHIPPING_FEE;

                if (appliedCoupon) {
                    if (appliedCoupon.type === 'fixed') {
                        discount = appliedCoupon.discount;
                    } else if (appliedCoupon.type === 'percent') {
                        discount = Math.round(subtotal * appliedCoupon.discount / 100);
                    } else if (appliedCoupon.type === 'shipping') {
                        shippingFee = 0;
                        discount = appliedCoupon.discount;
                    }
                }

                // Miễn phí vận chuyển nếu đủ điều kiện
                if (subtotal >= FREE_SHIPPING_THRESHOLD) {
                    shippingFee = 0;
                }

                const total = subtotal - discount + shippingFee;

                // Cập nhật giao diện
                document.getElementById('itemCount').textContent = itemCount;
                document.getElementById('subtotal').textContent = formatPrice(subtotal);
                document.getElementById('discount').textContent = discount > 0 ? `-${formatPrice(discount)}` : '-0₫';
                document.getElementById('shipping').textContent = shippingFee > 0 ? formatPrice(shippingFee) : 'Miễn phí';
                document.getElementById('total').textContent = formatPrice(total);

                // Cập nhật thanh tiến độ freeship
                updateFreeshippingProgress(subtotal);

                // Vô hiệu hóa nút thanh toán nếu không có sản phẩm nào được chọn
                const checkoutBtn = document.getElementById('checkoutBtn');
                if (itemCount === 0) {
                    checkoutBtn.classList.add('disabled');
                    checkoutBtn.style.pointerEvents = 'none';
                } else {
                    checkoutBtn.classList.remove('disabled');
                    checkoutBtn.style.pointerEvents = 'auto';
                }
            }

            // Cập nhật thanh tiến độ freeship
            function updateFreeshippingProgress(subtotal) {
                const remaining = FREE_SHIPPING_THRESHOLD - subtotal;
                const progressBar = document.getElementById('progressBar');

                if (remaining <= 0) {
                    progressBar.style.width = '100%';
                    document.getElementById('neededAmount').textContent = '0₫';
                    document.getElementById('remainingAmount').textContent = '0₫';
                    document.querySelector('.col-md-8 p').innerHTML = '🎉 <strong>Chúc mừng!</strong> Bạn đã được <strong>MIỄN PHÍ VẬN CHUYỂN</strong>!';
                } else {
                    const progress = (subtotal / FREE_SHIPPING_THRESHOLD) * 100;
                    progressBar.style.width = `${progress}%`;
                    document.getElementById('neededAmount').textContent = formatPrice(remaining);
                    document.getElementById('remainingAmount').textContent = formatPrice(remaining);
                }
            }

            // Format giá tiền
            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN').format(price) + '₫';
            }

            // Hiển thị thông báo
            function showNotification(message, type = 'info') {
                // Tạo toast notification
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
                toast.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <span>${message}</span>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

                document.body.appendChild(toast);

                // Tự động xóa sau 3 giây
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 3000);
            }

            // Khởi tạo
            updateCartSummary();
            updateSelectAllState();
        });
    </script>
@endpush
