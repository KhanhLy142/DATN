@extends('user.layouts.master')

@section('title', 'Liên hệ')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <!-- Tiêu đề -->
        <div class="mb-4 text-center">
            <h2 class="fw-bold text-pink">Liên hệ với chúng tôi</h2>
            <p class="text-muted">Nếu bạn có bất kỳ câu hỏi nào, hãy để lại lời nhắn – chúng tôi sẽ phản hồi sớm
                nhất!</p>
        </div>

        <div class="row">
            <!-- Form liên hệ (full width) -->
            <div class="col-md-12">
                <div class="card p-4 shadow-sm rounded-4">
                    <form enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Họ và tên</label>
                            <input type="text" class="form-control rounded-pill" id="name" placeholder="Nguyễn Văn A">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control rounded-pill" id="email"
                                   placeholder="email@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">Chủ đề</label>
                            <input type="text" class="form-control rounded-pill" id="subject"
                                   placeholder="Vấn đề bạn quan tâm">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold">Nội dung</label>
                            <textarea class="form-control" id="message" rows="4"
                                      placeholder="Nội dung tin nhắn..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="attachment" class="form-label fw-semibold d-block">Tệp đính kèm <span
                                        class="text-muted small">(tuỳ chọn)</span></label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-pink rounded-pill px-4">Gửi liên hệ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
