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

                        <!-- Chi tiết thanh toán -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính (3 sản phẩm):</span>
                                <span>560.000₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá:</span>
                                <span class="text-success">-50.000₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span>30.000₫</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Tổng cộng:</span>
                                <span class="text-pink fs-5">540.000₫</span>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4 d-grid gap-2">
                            <a href="/order-checkout" class="btn btn-pink btn-lg">
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
                                <p class="mb-0">Mua thêm <strong>160.000₫</strong> để được <strong>FREESHIP</strong> và nhận thêm 1 gift!</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-pink" role="progressbar" style="width: 75%"></div>
                                </div>
                                <small class="text-muted">Còn thiếu 160.000₫</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection

        @push('scripts')
            <script>
                // Chọn tất cả sản phẩm
                document.getElementById('selectAll').addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.cart-item input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                // Cập nhật số lượng
                document.querySelectorAll('.quantity-control button').forEach(button => {
                    button.addEventListener('click', function() {
                        const input = this.parentElement.querySelector('input[type="number"]');
                        const isIncrease = this.querySelector('.bi-plus');

                        if (isIncrease) {
                            input.value = parseInt(input.value) + 1;
                        } else if (parseInt(input.value) > 1) {
                            input.value = parseInt(input.value) - 1;
                        }

                        // Cập nhật tổng tiền (có thể thêm logic tính toán ở đây)
                    });
                });
            </script>
        @endpush
    </div>

    <!-- Sản phẩm 1 -->
    <div class="cart-item border-bottom pb-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-1">
                <input class="form-check-input" type="checkbox" checked>
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
                    <button class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" class="form-control text-center mx-2" value="1" min="1" style="width: 60px;">
                    <button class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <button class="btn btn-link text-danger p-0 small">
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
                <input class="form-check-input" type="checkbox" checked>
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
                    <button class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" class="form-control text-center mx-2" value="2" min="1" style="width: 60px;">
                    <button class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <button class="btn btn-link text-danger p-0 small">
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
                            <img src="{{ asset('images/product3.jpg') }}" class="rounded me-3"
                                 style="width: 60px; height: 60px; object-fit: cover;" alt="Suggest">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Serum Vitamin C</h6>
                                <p class="text-pink mb-0">180.000₫</p>
                            </div>
                            <button class="btn btn-outline-pink btn-sm">
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
                            <img src="{{ asset('images/product4.jpg') }}" class="rounded me-3"
                                 style="width: 60px; height: 60px; object-fit: cover;" alt="Suggest">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Toner làm sạch</h6>
                                <p class="text-pink mb-0">145.000₫</p>
                            </div>
                            <button class="btn btn-outline-pink btn-sm">
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
                    <input type="text" class="form-control" placeholder="Nhập mã giảm giá">
                    <button class="btn btn-outline-pink" type="button">Áp dụng</button>
                </div>
