<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VNPayService
{
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;
    private $ipnUrl;

    public function __construct()
    {
        $this->tmnCode = config('services.vnpay.tmn_code');
        $this->hashSecret = config('services.vnpay.hash_secret');
        $this->url = config('services.vnpay.url');
        $this->returnUrl = config('services.vnpay.return_url');
        $this->ipnUrl = config('services.vnpay.ipn_url');
    }

    public function createPaymentUrl($order, $ipAddress = null)
    {
        try {
            if (!$this->isConfigured()) {
                throw new \Exception('VNPay chưa được cấu hình đúng');
            }

            $ipAddress = $ipAddress ?: $this->getClientIP();

            $vnp_Params = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $this->tmnCode,
                "vnp_Amount" => intval($order->total_amount) * 100,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $ipAddress,
                "vnp_Locale" => "vn",
                "vnp_OrderInfo" => "Thanh toan don hang " . $order->tracking_number,
                "vnp_OrderType" => "other",
                "vnp_ReturnUrl" => $this->returnUrl,
                "vnp_TxnRef" => $order->tracking_number,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes'))
            );

            ksort($vnp_Params);

            $methods = [
                'method1' => $this->createHashMethod1($vnp_Params),
                'method2' => $this->createHashMethod2($vnp_Params),
                'method3' => $this->createHashMethod3($vnp_Params)
            ];

            Log::info('🧪 Testing 3 Hash Methods:', [
                'order_id' => $order->id,
                'methods' => $methods
            ]);

            $hashdata = $methods['method3']['hashdata'];
            $vnp_SecureHash = $methods['method3']['hash'];

            $query_parts = [];
            foreach ($vnp_Params as $key => $value) {
                $query_parts[] = urlencode($key) . '=' . urlencode($value);
            }
            $query_parts[] = 'vnp_SecureHash=' . $vnp_SecureHash;

            $query_string = implode('&', $query_parts);
            $vnpUrl = $this->url . "?" . $query_string;

            Log::info('✅ VNPay URL Created (Method 3)', [
                'order_id' => $order->id,
                'tracking_number' => $order->tracking_number,
                'amount' => $order->total_amount,
                'hashdata' => $hashdata,
                'secure_hash' => substr($vnp_SecureHash, 0, 16) . '...',
                'url_length' => strlen($vnpUrl)
            ]);

            return [
                'success' => true,
                'payment_url' => $vnpUrl,
                'data' => $vnp_Params,
                'debug' => [
                    'methods' => $methods,
                    'selected_method' => 'method3',
                    'hashdata' => $hashdata,
                    'secure_hash' => $vnp_SecureHash
                ]
            ];

        } catch (\Exception $e) {
            Log::error('❌ VNPay Create URL Error:', [
                'message' => $e->getMessage(),
                'order_id' => $order->id ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi tạo URL: ' . $e->getMessage()
            ];
        }
    }

    private function createHashMethod1($params)
    {
        $hashdata = "";
        $first = true;
        foreach ($params as $key => $value) {
            if (!$first) {
                $hashdata .= '&';
            }
            $hashdata .= $key . '=' . $value;
            $first = false;
        }

        return [
            'hashdata' => $hashdata,
            'hash' => hash_hmac('sha512', $hashdata, $this->hashSecret)
        ];
    }

    private function createHashMethod2($params)
    {
        $hashdata = "";
        $i = 0;
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        return [
            'hashdata' => $hashdata,
            'hash' => hash_hmac('sha512', $hashdata, $this->hashSecret)
        ];
    }

    private function createHashMethod3($params)
    {
        $hashdata = http_build_query($params, '', '&');

        return [
            'hashdata' => $hashdata,
            'hash' => hash_hmac('sha512', $hashdata, $this->hashSecret)
        ];
    }

    public function validateSignature($vnpayData)
    {
        try {
            $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';

            $inputData = $vnpayData;
            unset($inputData['vnp_SecureHash']);
            unset($inputData['vnp_SecureHashType']);

            ksort($inputData);

            $method1 = $this->createHashMethod1($inputData);
            $method2 = $this->createHashMethod2($inputData);
            $method3 = $this->createHashMethod3($inputData);

            $isValid1 = $method1['hash'] === $vnp_SecureHash;
            $isValid2 = $method2['hash'] === $vnp_SecureHash;
            $isValid3 = $method3['hash'] === $vnp_SecureHash;

            Log::info('🔍 VNPay Signature Validation (All Methods):', [
                'received_hash' => substr($vnp_SecureHash, 0, 16) . '...',
                'method1' => [
                    'hash' => substr($method1['hash'], 0, 16) . '...',
                    'valid' => $isValid1,
                    'hashdata' => $method1['hashdata']
                ],
                'method2' => [
                    'hash' => substr($method2['hash'], 0, 16) . '...',
                    'valid' => $isValid2,
                    'hashdata' => $method2['hashdata']
                ],
                'method3' => [
                    'hash' => substr($method3['hash'], 0, 16) . '...',
                    'valid' => $isValid3,
                    'hashdata' => $method3['hashdata']
                ]
            ]);

            return $isValid1 || $isValid2 || $isValid3;

        } catch (\Exception $e) {
            Log::error('❌ VNPay Signature Validation Error:', [
                'message' => $e->getMessage(),
                'data' => $vnpayData
            ]);
            return false;
        }
    }

    public function processReturnData($returnData)
    {
        try {
            Log::info('🔄 VNPay Return Data Processing:', $returnData);

            if (!$this->validateSignature($returnData)) {
                return [
                    'success' => false,
                    'message' => 'Chữ ký không hợp lệ',
                    'code' => 'INVALID_SIGNATURE'
                ];
            }

            $responseCode = $returnData['vnp_ResponseCode'] ?? '';
            $transactionStatus = $returnData['vnp_TransactionStatus'] ?? '';
            $txnRef = $returnData['vnp_TxnRef'] ?? '';
            $amount = isset($returnData['vnp_Amount']) ? ($returnData['vnp_Amount'] / 100) : 0;

            $isSuccess = $responseCode === '00' && $transactionStatus === '00';

            return [
                'success' => $isSuccess,
                'message' => $this->getResponseMessage($responseCode),
                'data' => $returnData,
                'order_tracking' => $txnRef,
                'transaction_no' => $returnData['vnp_TransactionNo'] ?? '',
                'amount' => $amount,
                'response_code' => $responseCode
            ];

        } catch (\Exception $e) {
            Log::error('❌ VNPay Process Return Error:', [
                'message' => $e->getMessage(),
                'data' => $returnData
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi xử lý: ' . $e->getMessage(),
                'code' => 'PROCESSING_ERROR'
            ];
        }
    }

    public function processIPN($ipnData)
    {
        try {
            Log::info('📨 VNPay IPN Processing:', $ipnData);

            if (!$this->validateSignature($ipnData)) {
                return [
                    'success' => false,
                    'message' => 'Invalid signature',
                    'response_code' => '97'
                ];
            }

            $responseCode = $ipnData['vnp_ResponseCode'] ?? '';
            $transactionStatus = $ipnData['vnp_TransactionStatus'] ?? '';
            $txnRef = $ipnData['vnp_TxnRef'] ?? '';
            $amount = isset($ipnData['vnp_Amount']) ? ($ipnData['vnp_Amount'] / 100) : 0;

            $isSuccess = $responseCode === '00' && $transactionStatus === '00';

            return [
                'success' => $isSuccess,
                'message' => $isSuccess ? 'Success' : 'Failed',
                'data' => $ipnData,
                'order_tracking' => $txnRef,
                'transaction_no' => $ipnData['vnp_TransactionNo'] ?? '',
                'amount' => $amount,
                'response_code' => $responseCode,
                'vnpay_response_code' => $isSuccess ? '00' : '01'
            ];

        } catch (\Exception $e) {
            Log::error('❌ VNPay IPN Error:', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'response_code' => '99'
            ];
        }
    }

    private function getClientIP()
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = trim($_SERVER[$key]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return '123.16.64.1';
    }

    private function getResponseMessage($code)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ.',
            '09' => 'Thẻ/Tài khoản chưa đăng ký InternetBanking.',
            '10' => 'Xác thực thông tin sai quá 3 lần.',
            '11' => 'Đã hết hạn chờ thanh toán.',
            '12' => 'Thẻ/Tài khoản bị khóa.',
            '13' => 'Nhập sai mật khẩu xác thực (OTP).',
            '24' => 'Khách hàng hủy giao dịch.',
            '51' => 'Tài khoản không đủ số dư.',
            '65' => 'Vượt quá hạn mức giao dịch trong ngày.',
            '70' => 'Sai chữ ký (Invalid signature).',
            '75' => 'Ngân hàng đang bảo trì.',
            '79' => 'Nhập sai mật khẩu quá số lần quy định.',
            '99' => 'Lỗi khác.'
        ];

        return $messages[$code] ?? 'Giao dịch thất bại - Mã lỗi: ' . $code;
    }

    public function isConfigured()
    {
        $checks = [
            'tmn_code' => !empty($this->tmnCode) && strlen($this->tmnCode) === 8,
            'hash_secret' => !empty($this->hashSecret) && strlen($this->hashSecret) === 32,
            'url' => !empty($this->url) && filter_var($this->url, FILTER_VALIDATE_URL),
            'return_url' => !empty($this->returnUrl)
        ];

        return array_product($checks);
    }

    public function getConfigInfo()
    {
        return [
            'tmn_code' => $this->tmnCode,
            'tmn_code_length' => strlen($this->tmnCode ?? ''),
            'hash_secret_length' => strlen($this->hashSecret ?? ''),
            'hash_secret_preview' => substr($this->hashSecret ?? '', 0, 8) . '...' . substr($this->hashSecret ?? '', -8),
            'url' => $this->url,
            'return_url' => $this->returnUrl,
            'ipn_url' => $this->ipnUrl,
            'is_configured' => $this->isConfigured()
        ];
    }
}
