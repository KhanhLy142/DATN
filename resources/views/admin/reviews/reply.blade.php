@extends('admin.layouts.master')

@section('title', 'Phản hồi đánh giá')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Phản hồi đánh giá</h4>

        {{-- Thông tin đánh giá --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Thông tin đánh giá</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Sản phẩm:</strong> {{ $review->product->name ?? 'N/A' }}</p>
                        <p><strong>Khách hàng:</strong> {{ $review->customer->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $review->customer->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Đánh giá:</strong>
                            <span style="letter-spacing: 1px; font-size: 18px;">{{ $review->stars }}</span>
                            ({{ $review->rating }}/5)
                        </p>
                        <p><strong>Ngày đánh giá:</strong> {{ $review->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Trạng thái:</strong> {!! $review->status_badge !!}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p><strong>Nội dung đánh giá:</strong></p>
                        <div class="bg-light p-3 rounded">
                            {{ $review->comment }}
                        </div>
                    </div>
                </div>

                {{-- Hiển thị phản hồi hiện tại nếu có --}}
                @if($review->reply)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Phản hồi hiện tại:</strong></p>
                            <div class="bg-info bg-opacity-10 p-3 rounded border-start border-info border-3">
                                {{ $review->reply }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Form phản hồi --}}
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    {{ $review->reply ? 'Cập nhật phản hồi' : 'Tạo phản hồi mới' }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.reviews.store-reply', $review->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="reply" class="form-label fw-semibold">
                            Nội dung phản hồi <span class="text-danger">*</span>
                        </label>
                        <textarea
                            name="reply"
                            id="reply"
                            rows="5"
                            class="form-control @error('reply') is-invalid @enderror"
                            placeholder="Nhập nội dung phản hồi cho khách hàng..."
                            maxlength="1000"
                            required>{{ old('reply', $review->reply) }}</textarea>

                        @error('reply')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            <small class="text-muted">
                                <span id="char-count">{{ strlen($review->reply ?? '') }}</span>/1000 ký tự
                            </small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-pink">
                                <i class="bi bi-send"></i>
                                {{ $review->reply ? 'Cập nhật phản hồi' : 'Gửi phản hồi' }}
                            </button>
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary ms-2">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>

                        {{-- Các action khác --}}
                        <div>
                            {{-- Toggle status --}}
                            <form action="{{ route('admin.reviews.toggle-status', $review->id) }}"
                                  method="POST" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="bi {{ $review->status ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    {{ $review->status ? 'Ẩn đánh giá' : 'Hiện đánh giá' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Gợi ý phản hồi --}}
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">💡 Gợi ý phản hồi</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Đánh giá tích cực (4-5 sao):</strong></p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-success suggestion-btn"
                                        data-text="Cảm ơn bạn đã tin tưởng và sử dụng sản phẩm của chúng tôi! Đánh giá tích cực của bạn là động lực to lớn để chúng tôi tiếp tục cải thiện chất lượng sản phẩm.">
                                    Cảm ơn tích cực
                                </button>
                            </li>
                            <li class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-success suggestion-btn"
                                        data-text="Rất vui khi biết bạn hài lòng với sản phẩm! Chúng tôi luôn nỗ lực mang đến những sản phẩm chất lượng nhất cho khách hàng.">
                                    Vui mừng hài lòng
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Đánh giá tiêu cực (1-3 sao):</strong></p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-warning suggestion-btn"
                                        data-text="Chúng tôi rất tiếc về trải nghiệm không tốt này. Chúng tôi sẽ xem xét và cải thiện sản phẩm dựa trên phản hồi của bạn. Xin vui lòng liên hệ với chúng tôi để được hỗ trợ tốt nhất.">
                                    Xin lỗi và hỗ trợ
                                </button>
                            </li>
                            <li class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-warning suggestion-btn"
                                        data-text="Cảm ơn bạn đã chia sẻ phản hồi thành thật. Chúng tôi sẽ kiểm tra lại quy trình và cải thiện để mang đến trải nghiệm tốt hơn cho khách hàng.">
                                    Tiếp nhận góp ý
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Đếm ký tự
            const replyTextarea = document.getElementById('reply');
            const charCount = document.getElementById('char-count');

            replyTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            // Gợi ý phản hồi
            const suggestionBtns = document.querySelectorAll('.suggestion-btn');
            suggestionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const text = this.getAttribute('data-text');
                    replyTextarea.value = text;
                    charCount.textContent = text.length;
                    replyTextarea.focus();
                });
            });
        });
    </script>
@endsection
