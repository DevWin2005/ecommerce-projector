@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small shadow-sm bg-white mb-3 d-inline-block">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Danh sách Đơn hàng
    </a>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Hóa Đơn Chi Tiết: <span class="text-primary">{{ $order->order_code }}</span></h3>
            <p class="text-muted small mb-0">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
        </div>
        @if($order->ghn_order_code)
            <div class="bg-primary text-white px-3 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-truck-fast text-warning me-1"></i> Mã Vận Đơn GHN: <strong class="text-warning font-monospace fs-6">{{ $order->ghn_order_code }}</strong>
            </div>
        @endif
    </div>
</div>

<div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
    <div class="row g-3 mb-4 small">
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <strong class="d-block text-dark mb-1"><i class="fa-solid fa-user me-1"></i> Thông tin giao hàng:</strong>
                <div><strong>Người nhận:</strong> {{ $order->customer_name }}</div>
                <div><strong>Điện thoại:</strong> <strong class="text-primary">{{ $order->customer_phone }}</strong></div>
                <div><strong>Email:</strong> {{ $order->customer_email }}</div>
                <div><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <strong class="d-block text-dark mb-1"><i class="fa-solid fa-wallet me-1"></i> Trạng thái thanh toán:</strong>
                <div><strong>Phương thức:</strong> <span class="badge bg-secondary text-uppercase">{{ $order->payment_method }}</span></div>
                <div><strong>Thanh toán:</strong> 
                    @if($order->payment_status === 'paid')
                        <span class="badge bg-success">Đã thanh toán</span>
                    @elseif($order->payment_status === 'failed')
                        <span class="badge bg-danger">Thanh toán thất bại</span>
                    @else
                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                    @endif
                </div>
                <div><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</div>
                @if($order->payment_status !== 'paid' && !in_array($order->status, ['completed', 'cancelled']))
                    <div class="mt-3 d-flex flex-column gap-2">
                        <form action="{{ route('orders.reorder', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold text-dark shadow-sm py-2">
                                <i class="fa-solid fa-rotate-left me-1"></i> HỦY & SỬA LẠI ĐƠN HÀNG NÀY
                            </button>
                        </form>
                        <a href="{{ route('momo.pay-again', ['orderId' => $order->id, 'type' => 'payWithATM']) }}" class="btn btn-danger btn-sm w-100 fw-bold shadow-sm py-2">
                            <i class="fa-solid fa-credit-card me-1"></i> Thanh Toán Thẻ ATM MoMo
                        </a>
                        <a href="{{ route('momo.pay-again', ['orderId' => $order->id, 'type' => 'captureWallet']) }}" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                            <i class="fa-solid fa-qrcode me-1"></i> Thanh Toán Ví MoMo QR
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <strong class="d-block text-dark mb-1"><i class="fa-solid fa-truck-ramp-box me-1"></i> Vận đơn Giao Hàng Nhanh (GHN):</strong>
                <div><strong>Mã vận đơn GHN:</strong> <span class="badge bg-primary font-monospace">{{ $order->ghn_order_code ?? ($order->payment_status === 'paid' ? 'Chờ Admin đẩy đơn' : 'Chờ khởi tạo') }}</span></div>
                <div><strong>Trạng thái vận chuyển:</strong> <span class="badge bg-info text-white">{{ $order->shipping_status ?? ($order->payment_status === 'paid' ? 'Chờ xử lý GHN' : 'Chờ thanh toán') }}</span></div>
                <div><strong>Cước phí GHN:</strong> <strong class="text-danger">{{ number_format($order->shipping_fee) }} đ</strong></div>
            </div>
        </div>
    </div>

    <h6 class="fw-bold text-dark mb-3">DANH SÁCH MÁY CHIẾU</h6>
    <table class="table align-middle small mb-0">
        <thead class="bg-light">
            <tr>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($item->product_image)
                                <img src="{{ Str::startsWith($item->product_image, 'http') ? $item->product_image : asset('storage/' . $item->product_image) }}" 
                                     width="45" height="45" class="rounded border" style="object-fit: contain;">
                            @endif
                            <strong class="text-dark">{{ $item->product_name }}</strong>
                        </div>
                    </td>
                    <td>{{ number_format($item->price) }} đ</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="fw-bold text-danger">{{ number_format($item->subtotal) }} đ</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Tạm tính:</td>
                <td>{{ number_format($order->subtotal) }} đ</td>
            </tr>
            @if($order->discount_amount > 0)
                <tr>
                    <td colspan="3" class="text-end fw-bold text-success">Giảm giá Voucher:</td>
                    <td class="text-success fw-bold">-{{ number_format($order->discount_amount) }} đ</td>
                </tr>
            @endif
            <tr>
                <td colspan="3" class="text-end fw-bold text-primary">Phí giao hàng GHN:</td>
                <td class="text-primary fw-bold">{{ number_format($order->shipping_fee) }} đ</td>
            </tr>
            <tr class="fs-6 bg-light">
                <td colspan="3" class="text-end fw-bold text-dark">TỔNG CỘNG THANH TOÁN:</td>
                <td class="fw-bold text-danger fs-5">{{ number_format($order->total_amount) }} đ</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
