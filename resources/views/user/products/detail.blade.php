@extends('user.layouts.master')

@section('title', 'Chi tiết sản phẩm - Wet Lip Oil Gloss')

@section('content')
    <div class="container py-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => 'makeup']) }}">Trang điểm</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wet Lip Oil Gloss</li>
            </ol>
        </nav>

        <div class="row">
            {{-- Ảnh sản phẩm bên trái --}}
            <div class="col-lg-6 mb-4">
                <div class="position-sticky" style="top: 20px;">
                    {{-- Ảnh chính --}}
                    <div class="main-image-container mb-3 position-relative">
                        <img id="main-image" src="{{ asset('images/lipgloss-main.png') }}"
                             class="img-fluid rounded shadow-sm w-100" alt="Wet Lip Oil Gloss"
                             style="height: 500px; object-fit: cover;">

                        {{-- Sale badge --}}
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-danger">Sale -15%</span>
                        </div>
                    </div>

                    {{-- Ảnh phụ --}}
                    <div class="d-flex gap-2 flex-wrap">
                        <img src="{{ asset('images/lipgloss-main.png') }}"
                             class="thumb-img border rounded cursor-pointer"
                             width="80" height="80" alt="thumb1"
                             onclick="changeMainImage(this.src)">
                        <img src="{{ asset('images/lipgloss-1.png') }}"
                             class="thumb-img border rounded cursor-pointer"
                             width="80" height="80" alt="thumb2"
                             onclick="changeMainImage(this.src)">
                        <img src="{{ asset('images/lipgloss-2.png') }}"
                             class="thumb-img border rounded cursor-pointer"
                             width="80" height="80" alt="thumb3"
                             onclick="changeMainImage(this.src)">
                        <img src="{{ asset('images/lipgloss-3.png') }}"
                             class="thumb-img border rounded cursor-pointer"
                             width="80" height="80" alt="thumb4"
                             onclick="changeMainImage(this.src)">
                    </div>
                </div>
            </div>

            {{-- Thông tin sản phẩm bên phải --}}
            <div class="col-lg-6">
                <div class="mb-2">
                    <span class="badge bg-pink text-white">Trang điểm</span>
                </div>

                <h1 class="fw-bold mb-3">Wet Lip Oil Gloss Special Edition</h1>

                <div class="mb-3">
                    <span class="text-muted">Thương hiệu: </span>
                    <a href="#" class="text-pink fw-semibold text-decoration-none">DaisyBeauty</a>
                </div>

                {{-- Giá sản phẩm --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center">
                        <span class="product-price me-3">$16.25</span>
                        <span class="text-muted text-decoration-line-through fs-5">$19.12</span>
                        <span class="badge bg-danger ms-2">-15%</span>
                    </div>
                </div>

                {{-- Tùy chọn sản phẩm --}}
                <form id="product-form">
                    {{-- Dung tích --}}
                    <div class="mb-4">
                        <label class="fw-semibold mb-2">Dung tích:</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="capacity" id="cap250" value="250ml" checked>
                            <label class="btn btn-outline-secondary" for="cap250">250ml</label>

                            <input type="radio" class="btn-check" name="capacity" id="cap500" value="500ml">
                            <label class="btn btn-outline-secondary" for="cap500">500ml (+$5)</label>
                        </div>
                    </div>

                    {{-- Màu sắc --}}
                    <div class="mb-4">
                        <label class="fw-semibold mb-2">Màu sắc:</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="radio" class="btn-check" name="color" id="color1" value="clear" checked>
                            <label class="btn p-0 border" for="color1" style="width:40px; height:40px;">
                                <span class="d-block w-100 h-100 rounded" style="background:#ffffff; border:1px solid #ddd;"></span>
                            </label>

                            <input type="radio" class="btn-check" name="color" id="color2" value="pink">
                            <label class="btn p-0 border" for="color2" style="width:40px; height:40px;">
                                <span class="d-block w-100 h-100 rounded" style="background:#ffb6c1;"></span>
                            </label>

                            <input type="radio" class="btn-check" name="color" id="color3" value="coral">
                            <label class="btn p-0 border" for="color3" style="width:40px; height:40px;">
                                <span class="d-block w-100 h-100 rounded" style="background:#ff7f50;"></span>
                            </label>

                            <input type="radio" class="btn-check" name="color" id="color4" value="red">
                            <label class="btn p-0 border" for="color4" style="width:40px; height:40px;">
                                <span class="d-block w-100 h-100 rounded" style="background:#dc3545;"></span>
                            </label>
                        </div>
                        <small class="text-muted mt-1">Màu được chọn: <span id="selected-color">Trong suốt</span></small>
                    </div>

                    {{-- Số lượng --}}
                    <div class="mb-4">
                        <label class="fw-semibold mb-2">Số lượng:</label>
                        <div class="quantity-box d-flex align-items-center">
                            <button type="button" class="btn-qty" onclick="decreaseQuantity()">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="99" class="qty-input text-center mx-2">
                            <button type="button" class="btn-qty" onclick="increaseQuantity()">+</button>
                            <span class="ms-3 text-muted">Còn lại: 25 sản phẩm</span>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-3 mb-4">
                        <button type="button" class="btn btn-pink w-100 fw-semibold py-2" onclick="addToCart()">
                            <i class="bi bi-cart-plus me-2"></i>
                            Thêm vào giỏ hàng
                        </button>
                        <button type="button" class="btn btn-outline-pink w-100 fw-semibold py-2">
                            Mua ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabs mô tả --}}
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
                {{-- Mô tả sản phẩm --}}
                <div class="tab-pane fade show active" id="description">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <h5 class="fw-bold mb-3">Về sản phẩm</h5>
                            <p class="text-justify-custom">
                                Wet Lip Oil Gloss Special Edition là dòng sản phẩm chăm sóc môi cao cấp được
                                phát triển với công nghệ tiên tiến, mang lại độ ẩm lâu dài và vẻ đẹp tự nhiên
                                cho đôi môi của bạn.
                            </p>

                            <h6 class="fw-semibold mt-4 mb-3">Thành phần chính:</h6>
                            <ul>
                                <li><strong>Vitamin E</strong>: Chống oxy hóa, bảo vệ môi khỏi tác hại môi trường</li>
                                <li><strong>Hyaluronic Acid</strong>: Cấp ẩm sâu, giữ độ ẩm lâu dài</li>
                                <li><strong>Jojoba Oil</strong>: Dưỡng ẩm tự nhiên, làm mềm môi</li>
                                <li><strong>Peptide</strong>: Thúc đẩy tái tạo tế bào, chống lão hóa</li>
                            </ul>

                            <h6 class="fw-semibold mt-4 mb-3">Công dụng:</h6>
                            <ul>
                                <li>Cung cấp độ ẩm lâu dài cho môi</li>
                                <li>Tạo hiệu ứng bóng tự nhiên, không bết dính</li>
                                <li>Bảo vệ môi khỏi tia UV và tác hại môi trường</li>
                                <li>Phù hợp với mọi loại da, kể cả da nhạy cảm</li>
                            </ul>

                            <div class="alert alert-info mt-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Sản phẩm đã được kiểm nghiệm da liễu,
                                an toàn cho phụ nữ mang thai và cho con bú.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Đánh giá --}}
                <div class="tab-pane fade" id="reviews">
                    <div class="row">
                        <div class="col-md-10 mx-auto">

                            {{-- Form đánh giá --}}
                            <div class="border rounded p-4 mb-4 bg-light">
                                <h5 class="fw-bold mb-3">Viết đánh giá của bạn</h5>
                                <form id="review-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Họ tên</label>
                                            <input type="text" class="form-control" placeholder="Nhập họ tên của bạn">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" placeholder="Nhập email của bạn">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Đánh giá</label>
                                        <div class="rating-input">
                                            @for($i = 1; $i <= 5; $i++)
                                                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}">
                                                <label for="star{{ $i }}" class="text-warning fs-4 cursor-pointer">☆</label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tiêu đề đánh giá</label>
                                        <input type="text" class="form-control" placeholder="Nhập tiêu đề cho đánh giá">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nội dung đánh giá</label>
                                        <textarea class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-pink fw-semibold">
                                        <i class="bi bi-send me-2"></i>Gửi đánh giá
                                    </button>
                                </form>
                            </div>

                            {{-- Danh sách đánh giá --}}
                            <h5 class="fw-bold mb-4">Đánh giá từ khách hàng</h5>

                            @php
                                $reviews = [
                                    ['name' => 'Nguyễn Thị Mai', 'date' => '20/06/2025', 'rating' => 5, 'title' => 'Sản phẩm tuyệt vời!', 'content' => 'Mình đã dùng sản phẩm được 2 tuần và thấy môi mềm mượt hơn rất nhiều. Màu sắc tự nhiên, không bị khô môi như những sản phẩm khác.'],
                                    ['name' => 'Trần Văn Hùng', 'date' => '18/06/2025', 'rating' => 4, 'title' => 'Chất lượng tốt', 'content' => 'Mua cho vợ dùng, cô ấy rất thích. Đóng gói đẹp, giao hàng nhanh. Sẽ ủng hộ shop tiếp.'],
                                    ['name' => 'Lê Thị Hoa', 'date' => '15/06/2025', 'rating' => 5, 'title' => 'Rất hài lòng', 'content' => 'Lần đầu mua online nhưng sản phẩm vượt quá mong đợi. Độ bóng tự nhiên, không bết dính. Sẽ repurchase!'],
                                    ['name' => 'Phạm Minh Tuấn', 'date' => '12/06/2025', 'rating' => 4, 'title' => 'Tốt nhưng giá hơi cao', 'content' => 'Chất lượng không có gì để chê, nhưng giá hơi cao so với các sản phẩm tương tự. Tuy nhiên vẫn đáng để thử.']
                                ];
                            @endphp

                            @foreach($reviews as $review)
                                <div class="border rounded p-4 mb-3 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-semibold mb-1">{{ $review['name'] }}</h6>
                                            <div class="d-flex align-items-center">
                                                <div class="rating me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="text-warning">{{ $i <= $review['rating'] ? '★' : '☆' }}</span>
                                                    @endfor
                                                </div>
                                                <small class="text-muted">{{ $review['date'] }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success">Đã mua hàng</span>
                                    </div>
                                    <h6 class="fw-semibold">{{ $review['title'] }}</h6>
                                    <p class="mb-0 text-muted">{{ $review['content'] }}</p>
                                </div>
                            @endforeach

                            {{-- Load more button --}}
                            <div class="text-center mt-4">
                                <button class="btn btn-outline-pink">
                                    <i class="bi bi-arrow-down me-2"></i>
                                    Xem thêm đánh giá
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal zoom ảnh (optional) --}}
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xem ảnh sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modal-image" src="{{ asset('images/lipgloss-main.png') }}"
                         class="img-fluid" alt="Product Image">
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    // Change main image function
    function changeMainImage(src) {
        document.getElementById('main-image').src = src;
        document.getElementById('modal-image').src = src;
    }

    // Quantity functions
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

    // Add to cart function
    function addToCart() {
        const quantity = document.getElementById('quantity').value;
        const capacity = document.querySelector('input[name="capacity"]:checked').value;
        const color = document.querySelector('input[name="color"]:checked').value;

        // Simulate add to cart
        alert(`Đã thêm ${quantity} sản phẩm vào giỏ hàng!\nDung tích: ${capacity}\nMàu sắc: ${color}`);
    }

    // Color selection display
    document.addEventListener('DOMContentLoaded', function() {
        const colorInputs = document.querySelectorAll('input[name="color"]');
        const selectedColorText = document.getElementById('selected-color');

        const colorNames = {
            'clear': 'Trong suốt',
            'pink': 'Hồng nhạt',
            'coral': 'Coral',
            'red': 'Đỏ'
        };

        colorInputs.forEach(input => {
            input.addEventListener('change', function() {
                selectedColorText.textContent = colorNames[this.value];
            });
        });
    });
</script>
