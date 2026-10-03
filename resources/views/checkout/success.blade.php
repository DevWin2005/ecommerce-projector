@extends('layouts.app')

@section('content')
<div class="max-w-800 mx-auto my-4 text-center">
    <!-- TOP NAVIGATION BACK BAR -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small shadow-sm bg-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Trang chủ
        </a>
        <a href="{{ route('orders.my') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small shadow-sm bg-white">
            <i class="fa-solid fa-box-archive me-1"></i> Đơn hàng của tôi
        </a>
    </div>

    <!-- HERO CHECKMARK ANIMATION -->
    <div class="bg-white p-5 rounded-4 border shadow-sm mb-4">
        <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle p-4 shadow" style="width: 90px; height: 90px;">
                <i class="fa-solid fa-check fa-3x"></i>
            </span>
        </div>
        
        <h2 class="fw-extrabold text-dark mb-2">ĐẶT HÀNG THÀNH CÔNG!</h2>
        <p class="text-secondary mb-3">Cảm ơn bạn đã tin tưởng mua sắm máy chiếu tại <strong>PROJECTOR SHOP</strong>.</p>

        <!-- MÃ ĐƠN HÀNG SHOP & MÃ VẬN ĐƠN GHN KHỚP 100% -->
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2 mb-4">
            <div class="bg-light px-4 py-2 rounded-pill border">
                Mã Đơn Hàng Shop: <strong class="text-primary fs-5 ms-1">{{ $order->order_code }}</strong>
            </div>

            @if($order->ghn_order_code)
                <div class="bg-primary text-white px-4 py-2 rounded-pill border shadow-sm d-flex align-items-center gap-2">
                    <i class="fa-solid fa-truck-fast text-warning"></i>
                    <span>Mã Vận Đơn GHN: <strong class="text-warning fs-5 ms-1 font-monospace">{{ $order->ghn_order_code }}</strong></span>
                </div>
            @endif
        </div>

        <!-- NẾU NHẬP SAI ĐỊA CHỈ HOẶC CHỌN NHẦM PTTT: NÚT HỦY ĐƠN & SỬA LẠI (RESTORE CART) -->
        @if($order->payment_status !== 'paid' && !in_array($order->status, ['completed', 'cancelled']))
            <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-4 text-start mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <strong class="text-dark d-block mb-1"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Nhập sai địa chỉ hoặc chọn nhầm Phương thức thanh toán?</strong>
                        <small class="text-muted">Bấm vào đây để hủy đơn cũ và đưa tất cả sản phẩm quay lại Giỏ hàng để bạn cập nhật lại địa chỉ / PTTT ngay lập tức.</small>
                    </div>
                    <form action="{{ route('orders.reorder', $order->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                            <i class="fa-solid fa-rotate-left me-1"></i> Hủy đơn này & Đặt lại
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- PROGRESS TIMELINE BAR -->
        <div class="row g-2 mb-4">
            <div class="col-3 text-center">
                <div class="p-2 bg-primary text-white rounded-3 fw-bold small">1. Đặt Hàng</div>
            </div>
            <div class="col-3 text-center">
                <div class="p-2 {{ ($order->ghn_order_code || in_array($order->status, ['processing', 'shipping', 'completed']) || $order->payment_status === 'paid') ? 'bg-primary text-white' : 'bg-light text-muted border' }} rounded-3 fw-bold small">2. Xử Lý / Đẩy GHN</div>
            </div>
            <div class="col-3 text-center">
                <div class="p-2 {{ ($order->status === 'shipping' || in_array($order->shipping_status, ['ready_to_pick', 'picking', 'storing', 'transporting', 'delivering'])) ? 'bg-primary text-white' : 'bg-light text-muted border' }} rounded-3 fw-bold small">3. Đang Giao</div>
            </div>
            <div class="col-3 text-center">
                <div class="p-2 {{ $order->status === 'completed' ? 'bg-success text-white' : 'bg-light text-muted border' }} rounded-3 fw-bold small">4. Hoàn Tất</div>
            </div>
        </div>

        <!-- HƯỚNG DẪN THANH TOÁN QR NẾU CHỌN CHUYỂN KHOẢN -->
        @if($order->payment_method === 'bank_transfer')
            <div class="p-4 bg-light rounded-4 border text-start mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fa-solid fa-qrcode text-primary me-2"></i> Hướng Dẫn Chuyển Khoản Ngân Hàng Qua Mã QR VietQR</h6>
                <div class="row align-items-center">
                    <div class="col-md-5 text-center mb-3 mb-md-0">
                        @php
                            $qrUrl = "https://img.vietqr.io/image/MB-0369888999-compact2.png?amount={$order->total_amount}&addInfo={$order->order_code}&accountName=PROJECTOR%20SHOP";
                        @endphp
                        <img src="{{ $qrUrl }}" alt="VietQR Payment" class="img-fluid rounded-3 border shadow-sm" style="max-height: 220px;">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Mở ứng dụng Ngân hàng (MB, VCB, Techcombank...) để quét QR</small>
                    </div>
                    <div class="col-md-7 small">
                        <p class="mb-2"><strong>Ngân hàng:</strong> MBBank (Ngân Hàng Quân Đội)</p>
                        <p class="mb-2"><strong>Số tài khoản:</strong> <span class="text-danger fw-bold fs-6">0369 888 999</span></p>
                        <p class="mb-2"><strong>Chủ tài khoản:</strong> CÔNG TY PHÂN PHỐI MÁY CHIẾU VIỆT NAM</p>
                        <p class="mb-2"><strong>Số tiền:</strong> <strong class="text-danger fs-6">{{ number_format($order->total_amount) }} đ</strong></p>
                        <p class="mb-0"><strong>Nội dung ck:</strong> <span class="badge bg-dark fs-6">{{ $order->order_code }}</span></p>
                    </div>
                </div>
            </div>
        @endif

        <!-- BẢNG CHI TIẾT ĐƠN HÀNG HÓA ĐƠN -->
        <div class="text-start mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-receipt text-primary me-2"></i> CHI TIẾT HÓA ĐƠN GIAO HÀNG (KHỚP VỚI GHN)</h6>
            <div class="row g-3 mb-3 small">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <strong class="d-block mb-1 text-dark"><i class="fa-solid fa-user me-1"></i> Người nhận hàng:</strong>
                        <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                        <div>SĐT: <strong class="text-primary">{{ $order->customer_phone }}</strong></div>
                        <div class="text-muted">{{ $order->customer_email }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <strong class="d-block mb-1 text-dark"><i class="fa-solid fa-location-dot me-1"></i> Địa chỉ giao máy chiếu:</strong>
                        <div>{{ $order->shipping_address }}</div>
                        <div class="text-muted mt-1">PTTT: <span class="badge bg-secondary text-uppercase">{{ $order->payment_method }}</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <strong class="d-block mb-1 text-dark"><i class="fa-solid fa-truck-ramp-box me-1"></i> Vận đơn Giao Hàng Nhanh (GHN):</strong>
                        <div>Mã vận đơn: <strong class="text-danger font-monospace fs-6">{{ $order->ghn_order_code ?? ($order->payment_status === 'paid' ? 'Chờ Admin đẩy GHN' : 'Đang khởi tạo') }}</strong></div>
                        <div>Cước GHN: <strong>{{ number_format($order->ghn_total_fee ?? $order->shipping_fee) }} đ</strong></div>
                        <div>Trạng thái: <span class="badge {{ $order->ghn_order_code ? 'bg-success' : ($order->payment_status === 'paid' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $order->shipping_status ?? ($order->payment_status === 'paid' ? 'Đã thanh toán (Chờ xử lý)' : 'Chờ thanh toán') }}</span></div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered align-middle small">
                <thead class="bg-light">
                    <tr>
                        <th>Máy chiếu</th>
                        <th>Đơn giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $item->product_name }}</strong>
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
                            <td colspan="3" class="text-end fw-bold text-success">Giảm giá voucher:</td>
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

        <!-- BUTTON ACTION -->
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-house me-2"></i> Quay Lại Trang Chủ
            </a>

            @auth
                <a href="{{ route('orders.my') }}" class="btn btn-primary-custom rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-box-archive me-2"></i> Quản Lý Đơn Hàng Cá Nhân
                </a>
            @else
                <a href="{{ route('order.lookup') }}?search={{ $order->order_code }}" class="btn btn-primary-custom rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-2"></i> Theo Dõi Tiến Độ Đơn Hàng
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
