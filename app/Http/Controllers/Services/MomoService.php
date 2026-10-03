<?php

namespace App\Http\Controllers\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    protected string $partnerCode;
    protected string $accessKey;
    protected string $secretKey;
    protected string $endpoint;
    protected string $redirectUrl;
    protected string $ipnUrl;

    public function __construct()
    {
        $this->partnerCode = (string) (config('services.momo.partner_code') ?: 'MOMO');
        $this->accessKey = (string) (config('services.momo.access_key') ?: 'F8BBA842ECF85');
        $this->secretKey = (string) (config('services.momo.secret_key') ?: 'K951B6FA292C6C82F060F45AEB8039E8');
        $this->endpoint = (string) (config('services.momo.endpoint') ?: 'https://test-payment.momo.vn/v2/gateway/api/create');
        $this->redirectUrl = (string) (config('services.momo.redirect_url') ?: url('/momo/callback'));
        $this->ipnUrl = (string) (config('services.momo.ipn_url') ?: url('/momo/ipn'));
    }

    /**
     * Tạo yêu cầu thanh toán MoMo (captureWallet hoặc payWithATM)
     */
    public function createPayment(Order $order, string $transactionId, string $requestType = 'captureWallet')
    {
        $orderId = $transactionId; // Sử dụng mã transaction duy nhất làm orderId trên MoMo
        $requestId = $transactionId;
        $amount = (string) (int) round($order->total_amount);
        $orderInfo = 'Thanh toan don hang #' . $order->order_code;
        $extraData = base64_encode(json_encode([
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'transaction_id' => $transactionId,
        ]));

        $rawHash = "accessKey=" . $this->accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $this->ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $this->partnerCode .
            "&redirectUrl=" . $this->redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Test Store',
            'storeId' => 'MomoTestStore',
            'requestId' => $requestId,
            'amount' => (int) $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'accessKey' => $this->accessKey,
            'signature' => $signature,
        ];

        try {
            $response = Http::withOptions(['verify' => false])->post($this->endpoint, $payload);
            $result = $response->json();
            Log::info('MoMo Create Payment Response:', $result ?? []);
            return $result;
        } catch (\Throwable $e) {
            Log::error('MoMo Create Payment Error: ' . $e->getMessage());
            return [
                'resultCode' => 99,
                'message' => 'Lỗi kết nối tới MoMo API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kiểm tra chữ ký callback/IPN MoMo gửi về
     */
    public function isValidResponse(array $data): bool
    {
        if (empty($data['signature'])) {
            return false;
        }

        $accessKey = $this->accessKey;
        $amount = $data['amount'] ?? '';
        $extraData = $data['extraData'] ?? '';
        $message = $data['message'] ?? '';
        $orderId = $data['orderId'] ?? '';
        $orderInfo = $data['orderInfo'] ?? '';
        $orderType = $data['orderType'] ?? '';
        $partnerCode = $data['partnerCode'] ?? '';
        $payType = $data['payType'] ?? '';
        $requestId = $data['requestId'] ?? '';
        $responseTime = $data['responseTime'] ?? '';
        $resultCode = $data['resultCode'] ?? '';
        $transId = $data['transId'] ?? '';

        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&message=" . $message .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType .
            "&partnerCode=" . $partnerCode .
            "&payType=" . $payType .
            "&requestId=" . $requestId .
            "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode .
            "&transId=" . $transId;

        $expectedSignature = hash_hmac("sha256", $rawHash, $this->secretKey);

        return hash_equals($expectedSignature, $data['signature']);
    }

    /**
     * Kiểm tra MoMo có báo thanh toán thành công hay không (resultCode == 0)
     */
    public function isSuccessful(array $data): bool
    {
        return isset($data['resultCode']) && (int) $data['resultCode'] === 0;
    }

    /**
     * Kiểm tra callback MoMo hợp lệ và thành công đầy đủ
     */
    public function isValidSuccessfulResponse(array $data): bool
    {
        return $this->isValidResponse($data) && $this->isSuccessful($data);
    }

    /**
     * Lấy ID đơn hàng nội bộ từ trường response MoMo
     */
    public function orderId(array $data)
    {
        if (!empty($data['extraData'])) {
            $decoded = json_decode(base64_decode($data['extraData']), true);
            if (!empty($decoded['order_id'])) {
                return $decoded['order_id'];
            }
        }

        // Tìm thông qua transaction_id trùng với orderId trong response MoMo
        if (!empty($data['orderId'])) {
            $tx = PaymentTransaction::where('transaction_id', $data['orderId'])->first();
            if ($tx) {
                return $tx->order_id;
            }
        }

        return null;
    }

    /**
     * Cập nhật giao dịch sau khi MoMo thanh toán thành công
     */
    public function markPaid($transactionId, array $data = [])
    {
        $tx = $transactionId instanceof PaymentTransaction
            ? $transactionId
            : PaymentTransaction::where('transaction_id', $transactionId)->first();

        if ($tx) {
            $tx->update([
                'status' => 'success',
                'response_data' => $data,
            ]);
        }

        return $tx;
    }

    /**
     * Cập nhật giao dịch thất bại hoặc bị hủy
     */
    public function markFailed($transactionId, array $data = [])
    {
        $tx = $transactionId instanceof PaymentTransaction
            ? $transactionId
            : PaymentTransaction::where('transaction_id', $transactionId)->first();

        if ($tx) {
            $tx->update([
                'status' => 'failed',
                'response_data' => $data,
            ]);
        }

        return $tx;
    }
}
