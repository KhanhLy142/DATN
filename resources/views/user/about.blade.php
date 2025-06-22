@extends('user.layouts.master')

@section('title', 'Giới thiệu')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <section class="py-5 bg-light">
        <div class="container">
            <h1 class="text-center mb-5 fw-bold text-pink">Giới thiệu về DaisyBeauty</h1>

            <div class="row align-items-center mb-5">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="{{ asset('images/about-us.jpg') }}" alt="Giới thiệu DaisyBeauty"
                         class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-md-6">
                    <h3 class="mb-3 fw-semibold">Nét đẹp tinh tế từ thiên nhiên</h3>
                    <p class="text-justify-custom">
                        <strong>Daisy Beauty</strong> là cửa hàng chuyên phân phối mỹ phẩm chính hãng đến từ nhiều
                        thương hiệu nổi tiếng trong và ngoài nước. Tại đây, bạn có thể tìm thấy đa dạng sản phẩm từ
                        skincare, trang điểm, chăm sóc tóc đến dưỡng thể – phù hợp với mọi loại da và nhu cầu làm đẹp.
                    </p>
                    <p class="text-justify-custom">
                        Chúng tôi lựa chọn kỹ lưỡng từng thương hiệu và dòng sản phẩm, ưu tiên các thành phần chiết xuất
                        từ thiên nhiên như trà xanh, cúc la mã, nha đam, hoa hồng và các loại tinh dầu dịu nhẹ. Hầu hết
                        sản phẩm tại Daisy Beauty đều nói không với paraben, cồn khô, hương liệu tổng hợp và không thử
                        nghiệm trên động vật.
                    </p>
                    <p class="text-justify-custom">
                        Dù bạn yêu thích các thương hiệu quốc tế như La Roche-Posay, Paula's Choice, Klairs, The
                        Ordinary, hay các dòng nội địa uy tín như Cocoon, Skinna, chúng tôi đều mang đến lựa chọn đa
                        dạng với mức giá hợp lý và cam kết hàng chính hãng 100%.
                    </p>
                    <p class="text-justify-custom">
                        Với đội ngũ tư vấn tận tâm, am hiểu làn da người Việt, Daisy Beauty luôn sẵn sàng đồng hành và
                        giúp bạn chọn được sản phẩm phù hợp nhất – dù bạn là người mới bắt đầu hay là “chuyên gia
                        skincare”. Chúng tôi tin rằng: mỗi làn da đều xứng đáng được nâng niu một cách an toàn, hiệu quả
                        và đầy yêu thương.
                    </p>
                </div>
            </div>

            <hr class="my-5">

            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="mb-3">
                        <i class="bi bi-eye-fill fs-1 text-pink"></i>
                    </div>
                    <h4 class="fw-semibold">Tầm nhìn</h4>
                    <p>Trở thành thương hiệu mỹ phẩm thiên nhiên hàng đầu Việt Nam trong 5 năm tới.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="mb-3">
                        <i class="bi bi-bullseye fs-1 text-pink"></i>
                    </div>
                    <h4 class="fw-semibold">Sứ mệnh</h4>
                    <p>Mang đến sản phẩm lành tính, an toàn, phù hợp với mọi làn da, đặc biệt là da nhạy cảm.</p>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <i class="bi bi-heart-fill fs-1 text-pink"></i>
                    </div>
                    <h4 class="fw-semibold">Giá trị cốt lõi</h4>
                    <p>Chân thành – Chất lượng – Tận tâm – Bền vững.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
