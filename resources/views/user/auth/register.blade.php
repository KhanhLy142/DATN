@extends('user.layouts.master')

@section('title', 'Đăng ký')

@section('content')
    <div class="container py-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4 rounded-4">
                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="Nguyễn Văn A">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control rounded-3" placeholder="abc@gmail.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mật khẩu</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="register-password" class="form-control rounded-3 pe-5" placeholder="********">
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2 p-0 border-0 bg-transparent password-toggle"
                                            data-target="register-password" style="z-index: 10;">
                                        <i class="bi bi-eye text-muted" id="register-password-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Xác nhận mật khẩu</label>
                                <div class="position-relative">
                                    <input type="password" name="password_confirmation" id="confirm-password" class="form-control rounded-3 pe-5" placeholder="********">
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2 p-0 border-0 bg-transparent password-toggle"
                                            data-target="confirm-password" style="z-index: 10;">
                                        <i class="bi bi-eye text-muted" id="confirm-password-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control rounded-3" placeholder="0123 456 789">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" name="address" class="form-control rounded-3" placeholder="123 Đường ABC, Quận 1, TP.HCM">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-pink rounded-pill px-4 py-2" style="width: 180px; font-size: 15px;">
                                Đăng ký
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="text-muted mb-0">Đã có tài khoản?
                                <a href="/login" class="text-pink fw-semibold">Đăng nhập ngay</a>
                            </p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
