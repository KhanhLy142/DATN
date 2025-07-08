@extends('admin.layouts.master')

@section('title', 'Quản lý Chat')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Danh sách Chat Sessions</h2>
            <div>
                <a href="{{ route('admin.chats.analytics') }}" class="btn btn-info me-2">
                    <i class="bi bi-bar-chart-fill"></i> Phân tích dữ liệu
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tìm kiếm trong tin nhắn</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Nhập từ khóa..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('admin.chats.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $chats->total() }}</h4>
                                <small>Tổng chat sessions</small>
                            </div>
                            <i class="bi bi-chat-dots-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $chats->where('chat_status', 'active')->count() }}</h4>
                                <small>Đang hoạt động</small>
                            </div>
                            <i class="bi bi-chat-square-dots-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $chats->sum(function($chat) { return $chat->getMessageCount(); }) }}</h4>
                                <small>Tổng tin nhắn</small>
                            </div>
                            <i class="bi bi-envelope-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $chats->filter(function($chat) { return $chat->created_at->isToday(); })->count() }}</h4>
                                <small>Chat hôm nay</small>
                            </div>
                            <i class="bi bi-calendar-day-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Chủ đề (AI phân tích)</th>
                            <th>Trạng thái</th>
                            <th>Số tin nhắn</th>
                            <th>Câu hỏi đầu tiên</th>
                            <th>Thời gian chat</th>
                            <th>Ngày tạo</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($chats as $chat)
                            <tr>
                                <td><strong>#{{ $chat->id }}</strong></td>
                                <td>
                                    <div>
                                        <strong>{{ $chat->customer->name ?? 'Khách #' . $chat->customer_id }}</strong>
                                        @if($chat->customer->email ?? false)
                                            <br><small class="text-muted">{{ $chat->customer->email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($chat->analyzeTopicFromMessages()) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $chat->chat_status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $chat->chat_status === 'active' ? 'Hoạt động' : 'Đã đóng' }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-info">{{ $chat->getMessageCount() }} tổng</span>
                                        <br>
                                        <small class="text-muted">
                                            KH: {{ $chat->getCustomerMessageCount() }} |
                                            Bot: {{ $chat->getChatbotMessageCount() }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $firstCustomerMessage = $chat->messages()->where('sender', 'customer')->oldest()->first();
                                    @endphp
                                    @if($firstCustomerMessage)
                                        <div style="max-width: 250px;" title="{{ $firstCustomerMessage->message }}">
                                            <strong>📝</strong> {{ Str::limit($firstCustomerMessage->message, 80) }}
                                        </div>
                                    @else
                                        <small class="text-muted">Chưa có tin nhắn từ khách hàng</small>
                                    @endif
                                </td>
                                <td>
                                    @php $duration = $chat->getChatDurationInMinutes(); @endphp
                                    @if($duration > 0)
                                        <span class="badge bg-light text-dark">{{ $duration }} phút</span>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        {{ $chat->created_at->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $chat->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.chats.show', $chat->id) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($chat->chat_status === 'active')
                                            <button onclick="closeChat({{ $chat->id }})"
                                                    class="btn btn-outline-warning btn-sm"
                                                    title="Đóng chat">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.chats.destroy', $chat->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Xác nhận xóa chat này và tất cả tin nhắn?')"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-chat-dots fs-1 mb-2"></i>
                                    <br>Chưa có chat nào
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($chats->hasPages())
                <div class="card-footer">
                    {{ $chats->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function closeChat(chatId) {
            if (confirm('Bạn có chắc muốn đóng chat này?')) {
                fetch(`/admin/chats/${chatId}/close`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                });
            }
        }
    </script>
@endsection
