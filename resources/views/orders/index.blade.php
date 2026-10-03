@extends('layouts.app')

@section('content')
<style>
    /* Modern E-Commerce My Orders Custom Styling */
    .order-nav-pills .nav-link {
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 20px;
        border-radius: 12px;
        transition: all 0.25s ease;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .order-nav-pills .nav-link:hover {
        color: #0284c7;
        background-color: #f0f9ff;
        border-color: #bae6fd;
    }
    .order-nav-pills .nav-link.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #0284c7, #2563eb) !important;
        border-color: transparent !important;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .order-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .order-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
    }
    .order-card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 20px;
    }
    .order-card-body {
        padding: 16px 20px;
    }
    .order-card-footer {
        background-color: #ffffff;
        border-top: 1px solid #f1f5f9;
        padding: 16px 20px;
    }

    .product-thumb {
        width: 64px;
        height: 64px;
        object-fit: contain;
        border-radius: 10px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
</style>

<div class="container py-2">
    <!-- HEADER TRANG -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-box-archive text-primary"></i> Đơn Hàng Của Tôi
            </h3>
            <p class="text-muted small mb-0">Quản lý và theo dõi quá trình xử lý, giao nhận các đơn hàng máy chiếu của bạn.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small text-nowrap shadow-sm bg-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    <!-- THANH LỌC TRẠNG THÁI ĐƠN HÀNG (SHOPEE / TIKI STANDARD TAB FILTER) -->
    <div class="nav order-nav-pills mb-4 gap-2 flex-wrap">
        <a href="{{ route('orders.my') }}" 
           class="nav-link {{ !request('status') ? 'active' : '' }}">
            Tất cả đơn <span class="badge rounded-pill {{ !request('status') ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['all'] ?? 0 }}</span>
        </a>
        <a href="{{ route('orders.my', ['status' => 'pending']) }}" 
           class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}">
            <i class="fa-regular fa-clock me-1"></i> Chờ xác nhận <span class="badge rounded-pill {{ request('status') == 'pending' ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['pending'] ?? 0 }}</span>
        </a>
        <a href="{{ route('orders.my', ['status' => 'processing']) }}" 
           class="nav-link {{ request('status') == 'processing' ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-packing me-1"></i> Đang chuẩn bị hàng <span class="badge rounded-pill {{ request('status') == 'processing' ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['processing'] ?? 0 }}</span>
        </a>
        <a href="{{ route('orders.my', ['status' => 'shipping']) }}" 
           class="nav-link {{ request('status') == 'shipping' ? 'active' : '' }}">
            <i class="fa-solid fa-truck-fast me-1"></i> Đang giao hàng <span class="badge rounded-pill {{ request('status') == 'shipping' ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['shipping'] ?? 0 }}</span>
        </a>
        <a href="{{ route('orders.my', ['status' => 'completed']) }}" 
           class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}">
            <i class="fa-solid fa-circle-check me-1"></i> Đã hoàn tất <span class="badge rounded-pill {{ request('status') == 'completed' ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['completed'] ?? 0 }}</span>
        </a>
        <a href="{{ route('orders.my', ['status' => 'cancelled']) }}" 
           class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}">
            <i class="fa-solid fa-ban me-1"></i> Đã hủy <span class="badge rounded-pill {{ request('status') == 'cancelled' ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['cancelled'] ?? 0 }}</span>
        </a>
    </div>

    <!-- DANH SÁCH THẺ ĐƠN HÀNG (E-COMMERCE ORDER CARDS) -->
    <div class="d-flex flex-column gap-3 mb-4">
        @forelse($orders as $ord)
            <div class="order-card">
                <!-- 1. CỘT TIÊU ĐỀ THẺ ĐƠN HÀNG (HEADER CARD) -->
                <div class="order-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-monospace fw-bold" style="font-size: 13.5px;">
                            <i class="fa-solid fa-receipt me-1"></i> #{{ $ord->order_code }}
                        </span>
                        <span class="text-muted small">
                            <i class="fa-regular fa-calendar-check me-1"></i> Ngày đặt: <strong>{{ $ord->created_at->format('d/m/Y - H:i') }}</strong>
                        </span>
                    </div>

                    <!-- TRẠNG THÁI THANH TOÁN & VẬN CHUYỂN -->
                    <div class="d-flex align-items-center gap-2">
                        <!-- Trạng thái Thanh toán -->
                        @if($ord->payment_status === 'paid')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill small fw-bold">
                                <i class="fa-solid fa-check me-1"></i> Đã thanh toán
                            </span>
                        @elseif($ord->payment_status === 'failed')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill small fw-bold">
                                <i class="fa-solid fa-xmark me-1"></i> Thanh toán thất bại
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50 px-3 py-1 rounded-pill small fw-bold">
                                <i class="fa-solid fa-clock me-1"></i> Chưa thanh toán
                            </span>
                        @endif

                        <!-- Trạng thái Giao hàng -->
                        @switch($ord->status)
                            @case('pending')
                                <span class="badge bg-secondary px-3 py-1 rounded-pill small fw-bold text-uppercase">Chờ xác nhận</span>
                                @break
                            @case('processing')
                                <span class="badge bg-info text-dark px-3 py-1 rounded-pill small fw-bold text-uppercase">Đang xử lý</span>
                                @break
                            @case('shipping')
                                <span class="badge bg-primary px-3 py-1 rounded-pill small fw-bold text-uppercase"><i class="fa-solid fa-truck-fast me-1"></i> Đang giao hàng</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success px-3 py-1 rounded-pill small fw-bold text-uppercase"><i class="fa-solid fa-circle-check me-1"></i> Hoàn thành</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger px-3 py-1 rounded-pill small fw-bold text-uppercase">Đã hủy</span>
                                @break
                        @endswitch
                    </div>
                </div>

                <!-- 2. NỘI DUNG SẢN PHẨM TRONG ĐƠN (BODY CARD) -->
                <div class="order-card-body">
                    <div class="d-flex flex-column gap-3">
                        @foreach($ord->items as $item)
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <img src="{{ $item->product_image ? (Str::startsWith($item->product_image, 'http') ? $item->product_image : asset('storage/' . $item->product_image)) : 'https://placehold.co/80x80?text=Projector' }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="product-thumb">
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 14.5px;">{{ $item->product_name }}</h6>
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <span>Đơn giá: <strong class="text-dark">{{ number_format($item->price) }} đ</strong></span>
                                            <span>•</span>
                                            <span class="badge bg-light text-dark border rounded-pill px-2">Số lượng: x{{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="fw-bold text-dark" style="font-size: 15px;">{{ number_format($item->subtotal) }} đ</span>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-0 border-light opacity-50">
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- 3. CHÂN THẺ ĐƠN HÀNG: TỔNG TIỀN VÀ NÚT HÀNH ĐỘNG (FOOTER CARD) -->
                <div class="order-card-footer d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 11px;">Tổng tiền đơn hàng:</small>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fw-extrabold text-danger fs-4">{{ number_format($ord->total_amount) }} đ</span>
                            <small class="text-muted">({{ strtoupper($ord->payment_method) }})</small>
                        </div>
                    </div>

                    <!-- NHÓM NÚT THAO TÁC SẮP XẾP CHUẨN SÀN THƯƠNG MẠI ĐIỆN TỬ -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- NÚT THANH TOÁN LẠI NẾU CHƯA THANH TOÁN -->
                        @if($ord->payment_status !== 'paid' && $ord->status !== 'cancelled')
                            <div class="btn-group" role="group">
                                <a href="{{ route('momo.pay-again', ['orderId' => $ord->id, 'type' => 'payWithATM']) }}" 
                                   class="btn btn-danger btn-sm rounded-pill-start px-3 fw-bold shadow-sm" 
                                   title="Thanh toán MoMo ATM">
                                    <i class="fa-solid fa-credit-card me-1"></i> Thanh Toán Thẻ ATM
                                </a>
                                <a href="{{ route('momo.pay-again', ['orderId' => $ord->id, 'type' => 'captureWallet']) }}" 
                                   class="btn btn-outline-danger btn-sm rounded-pill-end px-3 fw-bold" 
                                   title="Thanh toán Ví MoMo QR">
                                    <i class="fa-solid fa-qrcode me-1"></i> QR
                                </a>
                            </div>
                        @endif

                        <!-- NÚT XEM CHI TIẾT HÓA ĐƠN -->
                        <a href="{{ route('orders.show', $ord->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                            <i class="fa-solid fa-file-invoice me-1"></i> Xem Chi Tiết
                        </a>

                        <!-- NÚT SỬA ĐƠN HÀNG (Nếu chưa xác nhận và chưa thanh toán) -->
                        @if($ord->status === 'pending' && $ord->payment_status !== 'paid')
                            <form action="{{ route('orders.reorder', $ord->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm rounded-pill text-dark fw-bold px-3 shadow-sm" title="Hủy đơn cũ & đưa sản phẩm vào giỏ để sửa lại địa chỉ / PTTT">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Sửa Đơn
                                </button>
                            </form>
                        @endif

                        <!-- NÚT HỦY ĐƠN HÀNG (Nếu còn ở trạng thái pending) -->
                        @if($ord->status === 'pending')
                            <form action="{{ route('orders.cancel', $ord->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $ord->order_code }} không?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="fa-solid fa-xmark me-1"></i> Hủy Đơn
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-5 rounded-4 border text-center my-3 shadow-sm">
                <div class="mb-3">
                    <i class="fa-solid fa-box-open fa-4x text-muted opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark">Chưa có đơn hàng nào</h5>
                <p class="text-muted small mb-3">Bạn chưa thực hiện giao dịch mua máy chiếu nào trong mục này.</p>
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-cart-shopping me-1"></i> Khám phá máy chiếu ngay
                </a>
            </div>
        @endforelse
    </div>

    <!-- PAGING -->
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
