@extends('user.layouts.master')

@section('title', 'Đơn đặt hàng')

@section('content')
    <div class="container py-5">
        <h2 class="fw-bold mb-4 text-center text-pink">Đơn đặt hàng</h2>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card p-4 shadow-sm rounded-4">
                    <h5 class="fw-bold mb-3">Thông tin giao hàng</h5>

                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" placeholder="Nhập họ tên">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" placeholder="0123 456 789">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ nhận hàng</label>
                            <textarea class="form-control" id="address" rows="3"
                                      placeholder="Số nhà, đường, quận, thành phố..."></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card p-4 shadow-sm rounded-4">
                    <h5 class="fw-bold mb-3">Đơn hàng của bạn</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Son dưỡng môi (x1)</span>
                        <strong>120.000₫</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Kem chống nắng (x2)</span>
                        <strong>440.000₫</strong>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <strong>560.000₫</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <strong>30.000₫</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Tổng cộng:</span>
                        <strong class="text-pink">590.000₫</strong>
                    </div>
                </div>

                <div class="card p-4 shadow-sm rounded-4 mt-4">
                    <h5 class="fw-bold mb-3">Phương thức thanh toán</h5>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment" id="cod" checked>
                        <label class="form-check-label" for="cod">
                            Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment" id="bank">
                        <label class="form-check-label" for="bank">
                            Chuyển khoản ngân hàng
                        </label>
                    </div>

                    <button class="btn btn-pink w-100 mt-4 rounded-pill">Xác nhận đặt hàng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
