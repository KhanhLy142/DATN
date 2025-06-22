@extends('user.layouts.master')

@section('title', 'Giải pháp Chatbot AI')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chatbot AI</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container py-5">
        <!-- Banner giới thiệu -->
        <div class="text-center mb-5">
            <h1 class="fw-bold text-pink">Trợ lý Chatbot AI Thông Minh</h1>
            <p class="lead text-muted">Tự động hóa phản hồi và phân tích thông tin từ khách hàng bằng trí tuệ nhân
                tạo.</p>
            <img src="{{ asset('images/ai-chatbot-banner.png') }}" alt="Chatbot AI" class="img-fluid my-4"
                 style="max-height: 300px;">
            <a href="#chat-section" class="btn btn-pink btn-lg rounded-pill px-4">Bắt đầu trò chuyện</a>
        </div>

        <!-- Tính năng nổi bật -->
        <div class="row text-center mb-5">
            <h2 class="text-pink fw-bold mb-4">Tính năng nổi bật</h2>
            <div class="col-md-4 mb-4">
                <div class="p-3 border rounded shadow-sm">
                    <h5 class="fw-semibold text-dark">Phân tích thông tin đầu vào</h5>
                    <p class="small text-muted">Chatbot sử dụng AI để hiểu ý định và nhu cầu thực sự của người dùng.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-3 border rounded shadow-sm">
                    <h5 class="fw-semibold text-dark">Giao tiếp linh hoạt</h5>
                    <p class="small text-muted">Trả lời hội thoại theo ngữ cảnh, mang lại trải nghiệm tự nhiên như con
                        người.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-3 border rounded shadow-sm">
                    <h5 class="fw-semibold text-dark">Kết hợp con người + AI</h5>
                    <p class="small text-muted">Cho phép người thật can thiệp nếu chatbot chưa đủ chính xác.</p>
                </div>
            </div>
        </div>

        <!-- Khung trò chuyện -->
        <div class="my-5" id="chat-section">
            <h4 class="text-center fw-bold text-pink mb-4">Trò chuyện với Chatbot AI</h4>
            <div class="card shadow-sm rounded-4">
                <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="chat-box">
                    <!-- Tin nhắn mở đầu -->
                    <div class="mb-3">
                        <div class="fw-semibold">🧠 Chatbot AI:</div>
                        <div class="bg-light rounded p-2 mt-1">Xin chào! Bạn cần hỗ trợ gì hôm nay?</div>
                    </div>
                </div>

                <div class="p-3 border-top d-flex">
                    <input type="text" id="user-input" class="form-control rounded-pill me-2"
                           placeholder="Nhập câu hỏi...">
                    <button class="btn btn-pink rounded-pill" onclick="sendMessage()">Gửi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function sendMessage() {
            const input = document.getElementById("user-input");
            const message = input.value.trim();
            if (!message) return;

            const chatBox = document.getElementById("chat-box");

            // Hiển thị tin nhắn người dùng
            const userMsg = document.createElement("div");
            userMsg.innerHTML = `
            <div class="fw-semibold">🧍 Bạn:</div>
            <div class="bg-white border rounded p-2 mt-1">${message}</div>
        `;
            userMsg.classList.add("mb-3");
            chatBox.appendChild(userMsg);

            // Reset input
            input.value = "";

            // Tự động cuộn xuống
            chatBox.scrollTop = chatBox.scrollHeight;

            // Giả lập phản hồi AI
            setTimeout(() => {
                const aiMsg = document.createElement("div");
                aiMsg.innerHTML = `
                <div class="fw-semibold">🧠 Chatbot AI:</div>
                <div class="bg-light rounded p-2 mt-1">Cảm ơn bạn! Hệ thống đang phân tích và sẽ phản hồi sớm nhất có thể.</div>
            `;
                aiMsg.classList.add("mb-3");
                chatBox.appendChild(aiMsg);
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 1000);
        }
    </script>
@endpush
