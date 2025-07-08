<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class GeminiService
{
    private $apiKey;
    private $apiUrl;
    private $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        Log::info('GeminiService initialized', [
            'api_key_exists' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey ?? ''),
            'model' => $this->model,
            'url' => $this->apiUrl
        ]);
    }

    private function createAdvancedCosmeticsPrompt($userMessage, $chatHistory = [])
    {
        $categories = $this->getAvailableCategories();
        $brands = $this->getAvailableBrands();
        $products = $this->getRelevantProducts($userMessage);

        $systemPrompt = "
Bạn là chuyên gia tư vấn mỹ phẩm DaisyBeauty - thân thiện, am hiểu và chuyên nghiệp.
Hãy trả lời bằng tiếng Việt một cách tự nhiên, ngắn gọn nhưng hữu ích.

THÔNG TIN SHOP:
- Tên: DaisyBeauty
- Chuyên: Mỹ phẩm, trang điểm, chăm sóc tóc chính hãng
- Địa chỉ: 123 Hoa Hồng, Q.1, TP.HCM
- Hotline: 0901 234 567

DANH MỤC SẢN PHẨM CÓ SẴN:
{$categories}

THƯƠNG HIỆU CÓ SẴN:
{$brands}

SẢN PHẨM LIÊN QUAN (dựa trên câu hỏi):
{$products}

KIẾN THỨC CHUYÊN MÔN:
- Các loại da: khô, dầu, hỗn hợp, nhạy cảm
- Thành phần hot: vitamin C, retinol, hyaluronic acid, niacinamide, AHA/BHA
- Quy trình skincare: tẩy trang → sữa rửa mặt → toner → serum → kem dưỡng → kem chống nắng

QUY TẮC TƯ VẤN:
1. LUÔN ưu tiên gợi ý sản phẩm, thương hiệu, danh mục CÓ SẴN
2. Đưa ra tên sản phẩm chính xác, thương hiệu và giá cụ thể
3. Giải thích tại sao phù hợp với nhu cầu khách hàng
4. Hỏi thêm về loại da/nhu cầu cụ thể nếu cần
5. Đưa ra hướng dẫn sử dụng ngắn gọn
6. Kết thúc bằng câu hỏi để tiếp tục tư vấn
7. Trả lời tối đa 5-6 câu, thân thiện như bạn bè
8. Luôn đề cập lợi ích cụ thể của sản phẩm

LỊCH SỬ CHAT:
" . $this->formatChatHistory($chatHistory) . "

KHÁCH HÀNG VỪA HỎI: {$userMessage}

Hãy tư vấn dựa trên thông tin có sẵn và đưa ra gợi ý cụ thể:
        ";

        return $systemPrompt;
    }

    private function getAvailableCategories()
    {
        try {
            $categories = Category::where('status', 1)
                ->orderBy('name')
                ->get(['name', 'description']);

            if ($categories->isEmpty()) {
                return "Đang cập nhật danh mục sản phẩm.";
            }

            $categoryList = "";
            foreach ($categories as $category) {
                $categoryList .= "- {$category->name}";
                if ($category->description && $category->description !== 'Danh mục gốc') {
                    $categoryList .= ": {$category->description}";
                }
                $categoryList .= "\n";
            }

            return $categoryList;

        } catch (\Exception $e) {
            Log::error('Error getting categories', ['error' => $e->getMessage()]);
            return "Chăm sóc da, trang điểm, chăm sóc tóc, nước hoa, phụ kiện làm đẹp...";
        }
    }

    private function getAvailableBrands()
    {
        try {
            $brands = Brand::where('status', 'active')
                ->orderBy('name')
                ->get(['name', 'description', 'country']);

            if ($brands->isEmpty()) {
                return "Đang cập nhật thương hiệu.";
            }

            $brandList = "";
            foreach ($brands as $brand) {
                $brandList .= "- {$brand->name}";
                if ($brand->country) {
                    $brandList .= " ({$brand->country})";
                }
                if ($brand->description && strlen($brand->description) > 10) {
                    $brandList .= ": " . substr($brand->description, 0, 80) . "...";
                }
                $brandList .= "\n";
            }

            return $brandList;

        } catch (\Exception $e) {
            Log::error('Error getting brands', ['error' => $e->getMessage()]);
            return "La Roche-Posay, Innisfree, CeraVe, Clinique, MAC Cosmetics, Neutrogena, Bioré...";
        }
    }

    private function getRelevantProducts($userMessage)
    {
        try {
            $message = strtolower($userMessage);
            $query = Product::with(['category', 'brand'])
                ->where('status', 1)
                ->where('stock', '>', 0);

            if (str_contains($message, 'kem chống nắng') || str_contains($message, 'sunscreen') || str_contains($message, 'spf')) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%kem chống nắng%')
                        ->orWhere('name', 'like', '%sunscreen%')
                        ->orWhere('name', 'like', '%spf%')
                        ->orWhereHas('category', function($cat) {
                            $cat->where('name', 'like', '%kem chống nắng%');
                        });
                });
            } elseif (str_contains($message, 'serum') || str_contains($message, 'tinh chất')) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%serum%')
                        ->orWhere('name', 'like', '%tinh chất%')
                        ->orWhereHas('category', function($cat) {
                            $cat->where('name', 'like', '%serum%');
                        });
                });
            } elseif (str_contains($message, 'toner') || str_contains($message, 'nước hoa hồng')) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%toner%')
                        ->orWhere('name', 'like', '%nước hoa hồng%')
                        ->orWhereHas('category', function($cat) {
                            $cat->where('name', 'like', '%toner%');
                        });
                });
            } elseif (str_contains($message, 'son môi') || str_contains($message, 'lipstick') || str_contains($message, 'son')) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%son%')
                        ->orWhere('name', 'like', '%lipstick%')
                        ->orWhereHas('category', function($cat) {
                            $cat->where('name', 'like', '%son%');
                        });
                });
            } elseif (str_contains($message, 'trang điểm') || str_contains($message, 'makeup') || str_contains($message, 'phấn')) {
                $query->whereHas('category', function($cat) {
                    $cat->where('name', 'like', '%trang điểm%')
                        ->orWhere('name', 'like', '%phấn%')
                        ->orWhere('name', 'like', '%mascara%')
                        ->orWhere('name', 'like', '%kem nền%');
                });
            } elseif (str_contains($message, 'dầu gội') || str_contains($message, 'shampoo') || str_contains($message, 'tóc')) {
                $query->whereHas('category', function($cat) {
                    $cat->where('name', 'like', '%tóc%')
                        ->orWhere('name', 'like', '%dầu gội%');
                });
            } elseif (str_contains($message, 'nước hoa') || str_contains($message, 'perfume')) {
                $query->whereHas('category', function($cat) {
                    $cat->where('name', 'like', '%nước hoa%');
                });
            } elseif (str_contains($message, 'mụn') || str_contains($message, 'acne')) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%acne%')
                        ->orWhere('name', 'like', '%mụn%')
                        ->orWhere('description', 'like', '%trị mụn%')
                        ->orWhere('description', 'like', '%salicylic%')
                        ->orWhere('description', 'like', '%bha%');
                });
            } else {
                $brandNames = ['innisfree', 'la roche', 'cerave', 'clinique', 'mac', 'neutrogena', 'biore'];
                foreach ($brandNames as $brandName) {
                    if (str_contains($message, $brandName)) {
                        $query->whereHas('brand', function($brand) use ($brandName) {
                            $brand->where('name', 'like', "%{$brandName}%");
                        });
                        break;
                    }
                }
            }

            $products = $query->orderBy('created_at', 'desc')->limit(8)->get();

            if ($products->isEmpty()) {
                $products = Product::with(['category', 'brand'])
                    ->where('status', 1)
                    ->where('stock', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }

            Log::info('Products fetched for prompt', [
                'count' => $products->count(),
                'user_message' => $userMessage
            ]);

            return $this->formatProducts($products);

        } catch (\Exception $e) {
            Log::error('Error getting relevant products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return "Nhiều sản phẩm chất lượng từ các thương hiệu uy tín. Vui lòng cho biết cụ thể bạn cần sản phẩm gì?";
        }
    }

    private function formatProducts($products)
    {
        if ($products->isEmpty()) {
            return "Không có sản phẩm liên quan cụ thể trong kho.";
        }

        $productList = "";
        foreach ($products as $product) {
            $productList .= "- {$product->name}";

            if ($product->brand) {
                $productList .= " ({$product->brand->name})";
            }

            if ($product->category) {
                $productList .= " - Danh mục: {$product->category->name}";
            }

            $productList .= " - Giá: " . number_format($product->base_price) . "đ";

            if ($product->sku) {
                $productList .= " (Mã: {$product->sku})";
            }

            $productList .= "\n";
        }

        return $productList;
    }

    private function formatChatHistory($history)
    {
        if (empty($history)) {
            return "Đây là lần đầu khách hàng chat.";
        }

        $formatted = "";
        $historyArray = is_array($history) ? $history : $history->toArray();
        $recentMessages = array_slice($historyArray, -6);

        foreach ($recentMessages as $message) {
            $sender = ($message['sender'] ?? $message->sender) === 'customer' ? 'Khách' : 'Bot';
            $messageText = $message['message'] ?? $message->message;
            $formatted .= "{$sender}: {$messageText}\n";
        }

        return $formatted;
    }

    public function generateResponse($userMessage, $chatHistory = [])
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('Gemini API key is missing');
                return $this->getFallbackResponse();
            }

            $prompt = $this->createAdvancedCosmeticsPrompt($userMessage, $chatHistory);
            $url = $this->apiUrl . '?key=' . $this->apiKey;

            Log::info('Sending request to Gemini', [
                'url' => $url,
                'message_length' => strlen($userMessage),
                'prompt_length' => strlen($prompt)
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(config('services.gemini.timeout', 30))
                ->retry(2, 1000)
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 600,
                        'topP' => 0.8,
                        'topK' => 40
                    ]
                ]);

            Log::info('Gemini API Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_preview' => substr($response->body(), 0, 200)
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body()
                ]);
                return $this->getFallbackResponse();
            }

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::warning('Invalid Gemini response format', ['response' => $data]);
                return $this->getFallbackResponse();
            }

            return trim($data['candidates'][0]['content']['parts'][0]['text']);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini Connection Exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return "Không thể kết nối. Vui lòng kiểm tra mạng và thử lại.";

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Gemini Request Exception', [
                'message' => $e->getMessage(),
                'response' => $e->response ? $e->response->body() : null
            ]);
            return $this->getFallbackResponse();

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackResponse();
        }
    }

    private function getFallbackResponse()
    {
        $fallbacks = [
            "Xin lỗi, tôi đang gặp chút vấn đề kỹ thuật. Bạn có thể liên hệ hotline 0901 234 567 để được tư vấn trực tiếp không?",
            "Hệ thống AI tạm thời bận, nhưng tôi vẫn có thể hỗ trợ bạn! Bạn đang quan tâm sản phẩm gì?",
            "Để được tư vấn chi tiết nhất về các thương hiệu La Roche-Posay, Innisfree, CeraVe... bạn hãy gọi 0901 234 567 nhé!"
        ];

        return $fallbacks[array_rand($fallbacks)];
    }

    public function testConnection()
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('Cannot test connection: API key is missing');
                return false;
            }

            $url = $this->apiUrl . '?key=' . $this->apiKey;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(10)
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Hello, test connection']
                            ]
                        ]
                    ]
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Gemini Test Connection Error', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function debugConfig()
    {
        return [
            'api_key_exists' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey ?? ''),
            'api_key_preview' => substr($this->apiKey ?? '', 0, 10) . '...',
            'model' => $this->model,
            'api_url' => $this->apiUrl,
            'timeout' => config('services.gemini.timeout', 30)
        ];
    }
}
