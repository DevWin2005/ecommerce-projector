<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GHNService;
use App\Http\Controllers\Services\GHNOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // Lọc theo trạng thái hệ thống
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo trạng thái vận chuyển GHN
        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $request->shipping_status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tìm kiếm theo từ khóa
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('ghn_order_code', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Đếm số lượng đơn theo từng trạng thái để làm badge tab lọc
        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id, GHNService $ghnService)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $newStatus = $request->status;

        // Xử lý trường hợp admin chọn hủy đơn hàng (status = cancelled)
        if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {
            return $this->cancelOrderProcess($order, $ghnService);
        }

        $order->status = $newStatus;
        $order->payment_status = $request->payment_status;
        if ($request->filled('shipping_status')) {
            $order->shipping_status = $request->shipping_status;
        }
        $order->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    }

    // Hủy đơn hàng phía Admin (kèm kiểm tra API GHN)
    public function cancelOrder($id, GHNService $ghnService)
    {
        $order = Order::findOrFail($id);
        return $this->cancelOrderProcess($order, $ghnService);
    }

    // Hàm nội bộ xử lý logic hủy đơn với GHN
    private function cancelOrderProcess(Order $order, GHNService $ghnService)
    {
        if ($order->status === 'cancelled') {
            return redirect()->back()->with('error', 'Đơn hàng này đã bị hủy trước đó.');
        }

        // Danh sách các trạng thái GHN đang giao hàng -> KHÔNG CHO HỦY
        $deliveringStatuses = ['picking', 'storing', 'transporting', 'delivering'];

        // Nếu đơn đã có mã vận đơn GHN, kiểm tra API thực tế của GHN
        if ($order->ghn_order_code) {
            $detail = $ghnService->getOrderDetail($order->ghn_order_code);

            if (isset($detail['code']) && $detail['code'] === 200 && isset($detail['data']['status'])) {
                $currentGhnStatus = strtolower($detail['data']['status']);
                $order->shipping_status = $currentGhnStatus;
                $order->save();

                if (in_array($currentGhnStatus, $deliveringStatuses)) {
                    $statusNameMap = [
                        'picking' => 'Đang lấy hàng',
                        'storing' => 'Đang nhập kho GHN',
                        'transporting' => 'Đang trung chuyển',
                        'delivering' => 'Đang giao tới khách hàng',
                    ];
                    $label = $statusNameMap[$currentGhnStatus] ?? $currentGhnStatus;

                    return redirect()->back()->with('error', "Không thể hủy đơn hàng! Đơn hàng GHN đang trong quá trình giao hàng ({$label} - status: {$currentGhnStatus}).");
                }
            } elseif (in_array(strtolower($order->shipping_status), $deliveringStatuses)) {
                return redirect()->back()->with('error', "Không thể hủy đơn hàng! Trạng thái vận đơn GHN hiện tại không cho phép hủy (Trạng thái: {$order->shipping_status}).");
            }

            // Nếu trạng thái GHN hợp lệ cho phép hủy (e.g. ready_to_pick / pending), gửi request hủy sang GHN
            $cancelRes = $ghnService->cancelOrder([$order->ghn_order_code]);
            if (isset($cancelRes['code']) && $cancelRes['code'] !== 200 && !empty($cancelRes['message'])) {
                // Nếu GHN từ chối hủy
                return redirect()->back()->with('error', 'GHN API không đồng ý hủy vận đơn này: ' . $cancelRes['message']);
            }
        }

        // Cập nhật trạng thái hủy đơn trong cơ sở dữ liệu
        $order->status = 'cancelled';
        $order->shipping_status = 'cancel';
        $order->save();

        return redirect()->back()->with('success', 'Đã hủy đơn hàng và đồng bộ trạng thái hủy với GHN thành công!');
    }

    // Đẩy đơn hàng sang Giao Hàng Nhanh (GHN)
    public function createGHNOrder($id, GHNOrderService $ghnOrderService)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->ghn_order_code) {
            return redirect()->back()->with('error', 'Đơn hàng này đã có mã vận đơn GHN: ' . $order->ghn_order_code);
        }

        if (!$order->to_district_id || !$order->to_ward_code) {
            return redirect()->back()->with('error', 'Đơn hàng chưa có thông tin Quận/Huyện hoặc Phường/Xã để tạo vận đơn GHN.');
        }

        $result = $ghnOrderService->create($order, $order->payment_status === 'paid');

        if (isset($result['code']) && $result['code'] === 200 && !empty($result['data']['order_code'])) {
            $ghnCode = $result['data']['order_code'];
            $fee = $result['data']['total_fee'] ?? $order->shipping_fee;

            $order->update([
                'ghn_order_code' => $ghnCode,
                'shipping_status' => 'ready_to_pick',
                'ghn_total_fee' => $fee,
                'status' => 'shipping',
            ]);

            return redirect()->back()->with('success', 'Tạo vận đơn GHN thành công! Mã vận đơn: ' . $ghnCode);
        }

        $errMsg = $result['message'] ?? ($result['message_display'] ?? 'Tạo đơn GHN thất bại.');
        return redirect()->back()->with('error', 'Không thể tạo vận đơn GHN: ' . $errMsg);
    }

    // Đồng bộ trạng thái đơn hàng từ GHN
    public function syncGHNStatus($id, GHNService $ghnService)
    {
        $order = Order::findOrFail($id);

        if (!$order->ghn_order_code) {
            return redirect()->back()->with('error', 'Đơn hàng này chưa được tạo mã vận đơn trên GHN.');
        }

        $result = $ghnService->getOrderDetail($order->ghn_order_code);

        if (isset($result['code']) && $result['code'] === 200 && isset($result['data']['status'])) {
            $ghnStatus = strtolower($result['data']['status']);
            $order->shipping_status = $ghnStatus;

            if (in_array($ghnStatus, ['delivered', 'finish'])) {
                $order->status = 'completed';
                $order->payment_status = 'paid';
            } elseif (in_array($ghnStatus, ['cancel', 'return'])) {
                $order->status = 'cancelled';
            } elseif (in_array($ghnStatus, ['picking', 'storing', 'transporting', 'delivering'])) {
                $order->status = 'shipping';
            }

            $order->save();

            return redirect()->back()->with('success', 'Đã đồng bộ trạng thái GHN thành công: ' . $ghnStatus);
        }

        return redirect()->back()->with('error', 'Không thể lấy thông tin từ GHN: ' . ($result['message'] ?? 'Lỗi không xác định'));
    }

    /**
     * Xử lý đồng bộ hàng loạt đơn hàng (Bulk Action)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
            'action' => 'required|string',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một đơn hàng để xử lý hàng loạt.',
            'action.required' => 'Vui lòng chọn thao tác hàng loạt.',
        ]);

        $ids = $request->ids;
        $action = $request->action;
        $count = count($ids);

        if (str_starts_with($action, 'status_')) {
            $newStatus = str_replace('status_', '', $action);
            if (in_array($newStatus, ['pending', 'processing', 'shipping', 'completed', 'cancelled'])) {
                Order::whereIn('id', $ids)->update(['status' => $newStatus]);
                if ($newStatus === 'completed') {
                    Order::whereIn('id', $ids)->update(['payment_status' => 'paid']);
                }
                return redirect()->back()->with('success', "Đã cập nhật trạng thái hàng loạt cho {$count} đơn hàng!");
            }
        } elseif ($action === 'payment_paid') {
            Order::whereIn('id', $ids)->update(['payment_status' => 'paid']);
            return redirect()->back()->with('success', "Đã cập nhật thanh toán 'Đã thanh toán' hàng loạt cho {$count} đơn hàng!");
        }

        return redirect()->back()->with('error', 'Thao tác xử lý hàng loạt không hợp lệ.');
    }
}
