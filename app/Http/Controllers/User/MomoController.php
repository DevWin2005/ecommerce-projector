<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GHNOrderService;
use App\Http\Controllers\Services\MomoService;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    protected MomoService $momoService;
    protected GHNOrderService $ghnOrderService;

    public function __construct(MomoService $momoService, GHNOrderService $ghnOrderService)
    {
        $this->momoService = $momoService;
        $this->ghnOrderService = $ghnOrderService;
    }

    /**
     * 1. start — bắt đầu thanh toán
     */
    public function start($orderId, Request $request)
    {
        $order = Order::findOrFail($orderId);
        // Ưu tiên mặc định payWithATM (Thẻ ATM) nếu không chỉ định cụ thể
        $requestType = $request->input('type') === 'captureWallet' ? 'captureWallet' : 'payWithATM';

        // Tạo bản ghi giao dịch mới
        $transaction = $this->newTransaction($order, $requestType === 'payWithATM' ? 'momo_atm' : 'momo');

        // Gọi MoMo API và chuyển hướng
        return $this->redirectToMomo($order, $transaction, $requestType);
    }

    /**
     * 2. payAgain — thanh toán lại đơn hàng chưa hoàn tất mà không tạo đơn mới
     */
    public function payAgain($orderId, Request $request)
    {
        $order = Order::findOrFail($orderId);
        // Ưu tiên mặc định payWithATM (Thẻ ATM) nếu không chỉ định cụ thể
        $requestType = $request->input('type') === 'captureWallet' ? 'captureWallet' : 'payWithATM';

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Đơn hàng này đã bị hủy, không thể thanh toán lại.');
        }

        // Tạo một lượt thử giao dịch mới
        $transaction = $this->newTransaction($order, $requestType === 'payWithATM' ? 'momo_atm' : 'momo');

        return $this->redirectToMomo($order, $transaction, $requestType);
    }

    /**
     * 3. newTransaction — lưu một lần thử thanh toán
     */
    public function newTransaction(Order $order, string $method = 'momo'): PaymentTransaction
    {
        $transactionId = 'MOMO_' . $order->id . '_' . time() . '_' . rand(100, 999);

        return PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'amount' => $order->total_amount,
            'payment_method' => $method,
            'status' => 'pending',
            'response_data' => null,
        ]);
    }

    /**
     * 4. redirectToMomo — lấy đường dẫn thanh toán
     */
    public function redirectToMomo(Order $order, PaymentTransaction $transaction, string $requestType = 'captureWallet')
    {
        $response = $this->momoService->createPayment($order, $transaction->transaction_id, $requestType);

        if (isset($response['resultCode']) && (int)$response['resultCode'] === 0 && !empty($response['payUrl'])) {
            return redirect()->away($response['payUrl']);
        }

        // Đánh dấu giao dịch này là thất bại nếu không lấy được URL
        $this->momoService->markFailed($transaction, $response ?? []);

        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Không thể kết nối với MoMo: ' . ($response['message'] ?? 'Lỗi không xác định'));
    }

    /**
     * 5. callback — xử lý khi khách quay về website
     */
    public function callback(Request $request)
    {
        $data = $request->all();
        Log::info('MoMo Callback Received:', $data);

        $orderId = $this->momoService->orderId($data);
        $transactionId = $data['orderId'] ?? ($data['requestId'] ?? null);

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        if (!$orderId && $transaction) {
            $orderId = $transaction->order_id;
        }

        $order = $orderId ? Order::find($orderId) : null;

        if ($this->momoService->isValidSuccessfulResponse($data)) {
            if ($order) {
                $this->completePayment($order->id, $transactionId, $data);
                return redirect()->route('checkout.success', $order->order_code)
                    ->with('success', 'Thanh toán qua Ví MoMo thành công!');
            }
        }

        // Trường hợp giao dịch thất bại hoặc bị hủy
        if ($order) {
            $this->markFailed($order->id, $transactionId, $data);
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Thanh toán qua MoMo không thành công hoặc đã bị hủy. Bạn có thể bấm nút "Thanh toán lại" bên dưới.');
        }

        return redirect()->route('orders.my')->with('error', 'Thanh toán MoMo thất bại.');
    }

    /**
     * 6. ipn — nhận thông báo trực tiếp từ MoMo (Webhook Web Async)
     */
    public function ipn(Request $request)
    {
        $data = $request->all();
        Log::info('MoMo IPN Webhook Received:', $data);

        if (!$this->momoService->isValidResponse($data)) {
            Log::warning('MoMo IPN Invalid Signature:', $data);
            return response()->json(['resultCode' => 97, 'message' => 'Invalid signature']);
        }

        $orderId = $this->momoService->orderId($data);
        $transactionId = $data['orderId'] ?? ($data['requestId'] ?? null);

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        if (!$orderId && $transaction) {
            $orderId = $transaction->order_id;
        }

        $order = $orderId ? Order::find($orderId) : null;

        if ($this->momoService->isSuccessful($data)) {
            if ($order) {
                $this->completePayment($order->id, $transactionId, $data);
            }
            return response()->json(['resultCode' => 0, 'message' => 'Success']);
        } else {
            if ($order) {
                $this->markFailed($order->id, $transactionId, $data);
            }
            return response()->json(['resultCode' => 0, 'message' => 'Processed failed transaction']);
        }
    }

    /**
     * 7. completePayment — xác nhận thanh toán và tạo vận đơn GHN
     */
    public function completePayment($orderId, $transactionId, array $data = [])
    {
        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        // 1. Cập nhật giao dịch MoMo -> success
        if ($transactionId) {
            $this->momoService->markPaid($transactionId, $data);
        }

        // 2. Cập nhật trạng thái đơn hàng -> paid
        $order->update([
            'payment_status' => 'paid',
            'payment_method' => 'momo',
            'status' => $order->status === 'pending' ? 'processing' : $order->status,
        ]);

        // 3. Tự động bắn đơn sang GHN nếu chưa có mã GHN
        if (!$order->ghn_order_code && $order->to_district_id && $order->to_ward_code) {
            try {
                $ghnRes = $this->ghnOrderService->create($order, true);
                if (isset($ghnRes['code']) && $ghnRes['code'] === 200 && !empty($ghnRes['data']['order_code'])) {
                    $order->update([
                        'ghn_order_code' => $ghnRes['data']['order_code'],
                        'shipping_status' => 'ready_to_pick',
                        'status' => 'shipping',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('MomoController GHN order creation error: ' . $e->getMessage());
            }
        }

        // 4. Xóa các sản phẩm đã mua thành công khỏi giỏ hàng
        $fullCart = session()->get('cart', []);
        $orderItemIds = $order->items ? $order->items->pluck('product_id')->toArray() : [];
        foreach ($orderItemIds as $productId) {
            unset($fullCart[$productId]);
        }
        if (empty($fullCart)) {
            session()->forget('cart');
        } else {
            session()->put('cart', $fullCart);
        }
        session()->forget(['applied_coupon', 'shipping_fee', 'selected_cart_items']);
    }

    /**
     * 8. markFailed — ghi nhận giao dịch thất bại
     */
    public function markFailed($orderId, $transactionId, array $data = [])
    {
        if ($transactionId) {
            $this->momoService->markFailed($transactionId, $data);
        }

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'failed',
                    'payment_method' => 'momo',
                ]);
            }
        }
    }
}
