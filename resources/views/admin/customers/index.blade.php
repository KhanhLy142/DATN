@extends('admin.layouts.master')

@section('title', 'Quản lý khách hàng')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý khách hàng</h4>
                    <div class="card-body">
                        {{-- Thông báo --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Form tìm kiếm và lọc -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Tìm kiếm khách hàng..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" name="from_date" class="form-control"
                                               placeholder="Từ ngày" value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" name="to_date" class="form-control"
                                               placeholder="Đến ngày" value="{{ request('to_date') }}">
                                    </div>
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Nút thêm mới -->
                        <div class="mb-3">
                            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                                Thêm mới
                            </a>
                        </div>

                        <!-- Bảng danh sách khách hàng -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên khách hàng</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Số đơn hàng</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($customers as $index => $customer)
                                    <tr>
                                        <td>{{ $customers->firstItem() + $index }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone ?: '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $customer->orders_count }} đơn</span>
                                        </td>
                                        <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.customers.show', $customer) }}"
                                                   class="btn btn-sm btn-outline-info" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                                   class="btn btn-sm btn-outline-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if($customer->orders_count == 0)
                                                    <form action="{{ route('admin.customers.destroy', $customer) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            style="cursor: not-allowed;" title="Không thể xóa khách hàng có đơn hàng"
                                                            disabled>
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <p>Không có khách hàng nào được tìm thấy</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        @include('admin.layouts.pagination', ['paginator' => $customers, 'itemName' => 'khách hàng'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
