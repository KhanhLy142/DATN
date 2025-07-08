@extends('admin.layouts.master')

@section('title', 'Danh sách nhà cung cấp')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h4 class="fw-bold text-center text-pink fs-2 mb-4">Danh sách nhà cung cấp</h4>
        </div>

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

        <div class="mb-4">
            <form method="GET" action="{{ route('admin.suppliers.index') }}" class="row g-3" id="searchForm">
                <div class="col-md-6">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Tìm kiếm tên, email, điện thoại..."
                           value="{{ request('search') }}"
                           id="searchInput">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" id="statusSelect">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="mb-3">
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Thêm mới</a>
        </div>

        @if(request()->hasAny(['search', 'status']))
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Kết quả tìm kiếm:</strong>
                @if(request('search'))
                    Tìm kiếm: "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('status') !== null && request('status') !== '')
                    | Trạng thái: <strong>{{ request('status') == '1' ? 'Hoạt động' : 'Ngưng hoạt động' }}</strong>
                @endif
                - Tìm thấy <strong>{{ $suppliers->total() }}</strong> kết quả
            </div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th width="8%">#</th>
                            <th width="25%">Tên nhà cung cấp</th>
                            <th width="20%">Email</th>
                            <th width="15%">Điện thoại</th>
                            <th width="20%">Địa chỉ</th>
                            <th width="12%" class="text-center">Trạng thái</th>
                            <th width="15%" class="text-center">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($suppliers as $index => $supplier)
                            <tr>
                                <td>{{ $suppliers->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $supplier->name }}</strong>
                                </td>
                                <td>
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                            {{ $supplier->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->phone)
                                        <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                            {{ $supplier->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->address)
                                        <span title="{{ $supplier->address }}">
                                            {{ Str::limit($supplier->address, 50) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $supplier->status ? 'bg-success' : 'bg-secondary' }} cursor-pointer toggle-status"
                                          data-supplier-id="{{ $supplier->id }}"
                                          data-status="{{ $supplier->status }}"
                                          title="Click để thay đổi trạng thái">
                                        {{ $supplier->status ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.suppliers.show', $supplier->id) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}"
                                           class="btn btn-outline-warning btn-sm"
                                           title="Chỉnh sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}"
                                              method="POST"
                                              style="display: inline;"
                                              onsubmit="return confirm('Bạn có chắc muốn xoá nhà cung cấp này không? Thao tác này không thể hoàn tác!')">
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        @if(request()->hasAny(['search', 'status']))
                                            <p class="mb-0">Không tìm thấy nhà cung cấp nào phù hợp</p>
                                            <small>Thử thay đổi điều kiện tìm kiếm hoặc <a href="{{ route('admin.suppliers.index') }}">xem tất cả</a></small>
                                        @else
                                            <p class="mb-0">Không có nhà cung cấp nào</p>
                                            <small>Nhấn "Thêm mới" để tạo nhà cung cấp đầu tiên</small>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($suppliers->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('searchInput').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('searchForm').submit();
                    }
                });

                document.getElementById('statusSelect').addEventListener('change', function() {
                    document.getElementById('searchForm').submit();
                });

                document.querySelectorAll('.toggle-status').forEach(function(element) {
                    element.addEventListener('click', function() {
                        const supplierId = this.dataset.supplierId;
                        const currentStatus = this.dataset.status;

                        if (confirm('Bạn có chắc muốn thay đổi trạng thái nhà cung cấp này?')) {
                            fetch(`/admin/suppliers/${supplierId}/toggle-status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        location.reload();
                                    } else {
                                        alert('Có lỗi xảy ra khi cập nhật trạng thái!');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Có lỗi xảy ra khi cập nhật trạng thái!');
                                });
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
