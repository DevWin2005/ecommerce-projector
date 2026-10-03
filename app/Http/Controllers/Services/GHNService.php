<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    protected string $baseUrl;
    protected string $token;
    protected int $shopId;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl = config('ghn.base_url', 'https://online-gateway.ghn.vn/shiip/public-api');
        $this->token = config('ghn.token', '31622857-aa86-11f1-957c-ee0eb02815e1');
        $this->shopId = (int) config('ghn.shop_id', 6651725);
        $this->verifySsl = (bool) config('ghn.verify_ssl', false);
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => $this->verifySsl,
            ])
            ->acceptJson()
            ->timeout(15)
            ->withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json',
            ]);
    }

    // Lấy danh sách Tỉnh/Thành từ GHN API
    public function getProvinces(): array
    {
        return $this->get('/master-data/province');
    }

    // Lấy danh sách Quận/Huyện từ GHN API
    public function getDistricts(int $provinceId): array
    {
        return $this->get('/master-data/district', [
            'province_id' => $provinceId,
        ]);
    }

    // Lấy danh sách Phường/Xã từ GHN API
    public function getWards(int $districtId): array
    {
        return $this->get('/master-data/ward', [
            'district_id' => $districtId,
        ]);
    }

    // Tính phí vận chuyển GHN
    public function calculateFee(array $params): array
    {
        return $this->post('/v2/shipping-order/fee', array_merge([
            'shop_id' => $this->shopId,
        ], $params));
    }

    // Tạo đơn giao hàng GHN
    public function createOrder(array $orderData): array
    {
        return $this->post('/v2/shipping-order/create', array_merge([
            'shop_id' => $this->shopId,
        ], $orderData));
    }

    // Lấy chi tiết thông tin đơn hàng từ GHN
    public function getOrderDetail(string $orderCode): array
    {
        return $this->post('/v2/shipping-order/detail', [
            'order_code' => $orderCode,
        ]);
    }

    // Hủy đơn hàng GHN
    public function cancelOrder(array $orderCodes): array
    {
        return $this->post('/v2/switch-status/cancel', [
            'order_codes' => $orderCodes,
            'shop_id' => $this->shopId,
        ]);
    }

    protected function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client()->get($uri, $query);
            if (!$response->successful()) {
                $json = $response->json();
                Log::warning('GHN GET request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $json,
                ]);
                return $json ?? ['code' => $response->status(), 'message' => 'GHN API request failed.', 'data' => null];
            }
            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.', 'data' => null];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN.', 'data' => null];
        }
    }

    protected function post(string $uri, array $payload): array
    {
        try {
            $response = $this->client()->post($uri, $payload);

            if (!$response->successful()) {
                $json = $response->json();
                Log::warning('GHN POST request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $json,
                ]);
                return $json ?? ['code' => $response->status(), 'message' => 'GHN API request failed.', 'data' => null];
            }
            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.', 'data' => null];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN.', 'data' => null];
        }
    }
}
