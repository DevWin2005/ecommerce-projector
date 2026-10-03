<?php

namespace App\Http\Controllers\Services;

use App\Models\Order;

class GHNOrderService
{
    public function __construct(private GHNService $ghn)
    {
    }

    public function create(Order $order, bool $isPaid = false): array
    {
        $order->loadMissing('items.product');

        $items = [];
        $weight = 0;

        foreach ($order->items as $item) {
            $itemWeight = (int) ($item->product->weight ?? 500);
            $weight += $itemWeight * (int) $item->quantity;
            $items[] = [
                'name' => $item->product_name ?? 'Máy chiếu chính hãng',
                'quantity' => (int) $item->quantity,
                'price' => (int) $item->price,
                'weight' => $itemWeight,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'name' => 'Máy chiếu chính hãng (Đơn #' . $order->order_code . ')',
                'quantity' => 1,
                'price' => (int) ($order->subtotal ?? $order->total_amount ?? 100000),
                'weight' => 500,
            ];
        }

        // Nếu paid = true hoặc thanh toán ngân hàng/momo -> cod = 0. Ngược lại cod tối đa theo shop hoặc giá trị đơn hàng
        $codAmount = 0;
        if (!$isPaid && $order->payment_status !== 'paid' && $order->payment_method === 'cod') {
            $codAmount = (int) $order->total_amount;
            if ($codAmount > 300000) {
                $codAmount = 300000;
            }
        }

        $payload = [
            'payment_type_id' => 1, // 2: Người nhận trả tiền cước
            'note' => $order->note ? 'Ghi chú: ' . $order->note : 'Đơn hàng #' . $order->order_code,
            'required_note' => 'KHONGCHOXEMHANG',
            'from_name' => 'Projector Shop',
            'from_phone' => '0988776655',
            'from_address' => '102 Nguyễn Trãi',
            'from_district_id' => (int) config('ghn.from_district_id', 3440),
            'to_name' => $order->customer_name,
            'to_phone' => $order->customer_phone,
            'to_address' => $order->shipping_address,
            'to_ward_code' => (string) $order->to_ward_code,
            'to_district_id' => (int) $order->to_district_id,
            'cod_amount' => $codAmount,
            'weight' => $weight > 0 ? $weight : 500,
            'length' => 20,
            'width' => 20,
            'height' => 15,
            'service_type_id' => 2,
            'items' => $items,
        ];

        return $this->ghn->createOrder($payload);
    }
}
