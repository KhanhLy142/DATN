@extends('admin.layouts.master')

@section('title', 'Chi tiết mã giảm giá')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết mã giảm giá</h4>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-tag-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="card-title text-primary mb-0" data-discount-code="{{ $discount->code }}">
                        {{ $discount->code }}
                    </h5>
                    <button type="button" class="btn btn-outline-primary btn-sm ms-3" onclick="copyDiscountCode()">
                        <i class="bi bi-copy"></i> Sao chép
                    </button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-{{ $discount->discount_type == 'percent' ? 'percent' : 'currency-dollar' }} me-2 text-{{ $discount->discount_type == 'percent' ? 'success' : 'info' }}"></i>
                            <strong>Loại giảm giá:</strong>
                            <span class="badge {{ $discount->discount_type == 'percent' ? 'bg-success' : 'bg-info' }}">
                                {{ $discount->display_type }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-cash-coin me-2 text-warning"></i>
                            <strong>Giá trị giảm:</strong>
                            <strong class="text-{{ $discount->discount_type == 'percent' ? 'success' : 'info' }}">
                                {{ $discount->display_value }}
                            </strong>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-cart-check me-2 text-warning"></i>
                            <strong>Đơn hàng tối thiểu:</strong>
                            @if($discount->min_order_amount > 0)
                                <span class="badge bg-warning text-dark">
                                    {{ number_format($discount->min_order_amount, 0, ',', '.') }}đ
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-infinity"></i> Không giới hạn
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            @php
                                $statusClass = '';
                                $statusIcon = '';
                                switch($discount->status) {
                                    case 'Đang hoạt động':
                                        $statusClass = 'success';
                                        $statusIcon = 'play-circle';
                                        break;
                                    case 'Chưa bắt đầu':
                                        $statusClass = 'warning';
                                        $statusIcon = 'clock';
                                        break;
                                    case 'Đã hết hạn':
                                        $statusClass = 'danger';
                                        $statusIcon = 'x-circle';
                                        break;
                                    default:
                                        $statusClass = 'secondary';
                                        $statusIcon = 'pause-circle';
                                }
                            @endphp
                            <i class="bi bi-{{ $statusIcon }} me-2 text-{{ $statusClass }}"></i>
                            <strong>Trạng thái:</strong>
                            <span class="badge bg-{{ $statusClass }}">
                                {{ $discount->status }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-toggle-{{ $discount->is_active ? 'on' : 'off' }} me-2 text-{{ $discount->is_active ? 'success' : 'danger' }}"></i>
                            <strong>Kích hoạt:</strong>
                            <span class="badge {{ $discount->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $discount->is_active ? 'Có' : 'Không' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-calendar-check me-2 text-success"></i>
                            <strong>Ngày bắt đầu:</strong> {{ $discount->start_date->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-calendar-x me-2 text-danger"></i>
                            <strong>Ngày kết thúc:</strong> {{ $discount->end_date->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-calendar-plus me-2 text-success"></i>
                            <strong>Ngày tạo:</strong> {{ $discount->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">
                            <i class="bi bi-clock-history me-2 text-warning"></i>
                            <strong>Cập nhật lần cuối:</strong> {{ $discount->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle"></i> Điều kiện áp dụng mã giảm giá
                        </h6>
                        <p class="mb-1">
                            <strong>Giá trị giảm:</strong> {{ $discount->display_value }}
                        </p>
                        <p class="mb-1">
                            <strong>Đơn hàng tối thiểu:</strong>
                            @if($discount->min_order_amount > 0)
                                {{ number_format($discount->min_order_amount, 0, ',', '.') }}đ
                            @else
                                Không giới hạn
                            @endif
                        </p>
                        <p class="mb-0">
                            <strong>Thời gian hiệu lực:</strong>
                            {{ $discount->start_date->format('d/m/Y') }} - {{ $discount->end_date->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="mb-2">
                        <i class="bi bi-box-seam me-2 text-info"></i>
                        <strong>Sản phẩm áp dụng:</strong>
                    </p>

                    @if($discount->products->count() > 0)
                        <div class="mb-3">
                            <span class="badge bg-primary p-2 mb-2">
                                <i class="bi bi-box"></i> {{ $discount->products->count() }} sản phẩm được chọn
                            </span>
                            <div class="mt-2">
                                @foreach($discount->products->take(10) as $product)
                                    <span class="badge bg-light text-dark me-2 mb-2 border p-2">
                                        <i class="bi bi-box me-1"></i>{{ $product->name }}
                                        <small class="text-muted">({{ number_format($product->price ?? 0, 0, ',', '.') }} VNĐ)</small>
                                    </span>
                                @endforeach
                                @if($discount->products->count() > 10)
                                    <span class="text-muted">... và {{ $discount->products->count() - 10 }} sản phẩm khác</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-globe"></i>
                            Mã giảm giá này áp dụng cho <strong>tất cả sản phẩm</strong> trong cửa hàng.
                        </div>
                    @endif
                </div>

                <div class="border-top pt-3 mt-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-lightning-charge text-warning"></i> Thao tác nhanh
                            </h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <form action="{{ route('admin.discounts.toggle-status', $discount) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-{{ $discount->is_active ? 'warning' : 'success' }} btn-sm">
                                        <i class="bi bi-{{ $discount->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $discount->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                </a>

                                <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này không?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Xóa mã giảm giá
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>

        @if($discount->products->count() > 0)
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check text-primary"></i>
                        Chi tiết sản phẩm áp dụng ({{ $discount->products->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá gốc</th>
                                <th>Giá sau giảm</th>
                                <th>Tiết kiệm</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($discount->products as $product)
                                <tr>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                 alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                 style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->sku)
                                            <br><small class="text-muted">SKU: {{ $product->sku }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $originalPrice = (float) ($product->price ?? 0);
                                        @endphp
                                        <span class="text-muted">{{ number_format($originalPrice, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                    <td>
                                        @php
                                            try {
                                                $discountAmount = $discount->calculateDiscount($originalPrice);
                                                $finalPrice = max(0, $originalPrice - $discountAmount);
                                            } catch (Exception $e) {
                                                $discountAmount = 0;
                                                $finalPrice = $originalPrice;
                                            }
                                        @endphp
                                        <strong class="text-success">{{ number_format($finalPrice, 0, ',', '.') }} VNĐ</strong>
                                    </td>
                                    <td>
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-down"></i>
                                                {{ number_format($discountAmount, 0, ',', '.') }} VNĐ
                                            </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $product) }}"
                                           class="btn btn-outline-info btn-sm" target="_blank" title="Xem sản phẩm">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function copyDiscountCode() {
            const code = document.querySelector('[data-discount-code]').getAttribute('data-discount-code');
            navigator.clipboard.writeText(code).then(function() {
                // Show success message
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i> Đã sao chép';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            });
        }
    </script>
@endsection
