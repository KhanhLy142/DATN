@extends('admin.layouts.master')

@section('title', 'Danh sách mã giảm giá')

@section('content')
    <div class="container mt-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý mã giảm giá</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter và Search -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.discounts.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" id="searchDiscountInput" class="form-control"
                               placeholder="Tìm kiếm mã giảm giá..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select discount-filter">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Có hiệu lực</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select discount-filter">
                            <option value="">Tất cả loại</option>
                            <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Phần trăm</option>
                            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Cố định</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="date_filter" class="form-select">
                            <option value="">Tất cả thời gian</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>Tuần này</option>
                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>Tháng này</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
                Thêm mã giảm giá
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Mã giảm giá</th>
                    <th>Loại</th>
                    <th>Giá trị</th>
                    <th>Thời gian hiệu lực</th>
                    <th class="text-center" style="width: 120px;">Trạng thái</th>
                    <th>Sản phẩm áp dụng</th>
                    <th>Ngày tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($discounts as $index => $discount)
                    <tr>
                        <td>{{ $discounts->firstItem() + $index }}</td>
                        <td>
                            <strong data-discount-code="{{ $discount->code }}" class="text-primary">
                                {{ $discount->code }}
                            </strong>
                        </td>
                        <td>
                                <span class="badge {{ $discount->discount_type == 'percent' ? 'bg-success' : 'bg-info' }}">
                                    <i class="bi bi-{{ $discount->discount_type == 'percent' ? 'percent' : 'currency-dollar' }}"></i>
                                    {{ $discount->display_type }}
                                </span>
                        </td>
                        <td>
                            <strong class="text-{{ $discount->discount_type == 'percent' ? 'success' : 'info' }}">
                                {{ $discount->display_value }}
                            </strong>
                        </td>
                        <td>
                            <small>
                                <i class="bi bi-calendar-check text-success"></i> {{ $discount->start_date->format('d/m/Y') }}<br>
                                <i class="bi bi-calendar-x text-danger"></i> {{ $discount->end_date->format('d/m/Y') }}
                            </small>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = '';
                                $statusIcon = '';
                                switch($discount->status) {
                                    case 'Đang hoạt động':
                                        $statusClass = 'bg-success';
                                        $statusIcon = 'play-circle';
                                        break;
                                    case 'Chưa bắt đầu':
                                        $statusClass = 'bg-warning';
                                        $statusIcon = 'clock';
                                        break;
                                    case 'Đã hết hạn':
                                        $statusClass = 'bg-danger';
                                        $statusIcon = 'x-circle';
                                        break;
                                    default:
                                        $statusClass = 'bg-secondary';
                                        $statusIcon = 'pause-circle';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                    <i class="bi bi-{{ $statusIcon }}"></i> {{ $discount->status }}
                                </span>
                        </td>
                        <td>
                            @if($discount->products->count() > 0)
                                <span class="badge bg-primary">
                                        <i class="bi bi-box"></i> {{ $discount->products->count() }} sản phẩm
                                    </span>
                            @else
                                <span class="text-muted">
                                        <i class="bi bi-globe"></i> Tất cả sản phẩm
                                    </span>
                            @endif
                        </td>
                        <td>{{ $discount->created_at->format('d/m/Y') }}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.discounts.show', $discount) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.discounts.edit', $discount) }}"
                                   class="btn btn-outline-warning btn-sm"
                                   title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.discounts.toggle-status', $discount) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-outline-{{ $discount->is_active ? 'secondary' : 'success' }} btn-sm"
                                            title="{{ $discount->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                        <i class="bi bi-{{ $discount->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.discounts.destroy', $discount) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này không?')"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Không có mã giảm giá nào</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            @include('admin.layouts.pagination', ['paginator' => $discounts, 'itemName' => 'mã giảm giá'])
        </div>
    </div>
@endsection
