@extends('admin.layouts.master')

@section('title', 'Chi tiết nhân viên')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chi tiết nhân viên</h4>
                    <div class="card-body">
                        <div class="row">
                            <!-- Thông tin cơ bản -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Thông tin cơ bản</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Tên nhân viên:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                <span class="text-primary fs-5">{{ $staff->name }}</span>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Mã nhân viên (ID):</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                <code>NV-{{ str_pad($staff->id, 4, '0', STR_PAD_LEFT) }}</code>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Email:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                <a href="mailto:{{ $staff->email }}">{{ $staff->email }}</a>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Số điện thoại:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                @if($staff->phone)
                                                    <a href="tel:{{ $staff->phone }}">{{ $staff->phone }}</a>
                                                @else
                                                    <span class="text-muted">Chưa cập nhật</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Vai trò:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                <span class="badge
                                                    @if($staff->role == 'admin') bg-danger
                                                    @elseif($staff->role == 'sales') bg-primary
                                                    @elseif($staff->role == 'warehouse') bg-warning
                                                    @else bg-info
                                                    @endif fs-6">
                                                    {{ $staff->role_name }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Trạng thái:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-success fs-6">Hoạt động</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thông tin thời gian -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Thông tin thời gian</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Ngày tạo:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                {{ $staff->created_at->format('d/m/Y H:i:s') }}
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Cập nhật lần cuối:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                {{ $staff->updated_at->format('d/m/Y H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin bổ sung - hiển thị ngang -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Thông tin bổ sung</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            Nhân viên này đã được tạo vào {{ $staff->created_at->format('d/m/Y') }} và đang hoạt động bình thường.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút quay lại ở dưới -->
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
