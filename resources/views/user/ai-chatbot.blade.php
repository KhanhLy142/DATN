@extends('user.layouts.master')

@section('title', 'Trò chuyện với Chatbot AI')

@section('banner')
    <section class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Trò chuyện với AI</li>
                </ol>
            </nav>
        </div>
    </section>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="text-center mb-4">
                    <h4 class="fs-2 fw-bold" style="color: #ff6b9d;">🤖 Trò chuyện với AI DaisyBeauty</h4>
                    <p class="text-muted">Chuyên gia tư vấn mỹ phẩm 24/7 - Thông minh, nhanh chóng, chính xác</p>
                </div>

                <div class="card shadow-lg border-0" style="height: 600px;">
                    <div class="card-header text-white p-3" style="background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">💬 AI Beauty Consultant</h6>
                                <small class="opacity-75">Luôn sẵn sàng tư vấn cho bạn</small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-light" onclick="newChat()">
                                    🔄 Chat mới
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0 d-flex flex-column" style="height: 100%;">
                        <div id="chat-messages" class="flex-grow-1 p-3" style="overflow-y: auto; max-height: 450px;">
                            @if($messages->count() > 0)
                                @foreach($messages as $message)
                                    <div class="message-item mb-3 {{ $message->sender === 'customer' ? 'text-end' : 'text-start' }}">
                                        <div class="message-bubble d-inline-block p-3 rounded-3 {{ $message->sender === 'customer' ? 'user-message' : 'bot-message' }}"
                                             style="max-width: 75%; word-wrap: break-word;">
                                            @if($message->sender === 'customer')
                                                <strong>👤 Bạn:</strong>
                                            @else
                                                <strong>🤖 AI:</strong>
                                            @endif
                                            <div class="mt-1">{!! nl2br(e($message->message)) !!}</div>
                                            <small class="opacity-75 d-block mt-1">{{ $message->created_at->format('H:i') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted p-4">
                                    <h5>👋 Chào bạn!</h5>
                                    <p>Tôi là AI tư vấn mỹ phẩm của DaisyBeauty. Hãy hỏi tôi bất cứ điều gì về:</p>
                                    <div class="row g-2 mt-2">
                                        <div class="col-6">
                                            <button class="btn btn-outline-daisy btn-sm w-100" onclick="quickMessage('Tôi muốn tư vấn kem chống nắng')">
                                                ☀️ Kem chống nắng
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-outline-daisy btn-sm w-100" onclick="quickMessage('Serum nào tốt cho da dầu?')">
                                                💧 Serum cho da dầu
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-outline-daisy btn-sm w-100" onclick="quickMessage('Quy trình skincare cho người mới')">
                                                ✨ Quy trình skincare
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-outline-daisy btn-sm w-100" onclick="quickMessage('Sản phẩm trị mụn hiệu quả')">
                                                🎯 Trị mụn
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div id="typing-indicator" class="p-3 border-top bg-light" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" style="color: #ff6b9d;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <small class="text-muted">AI đang suy nghĩ...</small>
                            </div>
                        </div>

                        <div class="card-footer border-0 bg-white">
                            <form id="chat-form" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="chat_id" value="{{ $chat->id }}">
                                <input type="text"
                                       class="form-control border-2"
                                       id="message-input"
                                       name="message"
                                       placeholder="💬 Hỏi tôi về mỹ phẩm..."
                                       required
                                       autocomplete="off"
                                       maxlength="1000"
                                       style="border-color: #ec8ca3;">
                                <button class="btn btn-daisy px-4" type="submit" id="send-button">
                                    <i class="bi bi-send"></i>
                                    <span class="d-none d-sm-inline ms-1">Gửi</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        💡 Mẹo: Hãy cho tôi biết loại da để được tư vấn chính xác nhất!
                        <br>📞 Hotline: <strong>0901 234 567</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-daisy {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
            color: white;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-daisy:hover {
            background-color: #d5738e;
            border-color: #d5738e;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 140, 163, 0.3);
        }

        .btn-outline-daisy {
            border-color: #ec8ca3;
            color: #ec8ca3;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .btn-outline-daisy:hover {
            background-color: #ec8ca3;
            border-color: #ec8ca3;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 140, 163, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #ec8ca3 0%, #f8b6c1 100%) !important;
        }

        .user-message {
            background: linear-gradient(135deg, #ec8ca3 0%, #f8b6c1 100%);
            color: white;
        }

        .bot-message {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #333;
        }

        .message-bubble {
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 2px 8px rgba(236, 140, 163, 0.15);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: #ec8ca3;
            border-radius: 3px;
        }

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #d5738e;
        }

        .card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(236, 140, 163, 0.15);
        }

        .form-control:focus {
            border-color: #ec8ca3;
            box-shadow: 0 0 0 0.2rem rgba(236, 140, 163, 0.25);
        }

        #message-input {
            border-color: #ec8ca3 !important;
        }

        .spinner-border {
            color: #ec8ca3 !important;
        }

        #typing-indicator {
            background-color: #fce4ec;
            border-top: 1px solid #f8b6c1;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .fs-2.fw-bold {
            color: #ec8ca3 !important;
        }

        .message-item {
            animation: messageSlide 0.4s ease-out;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(15px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 768px) {
            .card {
                border-radius: 15px;
                margin: 0 0.5rem;
            }

            .message-bubble {
                max-width: 85% !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const chatId = @json(isset($chat) && $chat ? $chat->id : null);
        const csrfToken = @json(csrf_token());

        console.log('🔥 JavaScript loaded successfully!');
        console.log('Chat ID:', chatId, 'Type:', typeof chatId);
        console.log('CSRF Token:', csrfToken);

        function isValidChatId(id) {
            return id &&
                id !== null &&
                id !== 'null' &&
                typeof id !== 'undefined' &&
                !isNaN(id) &&
                id > 0;
        }

        if (!isValidChatId(chatId)) {
            console.error('❌ Chat ID is invalid:', chatId);
            createNewChatSession();
        } else {
            console.log('✅ Chat ID is valid:', chatId);
        }

        function createNewChatSession() {
            console.log('🔄 Attempting to create new chat session...');

            fetch('/chatbot/new', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('✅ New chat created, reloading...');
                        window.location.reload();
                    } else {
                        console.error('❌ Failed to create new chat:', data);
                        alert('Không thể tạo chat session. Vui lòng refresh trang.');
                    }
                })
                .catch(error => {
                    console.error('❌ Error creating chat:', error);
                    alert('Lỗi kết nối. Vui lòng kiểm tra mạng và refresh trang.');
                });
        }

        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!isValidChatId(chatId)) {
                    console.error('❌ Cannot send message: Invalid chat ID');
                    alert('Lỗi chat session. Đang tạo lại...');
                    createNewChatSession();
                    return;
                }

                sendMessage();
            });
        }

        const messageInput = document.getElementById('message-input');
        if (messageInput) {
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    if (!isValidChatId(chatId)) {
                        console.error('❌ Cannot send message: Invalid chat ID');
                        createNewChatSession();
                        return;
                    }

                    sendMessage();
                }
            });
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input?.value?.trim();

            if (!message) {
                console.log('⚠️ Empty message');
                return;
            }

            if (!isValidChatId(chatId)) {
                console.error('❌ Invalid chatId in sendMessage:', chatId);
                alert('Lỗi chat session. Vui lòng refresh trang.');
                return;
            }

            console.log('📤 Sending message:', message, 'to chat:', chatId);

            input.disabled = true;
            const sendButton = document.getElementById('send-button');
            if (sendButton) sendButton.disabled = true;

            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.style.display = 'block';
            }

            addMessageToChat('customer', message, new Date());

            input.value = '';

            fetch('/chatbot/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    chat_id: parseInt(chatId),
                    message: message
                })
            })
                .then(response => {
                    console.log('📡 Response status:', response.status);

                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('📥 Response data:', data);

                    if (typingIndicator) {
                        typingIndicator.style.display = 'none';
                    }

                    if (data.success) {
                        addMessageToChat('chatbot', data.reply, new Date());
                    } else {
                        addMessageToChat('chatbot', data.error || 'Có lỗi xảy ra, vui lòng thử lại.', new Date());
                    }
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);

                    if (typingIndicator) {
                        typingIndicator.style.display = 'none';
                    }

                    addMessageToChat('chatbot', 'Không thể kết nối. Vui lòng kiểm tra mạng và thử lại.', new Date());
                })
                .finally(() => {
                    input.disabled = false;
                    if (sendButton) sendButton.disabled = false;
                    input.focus();
                });
        }

        function addMessageToChat(sender, message, timestamp) {
            const chatMessages = document.getElementById('chat-messages');
            if (!chatMessages) return;

            const isUser = sender === 'customer';
            const timeString = timestamp.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});

            const messageHtml = '<div class="message-item mb-3 ' + (isUser ? 'text-end' : 'text-start') + '">' +
                '<div class="message-bubble d-inline-block p-3 rounded-3 ' + (isUser ? 'user-message' : 'bot-message') + '" style="max-width: 75%; word-wrap: break-word;">' +
                '<strong>' + (isUser ? '👤 Bạn:' : '🤖 AI:') + '</strong>' +
                '<div class="mt-1">' + message.replace(/\n/g, '<br>') + '</div>' +
                '<small class="opacity-75 d-block mt-1">' + timeString + '</small>' +
                '</div>' +
                '</div>';

            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function quickMessage(message) {
            const input = document.getElementById('message-input');
            if (input && isValidChatId(chatId)) {
                input.value = message;
                sendMessage();
            } else {
                console.error('❌ Cannot send quick message: missing input or invalid chatId');
            }
        }

        function newChat() {
            if (confirm('Bạn có muốn bắt đầu cuộc trò chuyện mới không?')) {
                createNewChatSession();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('message-input');
            if (input && isValidChatId(chatId)) {
                input.focus();
                console.log('✅ Chat initialized successfully with ID:', chatId);
            } else {
                console.log('⚠️ Chat not ready, chatId:', chatId);
            }
        });
    </script>
@endpush
