@extends('admin.layouts.master')

@section('title', 'Quản lý đánh giá')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách đánh giá sản phẩm</h4>

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Lọc theo sản phẩm</label>
                    <select name="product_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Lọc theo sao</label>
                    <select name="rating" class="form-select">
                        <option value="">Tất cả</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} sao
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-pink me-2">Lọc</button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

        {{-- Bảng đánh giá --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center shadow-sm bg-white">
                <thead class="bg-light">
                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Khách hàng</th>
                    <th>Đánh giá</th>
                    <th>Bình luận</th>
                    <th>Phản hồi</th>
                    <th>Ngày đánh giá</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reviews as $index => $review)
                    <tr>
                        <td>{{ $reviews->firstItem() + $index }}</td>
                        <td>{{ $review->product->name ?? 'N/A' }}</td>
                        <td>{{ $review->customer->name ?? 'N/A' }}</td>
                        <td>
                            <div style="letter-spacing: 1px; font-size: 18px;">
                                {{ $review->stars }}
                            </div>
                            <small class="text-muted">({{ $review->rating }}/5)</small>
                        </td>
                        <td>
                            <span title="{{ $review->comment }}">
                                {{ $review->short_comment }}
                            </span>
                        </td>
                        <td>
                            @if($review->reply)
                                <span class="badge bg-info">Đã phản hồi</span>
                                <br>
                                <small title="{{ $review->reply }}">
                                    {{ Str::limit($review->reply, 30) }}
                                </small>
                            @else
                                <span class="badge bg-secondary">Chưa phản hồi</span>
                            @endif
                        </td>
                        <td>{{ $review->created_at->format('d/m/Y') }}</td>
                        <td>{!! $review->status_badge !!}</td>
                        <td>
                            {{-- Phản hồi --}}
                            <a href="{{ route('admin.reviews.reply', $review->id) }}"
                               class="btn btn-sm btn-outline-info me-1" title="Phản hồi">
                                <i class="bi bi-chat-dots"></i>
                            </a>

                            {{-- Ẩn/Hiện --}}
                            <form action="{{ route('admin.reviews.toggle-status', $review->id) }}"
                                  method="POST" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-warning me-1"
                                        title="{{ $review->status ? 'Ẩn đánh giá' : 'Hiện đánh giá' }}">
                                    <i class="bi {{ $review->status ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                </button>
                            </form>

                            {{-- Xoá --}}
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}"
                                  method="POST" class="d-inline-block"
                                  onsubmit="return confirm('Bạn có chắc muốn xoá đánh giá này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-muted py-4">
                            <i class="bi bi-inbox"></i> Không có đánh giá nào
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang tùy chỉnh --}}
        @if($reviews->hasPages())
            <div class="d-flex justify-content-center mt-4">
                @include('admin.layouts.pagination', [
                    'paginator' => $reviews,
                    'itemName' => 'đánh giá'
                ])
            </div>
        @endif

        {{-- Thống kê tổng quan --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thống kê đánh giá</h5>
                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="fw-bold text-primary">{{ $reviews->total() }}</div>
                                <small class="text-muted">Tổng đánh giá</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-success">
                                    {{ $reviews->where('status', 1)->count() }}
                                </div>
                                <small class="text-muted">Đang hiển thị</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-warning">
                                    {{ $reviews->where('status', 0)->count() }}
                                </div>
                                <small class="text-muted">Đã ẩn</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-info">
                                    {{ $reviews->whereNotNull('reply')->count() }}
                                </div>
                                <small class="text-muted">Đã phản hồi</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-secondary">
                                    {{ $reviews->whereNull('reply')->count() }}
                                </div>
                                <small class="text-muted">Chưa phản hồi</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-pink">
                                    {{ number_format($reviews->avg('rating'), 1) }}
                                </div>
                                <small class="text-muted">Điểm TB</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
