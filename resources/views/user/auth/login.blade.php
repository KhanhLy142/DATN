@extends('user.layouts.master')

@section('title', 'Đăng nhập')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm p-4 rounded-4">
                    <h2 class="text-center mb-4 text-pink fw-bold">Đăng nhập tài khoản</h2>

                    {{-- Hiển thị lỗi nếu có --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Hiển thị thông báo lỗi đăng nhập --}}
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control rounded-3"
                                   placeholder="abc@gmail.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="position-relative">
                                <input type="password" id="password" name="password" class="form-control rounded-3 pe-5"
                                       placeholder="********" required>
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2 p-0 border-0 bg-transparent password-toggle"
                                        data-target="password" style="z-index: 10;">
                                    <i class="bi bi-eye text-muted" id="password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-pink rounded-pill px-4 py-2" style="width: 160px;">
                                Đăng nhập
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="text-muted mb-1">Chưa có tài khoản?
                                <a href="{{ route('register') }}" class="text-pink fw-semibold">Đăng ký ngay</a>
                            </p>
                            <p class="mb-0"><a href="#" class="text-decoration-none text-muted">Quên mật khẩu?</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
