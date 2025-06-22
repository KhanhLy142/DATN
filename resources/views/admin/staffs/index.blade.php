@extends('admin.layouts.master')

@section('title', 'Quản lý nhân viên')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Quản lý nhân viên</h4>
                    <div class="card-body">
                        <!-- Form tìm kiếm và lọc -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('admin.staffs.index') }}" class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Tìm kiếm nhân viên..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="role" class="form-select">
                                            <option value="">Tất cả vai trò</option>
                                            @foreach($roles as $key => $value)
                                                <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                        <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Nút thêm mới -->
                        <div class="mb-3">
                            <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
                                Thêm mới
                            </a>
                        </div>

                        <!-- Bảng danh sách nhân viên -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên nhân viên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Vai trò</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($staffs as $index => $staff)
                                    <tr>
                                        <td>{{ $staffs->firstItem() + $index }}</td>
                                        <td>{{ $staff->name }}</td>
                                        <td>{{ $staff->email }}</td>
                                        <td>{{ $staff->phone ?: '-' }}</td>
                                        <td>
                                            @if($staff->role == 'admin')
                                                <span class="badge bg-danger">{{ $staff->role_name }}</span>
                                            @elseif($staff->role == 'sales')
                                                <span class="badge bg-primary">{{ $staff->role_name }}</span>
                                            @elseif($staff->role == 'warehouse')
                                                <span class="badge bg-warning">{{ $staff->role_name }}</span>
                                            @else
                                                <span class="badge bg-info">{{ $staff->role_name }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $staff->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.staffs.show', $staff) }}"
                                                   class="btn btn-sm btn-outline-info" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.staffs.edit', $staff) }}"
                                                   class="btn btn-sm btn-outline-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.staffs.destroy', $staff) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
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
                                                <p>Không có nhân viên nào được tìm thấy</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        @include('admin.layouts.pagination', ['paginator' => $staffs, 'itemName' => 'nhân viên'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
