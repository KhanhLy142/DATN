@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
    <div class="container py-5">
        <h4 class="fw-bold text-center text-pink fs-2 mb-4">Chỉnh sửa khách hàng: {{ $customer->name }}</h4>

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

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                            @csrf
                            @method('PUT')

                            {{-- Tên khách hàng --}}
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Tên khách hàng *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $customer->name) }}"
                                       placeholder="Nhập tên đầy đủ" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $customer->email) }}"
                                       placeholder="example@email.com" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Mật khẩu mới</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password"
                                           placeholder="Để trống nếu không thay đổi">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu. Mật khẩu mới phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt</div>
                            </div>

                            {{-- Số điện thoại --}}
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                                       placeholder="0123456789">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="3"
                                          placeholder="Nhập địa chỉ đầy đủ">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hiển thị lỗi tổng quát --}}
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <h6 class="fw-bold">Có lỗi xảy ra:</h6>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Nút submit --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="submit" class="btn btn-pink px-4 py-2">
                                    Cập nhật
                                </button>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary px-4 py-2">
                                    Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
