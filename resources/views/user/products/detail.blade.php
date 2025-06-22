@extends('user.layouts.master')

@section('title', 'Wet Lip Oil Gloss')

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Ảnh sản phẩm bên trái -->
            <div class="col-md-6">
                <!-- Ảnh chính -->
                <img id="main-image" src="{{ asset('images/lipgloss-main.png') }}"
                     class="img-fluid mb-3 rounded shadow-sm" alt="Wet Lip Oil">

                <!-- Ảnh phụ -->
                <div class="d-flex gap-3">
                    <img src="{{ asset('images/lipgloss-main.png') }}" class="thumb-img border rounded" width="60"
                         alt="thumb1">
                    <img src="{{ asset('images/lipgloss-1.png') }}" class="thumb-img border rounded" width="60"
                         alt="thumb2">
                    <img src="{{ asset('images/lipgloss-2.png') }}" class="thumb-img border rounded" width="60"
                         alt="thumb3">
                </div>
            </div>

            <!-- Thông tin sản phẩm bên phải -->
            <div class="col-md-6">
                <h2 class="fw-bold mb-3">Wet Lip Oil Gloss Special type at the store</h2>

                <p class="product-price mb-4 fw-bold">$19.12</p>

                <p>Regular fit, round neckline, short sleeves. Made of extra long staple pima cotton.</p>

                <!-- Dung tích -->
                <div class="mb-3">
                    <label class="fw-semibold">Dung tích:</label>
                    <select class="form-select w-auto d-inline-block ms-2">
                        <option>250ml</option>
                        <option>500ml</option>
                    </select>
                </div>

                <!-- Màu sắc -->
                <div class="mb-3">
                    <label class="fw-semibold">Màu sắc:</label>
                    <span class="ms-3 me-2 d-inline-block rounded-circle"
                          style="width:24px; height:24px; background:#ffffff; border:1px solid #ccc;"></span>
                    <span class="me-2 d-inline-block rounded-circle"
                          style="width:24px; height:24px; background:#333;"></span>
                </div>

                <!-- Số lượng -->
                <div class="mb-4">
                    <label class="fw-semibold">Số lượng:</label>
                    <input type="number" value="1" min="1" class="form-control d-inline-block w-auto ms-2"
                           style="width:70px;">
                </div>

                <button class="btn btn-pink px-4 py-2 fw-bold">Thêm vào giỏ hàng</button>
            </div>
        </div>

        <!-- Tabs mô tả -->
        <div class="mt-5">
            <ul class="nav nav-tabs justify-content-center mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#details">Product Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews</a>
                </li>
            </ul>

            <div class="tab-content pt-3">
                <div class="tab-pane fade show active" id="description">
                    <p>
                        Symbol of lightness and delicacy, the hummingbird evokes curiosity and joy. Inspired by
                        traditional Japanese origami.
                    </p>
                </div>

                <div class="tab-pane fade" id="details">
                    <ul>
                        <li>Material: Premium pima cotton</li>
                        <li>Care: Hand wash recommended</li>
                    </ul>
                </div>

                <div class="tab-pane fade" id="reviews">
                    <!-- Danh sách đánh giá -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Đánh giá của khách hàng</h5>

                        <!-- Đánh giá mẫu -->
                        <div class="border rounded p-3 mb-3 shadow-sm">
                            <div class="d-flex align-items-center mb-2">
                                <!-- Hiển thị 4/5 sao -->
                                <div class="me-2 text-warning">
                                    ★★★★☆
                                </div>
                                <strong class="me-2">Nguyễn Văn A</strong>
                                <span class="text-muted small">- 22/05/2025</span>
                            </div>
                            <p>Sản phẩm rất đẹp và mùi thơm dễ chịu, giao hàng nhanh!</p>
                        </div>

                        <!-- Đánh giá mẫu khác -->
                        <div class="border rounded p-3 mb-3 shadow-sm">
                            <div class="d-flex align-items-center mb-2">
                                <!-- Hiển thị 5/5 sao -->
                                <div class="me-2 text-warning">
                                    ★★★★★
                                </div>
                                <strong class="me-2">Trần Thị B</strong>
                                <span class="text-muted small">- 18/05/2025</span>
                            </div>
                            <p>Rất ưng ý! Màu đẹp và không bị khô môi.</p>
                        </div>
                    </div>

                    <!-- Form đánh giá -->
                    <div class="mt-4">
                        <h5 class="fw-bold mb-3">Gửi đánh giá của bạn</h5>
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Số sao đánh giá:</label>
                                <select class="form-select w-auto">
                                    <option value="5">★★★★★ - Rất hài lòng</option>
                                    <option value="4">★★★★☆ - Hài lòng</option>
                                    <option value="3">★★★☆☆ - Bình thường</option>
                                    <option value="2">★★☆☆☆ - Không hài lòng</option>
                                    <option value="1">★☆☆☆☆ - Rất tệ</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bình luận:</label>
                                <textarea class="form-control" rows="4" placeholder="Viết cảm nhận của bạn..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-pink px-4 fw-semibold">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
