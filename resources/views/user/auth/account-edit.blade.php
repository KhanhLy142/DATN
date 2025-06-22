@extends('user.layouts.master')

@section('title', 'Cập nhật thông tin tài khoản')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4 rounded-4">
                    <h4 class="text-center mb-4 text-pink fw-bold">Cập nhật thông tin cá nhân</h4>

                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" id="name" class="form-control rounded-3" value="Nguyễn Văn A">
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" class="form-control rounded-3" value="abc@gmail.com">
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" id="phone" class="form-control rounded-3" value="0123456789">
                            </div>

                            <div class="col-md-6">
                                <label for="birthday" class="form-label">Ngày sinh</label>
                                <input type="date" id="birthday" class="form-control rounded-3" value="2000-01-01">
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" id="address" class="form-control rounded-3"
                                       value="123 đường ABC, Quận 1, TP.HCM">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-pink rounded-pill px-4">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

