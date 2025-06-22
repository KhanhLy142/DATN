<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Hiển thị danh sách tất cả chat sessions
     */
    public function index()
    {
        $chats = Chat::with(['user', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.chats.index', compact('chats'));
    }

    /**
     * Tạo chat session mới cho user
     */
    public function create()
    {
        return view('admin.chats.create');
    }

    /**
     * Lưu chat session mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $chat = Chat::create([
            'user_id' => $request->user_id,
            'chat_status' => 'active',
        ]);

        return redirect()->route('chats.show', $chat->id)
            ->with('success', 'Chat session đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết một chat session
     */
    public function show(string $id)
    {
        $chat = Chat::with(['user', 'messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('admin.chats.show', compact('chat'));
    }

    /**
     * API endpoint để gửi tin nhắn và nhận phản hồi từ AI
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = Chat::findOrFail($request->chat_id);

        // Lưu tin nhắn của user
        $userMessage = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // Gọi API AI để lấy phản hồi
        try {
            $aiResponse = $this->getAIResponse($request->message, $chat);

            // Lưu phản hồi của AI
            $botMessage = ChatMessage::create([
                'chat_id' => $chat->id,
                'sender' => 'chatbot',
                'message' => $aiResponse,
            ]);

            // Cập nhật thời gian chat
            $chat->touch();

            return response()->json([
                'success' => true,
                'user_message' => $userMessage,
                'bot_message' => $botMessage,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể kết nối với AI service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API để lấy lịch sử tin nhắn của một chat
     */
    public function getMessages(string $chatId): JsonResponse
    {
        $messages = ChatMessage::where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Đóng chat session
     */
    public function closeChat(string $id): JsonResponse
    {
        $chat = Chat::findOrFail($id);
        $chat->update(['chat_status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Chat đã được đóng'
        ]);
    }

    /**
     * Xóa chat session và tất cả tin nhắn
     */
    public function destroy(string $id)
    {
        $chat = Chat::findOrFail($id);

        // Xóa tất cả tin nhắn trước
        $chat->messages()->delete();

        // Xóa chat session
        $chat->delete();

        return redirect()->route('chats.index')
            ->with('success', 'Chat đã được xóa thành công!');
    }

    /**
     * Gọi API AI để lấy phản hồi
     */
    private function getAIResponse(string $message, Chat $chat): string
    {
        // Lấy lịch sử hội thoại để cung cấp context
        $context = $chat->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(function($msg) {
                return [
                    'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                    'content' => $msg->message
                ];
            })
            ->toArray();

        // Thêm tin nhắn hiện tại
        $context[] = [
            'role' => 'user',
            'content' => $message
        ];

        // Ví dụ gọi OpenAI API (bạn có thể thay bằng AI service khác)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => $context,
            'max_tokens' => 500,
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi không thể phản hồi lúc này.';
        }

        throw new \Exception('AI API không phản hồi');
    }

    /**
     * Thống kê chat
     */
    public function statistics()
    {
        $stats = [
            'total_chats' => Chat::count(),
            'active_chats' => Chat::where('chat_status', 'active')->count(),
            'closed_chats' => Chat::where('chat_status', 'closed')->count(),
            'total_messages' => ChatMessage::count(),
            'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
            'top_users' => Chat::with('user')
                ->selectRaw('user_id, count(*) as chat_count')
                ->groupBy('user_id')
                ->orderBy('chat_count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.chats.statistics', compact('stats'));
    }
}
