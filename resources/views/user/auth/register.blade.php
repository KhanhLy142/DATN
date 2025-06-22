@extends('user.layouts.master')

@section('title', 'Đăng ký')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4 rounded-4">
                    <h2 class="text-center mb-4 text-pink fw-bold">Tạo tài khoản mới</h2>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" class="form-control rounded-3" placeholder="Nguyễn Văn A">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control rounded-3" placeholder="abc@gmail.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control rounded-3" placeholder="********">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control rounded-3" placeholder="********">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control rounded-3">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control rounded-3" placeholder="0123 456 789">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control rounded-3"
                                       placeholder="123 Đường ABC, Quận 1, TP.HCM">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-pink rounded-pill px-4 py-2"
                                    style="width: 180px; font-size: 15px;">
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
