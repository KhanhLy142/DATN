@extends('admin.layouts.master')

@section('title', 'Danh sách nhà cung cấp')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách nhà cung cấp</h4>
                        <div class="mb-3">
                            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                                Thêm mới
                            </a>
                        </div>
                    <div class="card-body">
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

                        <!-- Responsive table wrapper -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <!-- Thay đổi từ table-dark thành table-light -->
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên nhà cung cấp</th>
                                    <th>Email</th>
                                    <th>Điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                                        <td>
                                            <strong>{{ $supplier->name }}</strong>
                                        </td>
                                        <td>
                                            @if($supplier->email)
                                                <a href="mailto:{{ $supplier->email }}" class="text-decoration-none text-truncate d-block" style="max-width: 150px;">
                                                    {{ $supplier->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($supplier->phone)
                                                <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                                    {{ $supplier->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($supplier->address)
                                                <span title="{{ $supplier->address }}" class="text-truncate d-block" style="max-width: 180px;">
                                                    {{ Str::limit($supplier->address, 40) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $supplier->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $supplier->status ? 'Hoạt động' : 'Ngưng' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                                   class="btn btn-outline-info btn-sm" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                                   class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST"
                                                      style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                                <p class="mb-0">Chưa có nhà cung cấp nào</p>
                                                <small>Nhấn "Thêm mới" để tạo nhà cung cấp đầu tiên</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                            @include('admin.layouts.pagination', ['paginator' => $suppliers, 'itemName' => 'nhà cung cấp'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
