<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;

        $this->middleware('customer.auth');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('customer')->user();

            if (!$user) {
                Log::warning('Unauthorized access to chatbot');
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để sử dụng chatbot');
            }

            $customer = $this->getOrCreateCustomer($user);

            $chat = Chat::where('customer_id', $customer->id)
                ->where('chat_status', 'active')
                ->first();

            if (!$chat) {
                $chat = Chat::create([
                    'customer_id' => $customer->id,
                    'chat_status' => 'active'
                ]);

                Log::info('Created new chat', [
                    'chat_id' => $chat->id,
                    'customer_id' => $customer->id,
                    'user_id' => $user->id
                ]);
            }

            if (!$chat || !$chat->id) {
                Log::error('Failed to create or retrieve chat', [
                    'user_id' => $user->id,
                    'customer_id' => $customer->id
                ]);

                return redirect()->back()->with('error', 'Không thể khởi tạo chat. Vui lòng thử lại.');
            }

            $messages = $chat->messages()
                ->orderBy('created_at', 'asc')
                ->limit(50)
                ->get();

            Log::info('Chatbot page loaded', [
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'message_count' => $messages->count()
            ]);

            return view('user.ai-chatbot', compact('chat', 'messages', 'user', 'customer'));

        } catch (\Exception $e) {
            Log::error('Chatbot index error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::guard('customer')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }

    private function getOrCreateCustomer($user)
    {
        try {
            $customer = $user->customer;

            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name ?? 'User',
                    'email' => $user->email,
                    'phone' => null,
                    'address' => null,
                ]);

                Log::info('Created new customer', [
                    'customer_id' => $customer->id,
                    'user_id' => $user->id
                ]);
            }

            return $customer;

        } catch (\Exception $e) {
            Log::error('Error creating customer', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $user = Auth::guard('customer')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

            $request->validate([
                'chat_id' => 'required|integer|min:1',
                'message' => 'required|string|max:1000'
            ]);

            $chatId = $request->chat_id;
            $userMessage = trim($request->message);

            $chat = Chat::with('customer')->find($chatId);

            if (!$chat) {
                Log::warning('Chat not found', [
                    'chat_id' => $chatId,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Chat không tồn tại'
                ], 404);
            }

            if ($chat->customer->user_id !== $user->id) {
                Log::warning('Unauthorized chat access', [
                    'chat_id' => $chatId,
                    'user_id' => $user->id,
                    'chat_owner' => $chat->customer->user_id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to chat'
                ], 403);
            }

            DB::beginTransaction();

            $userChatMessage = ChatMessage::create([
                'chat_id' => $chatId,
                'sender' => 'customer',
                'message' => $userMessage
            ]);

            $chatHistory = ChatMessage::where('chat_id', $chatId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->values()
                ->toArray();

            $aiResponse = $this->geminiService->generateResponse($userMessage, $chatHistory);

            if (!$aiResponse) {
                throw new \Exception('AI service returned empty response');
            }

            $botChatMessage = ChatMessage::create([
                'chat_id' => $chatId,
                'sender' => 'chatbot',
                'message' => $aiResponse
            ]);

            Chat::where('id', $chatId)->update(['updated_at' => now()]);

            DB::commit();

            Log::info('Chat message processed successfully', [
                'chat_id' => $chatId,
                'user_id' => $user->id,
                'message_length' => strlen($userMessage),
                'response_length' => strlen($aiResponse)
            ]);

            return response()->json([
                'success' => true,
                'reply' => $aiResponse,
                'user_message' => $userMessage,
                'timestamp' => $botChatMessage->created_at->format('H:i')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            Log::warning('Chat validation error', [
                'errors' => $e->errors(),
                'user_id' => Auth::guard('customer')->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->errors())
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Chat Error', [
                'chat_id' => $request->chat_id ?? 'unknown',
                'user_message' => $request->message ?? 'unknown',
                'error' => $e->getMessage(),
                'user_id' => Auth::guard('customer')->id(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau hoặc liên hệ hotline 0901 234 567.'
            ], 500);
        }
    }

    public function getChatHistory(Request $request)
    {
        try {
            $user = Auth::guard('customer')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

        } catch (\Exception $e) {
            Log::error('Get chat history error', [
                'chat_id' => $request->input('chat_id'),
                'user_id' => Auth::guard('customer')->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Không thể lấy lịch sử chat'
            ], 500);
        }
    }

}
