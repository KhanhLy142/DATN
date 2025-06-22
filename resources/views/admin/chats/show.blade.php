@extends('admin.layouts.master')

@section('title', 'Chi tiết Chat #' . $chat->id)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Chat với {{ $chat->user->name ?? 'N/A' }}</h3>
            <div>
                @if($chat->chat_status === 'active')
                    <button class="btn btn-warning me-2" onclick="closeChat()"><i class="fas fa-times"></i> Đóng</button>
                @endif
                <a href="{{ route('chats.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Thông tin chat</strong>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $chat->id }}</p>
                <p><strong>Trạng thái:</strong>
                    <span class="badge {{ $chat->chat_status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $chat->chat_status === 'active' ? 'Hoạt động' : 'Đã đóng' }}
                </span>
                </p>
                <p><strong>Ngày tạo:</strong> {{ $chat->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật:</strong> {{ $chat->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="card chat-container mb-4">
            <div class="card-body chat-messages" id="chatMessages">
                @foreach($chat->messages as $message)
                    <div class="mb-3 {{ $message->sender == 'user' ? 'text-end' : '' }}">
                        <div class="d-inline-block px-3 py-2 rounded {{ $message->sender == 'user' ? 'bg-primary text-white' : 'bg-light' }}">
                            {{ $message->message }}
                        </div>
                        <div class="text-muted small mt-1">
                            {{ $message->created_at->format('H:i d/m/Y') }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if($chat->chat_status === 'active')
                <div class="card-footer">
                    <form id="messageForm" class="d-flex">
                        <input id="messageInput" type="text" class="form-control me-2" placeholder="Nhập tin nhắn..." required>
                        <button class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const chatMessages = document.getElementById('chatMessages');
        const chatId = {{ $chat->id }};

        messageForm?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            messageInput.disabled = true;

            const res = await fetch('/admin/chats/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ chat_id: chatId, message })
            });

            const data = await res.json();

            if (data.success) location.reload();
            else alert(data.error || 'Có lỗi xảy ra');

            messageInput.disabled = false;
            messageInput.value = '';
        });

        function closeChat() {
            if (confirm('Bạn có chắc muốn đóng chat này?')) {
                fetch(`/admin/chats/${chatId}/close`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) location.reload();
                    else alert('Có lỗi xảy ra');
                });
            }
        }
    </script>
@endsection
