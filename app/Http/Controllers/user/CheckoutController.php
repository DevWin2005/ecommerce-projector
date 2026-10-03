<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Http\Controllers\Services\GHNService;
use App\Http\Controllers\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private function getSelectedCart(): array
    {
        $cart = session()->get('cart', []);
        $selectedIds = session()->get('selected_cart_items', null);

        if ($selectedIds === null) {
            return $cart;
        }

        $selectedMap = array_flip(array_map('strval', $selectedIds));

        return array_filter($cart, function ($key) use ($selectedMap) {
            return isset($selectedMap[(string) $key]);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function index()
    {
        $cart = $this->getSelectedCart();
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng hoặc danh sách sản phẩm được chọn đang trống! Vui lòng chọn sản phẩm trước khi thanh toán.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $appliedCoupon = session()->get('applied_coupon', null);
        $discountAmount = $appliedCoupon ? $appliedCoupon['discount'] : 0;
        $shippingFee = session()->get('shipping_fee', 0);
        $total = max(0, $subtotal - $discountAmount + $shippingFee);

        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->get();

        return view('checkout.index', compact('cart', 'subtotal', 'discountAmount', 'shippingFee', 'total', 'appliedCoupon', 'availableCoupons'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ], [
            'coupon_code.required' => 'Vui lòng nhập mã giảm giá.',
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Mã giảm giá không tồn tại hoặc đã hết hạn.');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return redirect()->back()->with('error', 'Mã giảm giá đã quá hạn sử dụng.');
        }

        $cart = $this->getSelectedCart();
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($subtotal < $coupon->min_order_amount) {
            return redirect()->back()->with('error', 'Đơn hàng các sản phẩm chọn mua tối thiểu phải từ ' . number_format($coupon->min_order_amount) . 'đ mới có thể sử dụng mã này.');
        }

        $discount = 0;
        if ($coupon->type === 'percent') {
            $discount = ($subtotal * $coupon->value) / 100;
        } else {
            $discount = $coupon->value;
        }

        session()->put('applied_coupon', [
            'code' => $coupon->code,
            'discount' => $discount,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return redirect()->back()->with('success', 'Đã áp dụng mã giảm giá "' . $coupon->code . '" thành công!');
    }

    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        return redirect()->back()->with('success', 'Đã bỏ áp dụng mã giảm giá.');
    }

    public function store(Request $request, GHNService $ghnService, GHNOrderService $ghnOrderService)
    {
        $cart = $this->getSelectedCart();
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => ['required', 'string', 'regex:/^(03|05|07|08|09)[0-9]{8}$/'],
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,momo,momo_atm,vnpay',
            'to_district_id' => 'nullable|integer',
            'to_ward_code' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ và tên nhận hàng.',
            'customer_email.required' => 'Vui lòng nhập địa chỉ email.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'customer_phone.regex' => 'Số điện thoại nhận hàng không hợp lệ. Vui lòng nhập số di động hợp lệ (đầu số 03, 05, 07, 08, 09 - ví dụ: 0988776655).',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ chi tiết giao hàng.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $subtotal = 0;
        $totalWeight = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $totalWeight += ((int)($item['weight'] ?? 500)) * (int)$item['quantity'];
        }

        $appliedCoupon = session()->get('applied_coupon', null);
        $discountAmount = $appliedCoupon ? $appliedCoupon['discount'] : 0;

        $shippingFee = (float) $request->input('shipping_fee', 0);
        $toDistrictId = $request->input('to_district_id');
        $toWardCode = $request->input('to_ward_code');

        // Tính lại cước GHN thực tế
        if ($toDistrictId && $toWardCode) {
            $feeRes = $ghnService->calculateFee([
                'from_district_id' => (int) config('ghn.from_district_id', 3440),
                'to_district_id' => (int) $toDistrictId,
                'to_ward_code' => (string) $toWardCode,
                'weight' => $totalWeight > 0 ? $totalWeight : 500,
                'length' => 20,
                'width' => 20,
                'height' => 15,
                'service_type_id' => 2,
            ]);

            if (isset($feeRes['code']) && $feeRes['code'] === 200 && isset($feeRes['data']['total'])) {
                $shippingFee = (float) $feeRes['data']['total'];
            }
        }

        $totalAmount = max(0, $subtotal - $discountAmount + $shippingFee);
        $orderCode = 'ORD-' . strtoupper(Str::random(6));

        $order = Order::create([
            'order_code' => $orderCode,
            'user_id' => auth()->check() ? auth()->id() : null,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'ghn_total_fee' => $shippingFee,
            'shipping_status' => null,
            'note' => $request->note,
        ]);

        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'product_name' => $item['name'],
                'product_image' => $item['image'] ?? null,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);

            $product = Product::find($productId);
            if ($product && $product->stock >= $item['quantity']) {
                $product->decrement('stock', $item['quantity']);
            }
        }

        // 1. Nếu phương thức thanh toán là MoMo (QR hoặc ATM) -> Chuyển hướng sang MomoController@start
        if (in_array($request->payment_method, ['momo', 'momo_atm'])) {
            $type = $request->payment_method === 'momo_atm' ? 'payWithATM' : 'captureWallet';
            return redirect()->route('momo.start', ['orderId' => $order->id, 'type' => $type]);
        }

        // 2. Nếu phương thức thanh toán là COD hoặc khác -> Lưu giao dịch COD & tự động bắn đơn GHN
        \App\Models\PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => 'COD_' . $order->id . '_' . time(),
            'amount' => $order->total_amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        // TỰ ĐỘNG BẮN ĐƠN HÀNG SANG BÊN GHN (https://5sao.ghn.dev/order hoặc https://khachhang.ghn.vn/order)
        if ($order->to_district_id && $order->to_ward_code) {
            try {
                $ghnRes = $ghnOrderService->create($order, $order->payment_status === 'paid');
                if (isset($ghnRes['code']) && $ghnRes['code'] === 200 && !empty($ghnRes['data']['order_code'])) {
                    $ghnCode = $ghnRes['data']['order_code'];
                    $fee = $ghnRes['data']['total_fee'] ?? $shippingFee;

                    $order->update([
                        'ghn_order_code' => $ghnCode,
                        'shipping_status' => 'ready_to_pick',
                        'ghn_total_fee' => $fee,
                        'status' => 'shipping',
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::warning('Tạo đơn GHN thất bại:', $ghnRes ?? []);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Tự động bắn đơn GHN thất bại: ' . $e->getMessage());
            }
        }

        // Xóa các sản phẩm vừa mua khỏi giỏ hàng, giữ lại sản phẩm chưa chọn
        $fullCart = session()->get('cart', []);
        foreach ($cart as $productId => $item) {
            unset($fullCart[$productId]);
        }
        if (empty($fullCart)) {
            session()->forget('cart');
        } else {
            session()->put('cart', $fullCart);
        }
        session()->forget('checkout_prefill');
        return redirect()->route('checkout.success', $order->order_code)->with('success', 'Đặt hàng thành công!');
    }

    public function success($orderCode)
    {
        $order = Order::with('items.product')->where('order_code', $orderCode)->firstOrFail();
        return view('checkout.success', compact('order'));
    }
}
