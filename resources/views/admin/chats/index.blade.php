@extends('admin.layouts.master')

@section('title', 'Quản lý Chat')

@section('content')
    <div class="container py-4">
        <h2 class="fw-bold text-primary mb-4">Danh sách Chat Sessions</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Trạng thái</th>
                    <th>Tin nhắn cuối</th>
                    <th>Ngày tạo</th>
                    <th>Cập nhật</th>
                    <th class="text-center">Hành động</th>
                </tr>
                </thead>
                <tbody>
                @forelse($chats as $chat)
                    <tr>
                        <td>{{ $chat->id }}</td>
                        <td>{{ $chat->user->name ?? 'N/A' }}</td>
                        <td>
                        <span class="badge {{ $chat->chat_status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $chat->chat_status === 'active' ? 'Hoạt động' : 'Đã đóng' }}
                        </span>
                        </td>
                        <td>
                            @if($chat->lastMessage)
                                <small class="text-muted">{{ $chat->lastMessage->sender }}:</small>
                                {{ Str::limit($chat->lastMessage->message, 50) }}
                            @else
                                <span class="text-muted">Chưa có tin nhắn</span>
                            @endif
                        </td>
                        <td>{{ $chat->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $chat->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('chats.show', $chat->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            @if($chat->chat_status === 'active')
                                <button onclick="closeChat({{ $chat->id }})" class="btn btn-sm btn-outline-warning"><i class="fas fa-times"></i></button>
                            @endif
                            <form method="POST" action="{{ route('chats.destroy', $chat->id) }}" class="d-inline" onsubmit="return confirm('Xác nhận xóa?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có chat nào</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $chats->links() }}
            </div>
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
                    if (data.success) location.reload();
                    else alert('Có lỗi xảy ra');
                });
            }
        }
    </script>
@endsection
