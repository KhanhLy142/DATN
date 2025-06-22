@extends('admin.layouts.master')

@section('title', 'Chi tiết nhà cung cấp')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="fw-bold text-pink fs-2 mb-0">Chi tiết nhà cung cấp</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Tên nhà cung cấp:</strong> {{ $supplier->name }}
                        </div>

                        <div class="mb-3">
                            <strong>Email:</strong> {{ $supplier->email ?: 'Chưa có' }}
                        </div>

                        <div class="mb-3">
                            <strong>Số điện thoại:</strong> {{ $supplier->phone ?: 'Chưa có' }}
                        </div>

                        <div class="mb-3">
                            <strong>Trạng thái:</strong>
                            <span class="badge {{ $supplier->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $supplier->status ? 'Hoạt động' : 'Ngưng hoạt động' }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Địa chỉ:</strong> {{ $supplier->address ?: 'Chưa có địa chỉ' }}
                        </div>

                        <div class="mb-3">
                            <strong>Ngày tạo:</strong> {{ $supplier->created_at->format('d/m/Y H:i') }}
                        </div>

                        <div class="mb-3">
                            <strong>Cập nhật lần cuối:</strong> {{ $supplier->updated_at->format('d/m/Y H:i') }}
                        </div>

                        @if($supplier->brands()->count() > 0)
                            <div class="mb-3">
                                <strong>Thương hiệu liên kết ({{ $supplier->brands()->count() }}):</strong>
                            </div>
                            <div class="row">
                                @foreach($supplier->brands as $brand)
                                    <div class="col-md-4 mb-2">
                                        <div class="card card-body bg-light">
                                            <h6 class="card-title mb-1">{{ $brand->name }}</h6>
                                            <small class="text-muted">{{ $brand->country ?: 'N/A' }}</small>
                                            <span class="badge {{ $brand->status ? 'bg-success' : 'bg-danger' }} mt-1">
                                                {{ $brand->status ? 'Hoạt động' : 'Ngưng' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Nhà cung cấp này chưa có thương hiệu nào.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
