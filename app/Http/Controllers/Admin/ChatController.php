<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{

    public function index(Request $request)
    {
        $query = Chat::with(['customer', 'messages']);

        if ($request->filled('status')) {
            $query->where('chat_status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('messages', function($q) use ($searchTerm) {
                $q->where('message', 'LIKE', "%{$searchTerm}%");
            });
        }

        $chats = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('admin.chats.index', compact('chats'));
    }


    public function show(string $id)
    {
        $chat = Chat::with(['customer', 'messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('admin.chats.show', compact('chat'));
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = Chat::findOrFail($request->chat_id);

        $adminMessage = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender' => 'chatbot',
            'message' => $request->message,
        ]);

        $chat->touch();

        return response()->json([
            'success' => true,
            'message' => $adminMessage,
        ]);
    }

    public function getMessages(string $chatId): JsonResponse
    {
        $messages = ChatMessage::where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function closeChat(string $id): JsonResponse
    {
        $chat = Chat::findOrFail($id);
        $chat->update(['chat_status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Chat đã được đóng'
        ]);
    }

    public function destroy(string $id)
    {
        $chat = Chat::findOrFail($id);

        $chat->messages()->delete();

        $chat->delete();

        return redirect()->route('admin.chats.index')
            ->with('success', 'Chat đã được xóa thành công!');
    }

    public function analytics()
    {
        $totalChats = Chat::count();
        $totalMessages = ChatMessage::count();

        $chatsByTopic = Chat::with('messages')->get()->groupBy(function($chat) {
            return $chat->analyzeTopicFromMessages();
        })->map(function($group) {
            return $group->count();
        });

        $popularQuestions = ChatMessage::where('sender', 'customer')
            ->selectRaw('message, COUNT(*) as count')
            ->groupBy('message')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        $allCustomerMessages = ChatMessage::where('sender', 'customer')
            ->pluck('message')
            ->implode(' ');

        $words = str_word_count(strtolower($allCustomerMessages), 1, 'aàáảãạăắằẳẵặâấầẩẫậeèéẻẽẹêếềểễệiìíỉĩịoòóỏõọôốồổỗộơớờởỡợuùúủũụưứừửữựyỳýỷỹỵđ');
        $commonWords = array_count_values($words);
        arsort($commonWords);
        $topKeywords = array_slice($commonWords, 0, 30, true);

        $slowResponseChats = Chat::with(['customer', 'messages'])
            ->get()
            ->filter(function($chat) {
                return $chat->getChatDurationInMinutes() > 30;
            })
            ->take(10);

        $chatsByDate = Chat::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return view('admin.chats.analytics', compact(
            'totalChats',
            'totalMessages',
            'chatsByTopic',
            'popularQuestions',
            'topKeywords',
            'slowResponseChats',
            'chatsByDate'
        ));
    }

    public function exportTrainingData()
    {
        $trainingData = [];

        $chats = Chat::with(['messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->get();

        foreach ($chats as $chat) {
            $conversation = [];
            foreach ($chat->messages as $message) {
                $conversation[] = [
                    'sender' => $message->sender,
                    'message' => $message->message,
                    'timestamp' => $message->created_at->toISOString()
                ];
            }

            if (count($conversation) > 0) {
                $trainingData[] = [
                    'chat_id' => $chat->id,
                    'topic' => $chat->analyzeTopicFromMessages(),
                    'duration_minutes' => $chat->getChatDurationInMinutes(),
                    'message_count' => count($conversation),
                    'conversation' => $conversation
                ];
            }
        }

        $filename = 'chat_training_data_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($trainingData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
