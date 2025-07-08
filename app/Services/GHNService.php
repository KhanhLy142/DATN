<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    private $token;
    private $baseUrl;
    private $shopId;
    private $testMode;
    private $autoCreateOrder;

    public function __construct()
    {
        $this->token = config('services.ghn.token', '316ea3e3-53fc-11f0-989d-42259a3f1d4c');
        $this->baseUrl = config('services.ghn.base_url', 'https://online-gateway.ghn.vn/shiip/public-api');
        $this->shopId = config('services.ghn.shop_id', 4583816);
        $this->testMode = config('services.ghn.test_mode', true);
        $this->autoCreateOrder = config('services.ghn.auto_create_order', false);
    }

    public function calculateShippingFee($data)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/shipping-order/fee', [
                'service_id' => null,
                'service_type_id' => $data['service_type_id'] ?? 2,
                'to_province_id' => $data['to_province_id'],
                'to_district_id' => $data['to_district_id'],
                'to_ward_code' => $data['to_ward_code'],
                'weight' => $data['weight'] ?? 500,
                'length' => $data['length'] ?? 20,
                'width' => $data['width'] ?? 20,
                'height' => $data['height'] ?? 10,
                'insurance_value' => $data['insurance_value'] ?? 0,
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['code'] == 200) {
                    return [
                        'success' => true,
                        'fee' => $result['data']['total'] ?? 0,
                        'service_fee' => $result['data']['service_fee'] ?? 0,
                        'insurance_fee' => $result['data']['insurance_fee'] ?? 0,
                        'service_id' => $result['data']['service_id'] ?? null,
                        'expected_delivery_time' => $result['data']['expected_delivery_time'] ?? null,
                        'raw_data' => $result['data']
                    ];
                }
            }

            return $this->getFallbackShippingFee($data);

        } catch (\Exception $e) {
            Log::error('GHN Calculate Shipping Fee Error: ' . $e->getMessage());
            return $this->getFallbackShippingFee($data);
        }
    }

    public function createOrder($orderData)
    {
        if ($this->testMode) {
            return $this->createFakeOrder($orderData);
        }

        return $this->createRealOrder($orderData);
    }

    private function createRealOrder($orderData)
    {
        try {
            Log::info('Creating REAL GHN Order', ['data' => $orderData]);

            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/shipping-order/create', $orderData);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['code'] == 200) {
                    Log::info('GHN Order Created Successfully', ['result' => $result]);

                    return [
                        'success' => true,
                        'order_code' => $result['data']['order_code'],
                        'tracking_number' => $result['data']['order_code'],
                        'expected_delivery_time' => $result['data']['expected_delivery_time'] ?? null,
                        'fee' => $result['data']['fee'] ?? 0,
                        'is_real' => true,
                        'raw_data' => $result['data']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Tạo đơn GHN thất bại',
                        'code' => $result['code'],
                        'is_real' => true
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không thể kết nối tới GHN API',
                'code' => $response->status(),
                'is_real' => true
            ];

        } catch (\Exception $e) {
            Log::error('GHN Create Real Order Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi tạo đơn GHN: ' . $e->getMessage(),
                'is_real' => true
            ];
        }
    }

    private function createFakeOrder($orderData)
    {
        try {
            Log::info('Creating FAKE GHN Order for testing', ['data' => $orderData]);

            usleep(500000);

            $fakeTrackingNumber = 'TEST_' . date('Ymd') . '_' . strtoupper(substr(md5(uniqid()), 0, 8));

            $expectedDelivery = now()->addDays(rand(2, 5))->toISOString();

            return [
                'success' => true,
                'order_code' => $fakeTrackingNumber,
                'tracking_number' => $fakeTrackingNumber,
                'expected_delivery_time' => $expectedDelivery,
                'fee' => $orderData['cod_amount'] > 0 ? 35000 : 30000,
                'is_real' => false,
                'raw_data' => [
                    'order_code' => $fakeTrackingNumber,
                    'sort_code' => 'FAKE001',
                    'trans_type' => 'test',
                    'ward_encode' => '',
                    'district_encode' => '',
                    'fee' => [
                        'main_service' => 25000,
                        'insurance' => 0,
                        'cod_fee' => 5000,
                        'station_do' => 0,
                        'station_pu' => 0,
                        'return' => 0,
                        'r2s' => 0,
                        'coupon' => 0
                    ],
                    'total_fee' => 30000,
                    'expected_delivery_time' => $expectedDelivery
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Create Fake Order Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi tạo đơn test: ' . $e->getMessage(),
                'is_real' => false
            ];
        }
    }

    public function trackOrder($orderCode)
    {
        if (str_starts_with($orderCode, 'TEST_')) {
            return $this->getFakeTrackingData($orderCode);
        }

        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/shipping-order/detail', [
                'order_code' => $orderCode
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['code'] == 200) {
                    return [
                        'success' => true,
                        'status' => $result['data']['status'],
                        'status_text' => $this->getStatusText($result['data']['status']),
                        'logs' => $result['data']['logs'] ?? [],
                        'is_real' => true,
                        'raw_data' => $result['data']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không thể tra cứu đơn hàng',
                'is_real' => true
            ];

        } catch (\Exception $e) {
            Log::error('GHN Track Order Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi tra cứu đơn hàng: ' . $e->getMessage(),
                'is_real' => true
            ];
        }
    }

    private function getFakeTrackingData($orderCode)
    {
        $createdDate = \Carbon\Carbon::createFromFormat('Ymd', substr($orderCode, 5, 8));
        $daysPassed = now()->diffInDays($createdDate);

        $statuses = [
            ['status' => 'ready_to_pick', 'time' => $createdDate->addHour(), 'description' => 'Đơn hàng đã được tạo'],
            ['status' => 'picked', 'time' => $createdDate->addHours(2), 'description' => 'Đã lấy hàng'],
            ['status' => 'transporting', 'time' => $createdDate->addDay(), 'description' => 'Đang vận chuyển'],
            ['status' => 'delivering', 'time' => $createdDate->addDays(2), 'description' => 'Đang giao hàng'],
            ['status' => 'delivered', 'time' => $createdDate->addDays(3), 'description' => 'Đã giao hàng thành công']
        ];

        $currentStatusIndex = min($daysPassed, count($statuses) - 1);
        $currentStatus = $statuses[$currentStatusIndex]['status'];

        return [
            'success' => true,
            'status' => $currentStatus,
            'status_text' => $this->getStatusText($currentStatus),
            'logs' => array_slice($statuses, 0, $currentStatusIndex + 1),
            'is_real' => false,
            'raw_data' => [
                'order_code' => $orderCode,
                'status' => $currentStatus,
                'logs' => array_slice($statuses, 0, $currentStatusIndex + 1)
            ]
        ];
    }

    private function getFallbackShippingFee($data)
    {
        $baseFee = 30000;

        $majorCities = [201, 202, 203, 204, 205];
        if (in_array($data['to_province_id'], $majorCities)) {
            $baseFee = 25000;
        }

        if (($data['service_type_id'] ?? 2) == 1) {
            $baseFee += 20000;
        }

        return [
            'success' => true,
            'fee' => $baseFee,
            'service_fee' => $baseFee,
            'insurance_fee' => 0,
            'service_id' => null,
            'fallback' => true
        ];
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'cancel' => 'Đã hủy',
            'picked' => 'Đã lấy hàng',
            'storing' => 'Hàng đang trong kho',
            'transporting' => 'Đang vận chuyển',
            'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'delivery_fail' => 'Giao hàng thất bại',
            'waiting_to_return' => 'Chờ trả hàng',
            'return' => 'Trả hàng',
            'returned' => 'Đã trả hàng',
            'exception' => 'Có vấn đề',
            'damage' => 'Hàng bị hỏng',
            'lost' => 'Hàng bị thất lạc'
        ];

        return $statusMap[$status] ?? $status;
    }

    public function isTestMode()
    {
        return $this->testMode;
    }

    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
    }
}
