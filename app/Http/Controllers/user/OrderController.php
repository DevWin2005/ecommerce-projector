<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Lịch sử đơn hàng cá nhân của User
    public function myOrders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        $userId = Auth::id();
        $counts = [
            'all' => Order::where('user_id', $userId)->count(),
            'pending' => Order::where('user_id', $userId)->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', $userId)->where('status', 'processing')->count(),
            'shipping' => Order::where('user_id', $userId)->where('status', 'shipping')->count(),
            'completed' => Order::where('user_id', $userId)->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', $userId)->where('status', 'cancelled')->count(),
        ];

        return view('orders.index', compact('orders', 'counts'));
    }

    // Chi tiết đơn hàng cá nhân
    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->user_id !== Auth::id() && (!Auth::check() || Auth::user()->role !== 'admin')) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return view('orders.show', compact('order'));
    }

    // Hủy đơn hàng phía khách hàng
    public function cancel($id, GHNService $ghnService)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && (!Auth::check() || Auth::user()->role !== 'admin')) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Đơn hàng này không thể hủy.');
        }

        if ($order->ghn_order_code) {
            $response = $ghnService->cancelOrder([$order->ghn_order_code]);
            if (isset($response['code']) && $response['code'] !== 200) {
                return back()->with('error', 'Giao Hàng Nhanh không cho phép hủy vận đơn này: ' . ($response['message'] ?? ''));
            }
        }

        $order->update([
            'status' => 'cancelled',
            'shipping_status' => 'cancel',
        ]);

        return back()->with('success', 'Đã hủy đơn hàng thành công.');
    }

    // Tra cứu đơn hàng vãng lai không cần đăng nhập
    public function showLookupForm()
    {
        return view('orders.lookup');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ], [
            'search.required' => 'Vui lòng nhập mã đơn hàng hoặc số điện thoại.',
        ]);

        $search = trim($request->search);
        $order = Order::with('items.product')
            ->where('order_code', $search)
            ->orWhere('customer_phone', $search)
            ->orWhere('ghn_order_code', $search)
            ->latest()
            ->first();

        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng phù hợp với thông tin tra cứu.');
        }

        return view('orders.show', compact('order'));
    }

    // Hiển thị giao diện thanh toán đơn hàng (payment/index hoặc checkout/index)
    public function checkout()
    {
        return app(CheckoutController::class)->index();
    }

    // Xử lý lưu đơn hàng
    public function processOrder(Request $request, \App\Http\Controllers\Services\GHNService $ghnService, \App\Http\Controllers\Services\GHNOrderService $ghnOrderService)
    {
        return app(CheckoutController::class)->store($request, $ghnService, $ghnOrderService);
    }

    // Hủy đơn cũ & Khôi phục sản phẩm vào giỏ hàng để sửa thông tin / phương thức thanh toán
    public function reorderAndEdit($id)
    {
        $order = Order::with('items')->findOrFail($id);

        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        $cart = session()->get('cart', []);
        $selectedIds = [];

        foreach ($order->items as $item) {
            $cart[$item->product_id] = [
                'name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $item->product_image,
            ];
            $selectedIds[] = (string) $item->product_id;
        }

        session()->put('cart', $cart);
        session()->put('selected_cart_items', $selectedIds);

        // Pre-fill thông tin địa chỉ cũ để khách hàng không phải nhập lại từ đầu
        session()->put('checkout_prefill', [
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'shipping_address' => $order->shipping_address,
            'note' => $order->note,
        ]);

        // Nếu đơn hàng chưa thanh toán và ở trạng thái pending, tự động hủy đơn cũ để tránh đơn lặp
        if ($order->status === 'pending' && $order->payment_status !== 'paid') {
            $order->update([
                'status' => 'cancelled',
                'shipping_status' => 'cancel',
            ]);

            if ($order->ghn_order_code) {
                try {
                    app(GHNService::class)->cancelOrder([$order->ghn_order_code]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Hủy đơn GHN khi reorder thất bại: ' . $e->getMessage());
                }
            }
            $msg = 'Đã hủy đơn hàng cũ #' . $order->order_code . ' và khôi phục toàn bộ sản phẩm vào giỏ hàng. Bạn có thể chỉnh sửa lại địa chỉ hoặc chọn lại phương thức thanh toán!';
        } else {
            $msg = 'Đã thêm các sản phẩm từ đơn hàng #' . $order->order_code . ' vào giỏ hàng!';
        }

        return redirect()->route('checkout.index')->with('success', $msg);
    }

    // Đổi phương thức thanh toán cho đơn hàng chưa thanh toán
    public function changePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,bank_transfer,momo,momo_atm,vnpay',
        ]);

        $order = Order::findOrFail($id);

        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Đơn hàng này đã được thanh toán, không thể đổi phương thức.');
        }

        $order->update([
            'payment_method' => $request->payment_method,
        ]);

        if (in_array($request->payment_method, ['momo', 'momo_atm'])) {
            $type = $request->payment_method === 'momo_atm' ? 'payWithATM' : 'captureWallet';
            return redirect()->route('momo.start', ['orderId' => $order->id, 'type' => $type]);
        }

        return back()->with('success', 'Đã cập nhật phương thức thanh toán thành công!');
    }
}
