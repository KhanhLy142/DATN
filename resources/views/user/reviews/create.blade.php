@extends('user.layouts.master')

@section('title', 'Đánh giá sản phẩm - ' . $product->name)

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đánh giá</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-4 p-4">
                    <h2 class="fw-bold text-pink text-center mb-4">Đánh giá sản phẩm</h2>

                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="{{ $product->main_image_url ?? asset('images/default-product.png') }}"
                                         class="img-fluid rounded"
                                         alt="{{ $product->name }}"
                                         style="max-height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-9">
                                    <h5 class="fw-bold mb-2">{{ $product->name }}</h5>
                                    @if($product->brand)
                                        <p class="text-muted mb-1">Thương hiệu: {{ $product->brand->name }}</p>
                                    @endif
                                    <p class="text-pink fw-bold mb-0">{{ number_format($product->base_price, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                        @csrf
                        @if($order)
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-bold">Đánh giá của bạn <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <div class="d-flex align-items-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}"
                                               class="d-none" {{ old('rating') == $i ? 'checked' : '' }}>
                                        <label for="star{{ $i }}" class="star-label me-1" style="font-size: 2rem; cursor: pointer; color: #ddd;">
                                            ⭐
                                        </label>
                                    @endfor
                                    <span class="ms-3 rating-text text-muted">Chọn số sao</span>
                                </div>
                                @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">Nội dung đánh giá <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comment') is-invalid @enderror"
                                      id="comment"
                                      name="comment"
                                      rows="5"
                                      placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này...">{{ old('comment') }}</textarea>
                            @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tối thiểu 10 ký tự, tối đa 1000 ký tự</div>
                        </div>

                        @if(!$hasPurchased && !$order)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Lưu ý:</strong> Bạn chưa mua sản phẩm này. Đánh giá của bạn sẽ được xem xét trước khi hiển thị.
                            </div>
                        @endif

                        <div class="text-center">
                            <button type="submit" class="btn btn-pink btn-lg me-3">
                                <i class="bi bi-send me-2"></i>Gửi đánh giá
                            </button>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-pink {
            color: #ec8ca3 !important;
        }
        .btn-pink {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
            color: white;
        }
        .btn-pink:hover {
            background-color: #e07a96;
            border-color: #e07a96;
            color: white;
        }
        .star-label {
            transition: color 0.2s;
        }
        .star-label:hover,
        .star-label.active {
            color: #ffc107 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-label');
            const ratingText = document.querySelector('.rating-text');
            const ratingTexts = [
                'Rất tệ',
                'Tệ',
                'Bình thường',
                'Tốt',
                'Rất tốt'
            ];

            stars.forEach((star, index) => {
                star.addEventListener('mouseover', function() {
                    highlightStars(index + 1);
                    ratingText.textContent = ratingTexts[index];
                });

                star.addEventListener('click', function() {
                    document.querySelector(`#star${index + 1}`).checked = true;
                    ratingText.textContent = ratingTexts[index];
                });
            });

            document.querySelector('.rating-input').addEventListener('mouseleave', function() {
                const checkedStar = document.querySelector('input[name="rating"]:checked');
                if (checkedStar) {
                    const checkedIndex = parseInt(checkedStar.value);
                    highlightStars(checkedIndex);
                    ratingText.textContent = ratingTexts[checkedIndex - 1];
                } else {
                    highlightStars(0);
                    ratingText.textContent = 'Chọn số sao';
                }
            });

            function highlightStars(count) {
                stars.forEach((star, index) => {
                    if (index < count) {
                        star.style.color = '#ffc107';
                    } else {
                        star.style.color = '#ddd';
                    }
                });
            }

            const checkedStar = document.querySelector('input[name="rating"]:checked');
            if (checkedStar) {
                const checkedIndex = parseInt(checkedStar.value);
                highlightStars(checkedIndex);
                ratingText.textContent = ratingTexts[checkedIndex - 1];
            }
        });
    </script>
@endsection
