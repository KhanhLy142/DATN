@extends('user.layouts.master')

@section('title', 'Sản phẩm')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">

            {{-- BỘ LỌC SẢN PHẨM --}}
            <aside class="col-md-3 mb-4">
                <div class="p-3 border rounded shadow-sm">
                    <h5 class="fw-bold mb-4 text-center">Lọc sản phẩm</h5>

                    <form method="GET" action="{{ route('products.index') }}">
                        {{-- Thương hiệu --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold">Thương hiệu</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="brand" id="brand1" value="Innisfree">
                                <label class="form-check-label" for="brand1">Innisfree</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="brand" id="brand2"
                                       value="The Ordinary">
                                <label class="form-check-label" for="brand2">The Ordinary</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="brand" id="brand3" value="Skin1004">
                                <label class="form-check-label" for="brand3">Skin1004</label>
                            </div>
                        </div>

                        {{-- Giá --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold">Giá sản phẩm</h6>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <input type="number" class="form-control text-center" placeholder="Từ"
                                       style="width: 80px;">
                                <span>-</span>
                                <input type="number" class="form-control text-center" placeholder="Đến"
                                       style="width: 80px;">
                            </div>
                        </div>

                        {{-- Nút lọc --}}
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-pink d-flex align-items-center px-4 py-2"
                                    style="font-weight: 500;">
                                <i class="bi bi-search me-2"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            {{-- DANH SÁCH SẢN PHẨM --}}
            <section class="col-md-9">
                <div class="row g-4">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="col-md-4">
                            <div class="card product-card h-100 text-center">
                                <img src="{{ asset('images/product' . $i . '.jpg') }}" class="card-img-top p-3"
                                     alt="Product">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">Sản phẩm {{ $i }}</h5>
                                    <p class="price text-pink">$39.00</p>
                                    <button class="btn btn-pink mt-auto">Thêm vào giỏ hàng</button>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- PHÂN TRANG --}}
                <div class="mt-4 d-flex justify-content-center">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item disabled"><a class="page-link">‹</a></li>
                            <li class="page-item active"><a class="page-link">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">›</a></li>
                        </ul>
                    </nav>
                </div>
            </section>
        </div>
    </div>
@endsection
