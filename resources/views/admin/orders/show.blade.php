@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm mb-2 rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách đơn
        </a>
        <h3 class="fw-bold text-dark mb-0">Chi Tiết Đơn Hàng: <span class="text-primary">{{ $order->order_code }}</span></h3>
    </div>

    <!-- CÁC NÚT THAO TÁC GHN & HỦY ĐƠN -->
    <div class="d-flex align-items-center gap-2">
        @if(!$order->ghn_order_code)
            <form action="{{ route('admin.orders.ghnCreate', $order->id) }}" method="POST" onsubmit="return confirm('Xác nhận tạo vận đơn Giao Hàng Nhanh (GHN) cho đơn hàng này?');">
                @csrf
                <button type="submit" class="btn btn-success fw-bold shadow-sm rounded-pill px-3">
                    <i class="fa-solid fa-truck-fast me-1"></i> Đẩy đơn sang GHN
                </button>
            </form>
        @else
            <form action="{{ route('admin.orders.ghnSync', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-info text-white fw-bold shadow-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> Đồng bộ trạng thái GHN
                </button>
            </form>
        @endif

        <!-- NÚT HỦY ĐƠN ADMIN -->
        @php
            $isDelivering = in_array(strtolower($order->shipping_status), ['picking', 'storing', 'transporting', 'delivering']);
            $isCancelled = $order->status === 'cancelled';
        @endphp

        @if(!$isCancelled)
            @if($isDelivering)
                <button type="button" class="btn btn-outline-danger fw-bold rounded-pill px-3" disabled title="Không thể hủy vì đơn GHN đang trong quá trình giao!">
                    <i class="fa-solid fa-ban me-1"></i> Chặn Hủy (Đang giao GHN)
                </button>
            @else
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng này không? Hệ thống sẽ hủy vận đơn trên GHN nếu có.');">
                    @csrf
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm rounded-pill px-3">
                        <i class="fa-solid fa-ban me-1"></i> Hủy Đơn Hàng
                    </button>
                </form>
            @endif
        @else
            <span class="badge bg-danger p-2 fs-6 rounded-pill"><i class="fa-solid fa-ban me-1"></i> Đơn hàng đã bị hủy</span>
        @endif
    </div>
</div>

<!-- CẢNH BÁO QUY TẮC HỦY GHN NẾU ĐANG GIAO -->
@if($isDelivering)
    <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4">
        <i class="fa-solid fa-triangle-exclamation fa-2x text-warning"></i>
        <div>
            <strong class="d-block text-dark">Lưu ý Quy tắc Hủy đơn GHN:</strong>
            <span>Đơn hàng này đang ở trạng thái vận chuyển từ GHN (<strong>{{ $order->shipping_status }}</strong>). Theo chính sách hệ thống, đơn đang giao <strong>KHÔNG CHO PHÉP HỦY</strong>.</span>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- THÔNG TIN & CẬP NHẬT TRẠNG THÁI -->
    <div class="col-lg-5">
        <!-- THÔNG TIN VẬN CHUYỂN GHN -->
        <div class="bg-white rounded-4 border shadow-sm mb-4 overflow-hidden">
            <!-- Header Card -->
            <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="fa-solid fa-truck-fast fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Vận Chuyển Giao Hàng Nhanh (GHN)</h6>
                        <small class="text-muted" style="font-size: 11px;">Hệ thống vận đơn tự động GHN Express</small>
                    </div>
                </div>
                @if($order->shipping_status)
                    @php
                        $statusBadge = 'bg-secondary';
                        $statusText = $order->shipping_status;
                        switch($order->shipping_status) {
                            case 'ready_to_pick': $statusBadge = 'bg-primary'; $statusText = 'Chờ lấy hàng'; break;
                            case 'picking': $statusBadge = 'bg-info text-dark'; $statusText = 'Đang lấy hàng'; break;
                            case 'storing': $statusBadge = 'bg-secondary'; $statusText = 'Đang lưu kho GHN'; break;
                            case 'transporting': $statusBadge = 'bg-info text-dark'; $statusText = 'Đang trung chuyển'; break;
                            case 'delivering': $statusBadge = 'bg-warning text-dark'; $statusText = 'Đang giao tới khách'; break;
                            case 'delivered': $statusBadge = 'bg-success'; $statusText = 'Đã giao thành công'; break;
                            case 'cancel': $statusBadge = 'bg-danger'; $statusText = 'Đã hủy vận đơn'; break;
                        }
                    @endphp
                    <span class="badge {{ $statusBadge }} px-3 py-2 fw-bold" style="font-size: 13px;">{{ $statusText }}</span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 fw-medium">Chưa tạo vận đơn</span>
                @endif
            </div>

            <!-- Body Details -->
            <div class="p-4">
                <div class="row g-3">
                    <!-- Mã Vận Đơn -->
                    <div class="col-6">
                        <small class="text-muted fw-bold d-block text-uppercase mb-1" style="font-size: 11px;">Mã Vận Đơn GHN</small>
                        @if($order->ghn_order_code)
                            <div class="d-flex align-items-center gap-2">
                                <span class="font-monospace text-primary fw-bold fs-5">{{ $order->ghn_order_code }}</span>
                                <a href="https://5sao.ghn.dev/order?code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" title="Tra cứu trực tiếp trên GHN" style="font-size: 11px;">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Tra cứu
                                </a>
                            </div>
                        @else
                            <span class="text-muted small fst-italic">Chưa khởi tạo</span>
                        @endif
                    </div>

                    <!-- Cước phí GHN -->
                    <div class="col-6">
                        <small class="text-muted fw-bold d-block text-uppercase mb-1" style="font-size: 11px;">Cước Phí GHN Thực Tế</small>
                        @if($order->ghn_total_fee > 0)
                            <strong class="text-danger fs-5">{{ number_format($order->ghn_total_fee) }} đ</strong>
                        @else
                            <span class="text-muted small fst-italic">Tạm tính: {{ number_format($order->shipping_fee) }} đ</span>
                        @endif
                    </div>

                    <!-- Mã Địa Giới GHN -->
                    <div class="col-12 border-top pt-3">
                        <small class="text-muted fw-bold d-block text-uppercase mb-2" style="font-size: 11px;">Mã Hành Chính GHN (Khu Vực Giao)</small>
                        <div class="d-flex align-items-center gap-2 flex-wrap small">
                            <span class="badge bg-light text-dark border px-3 py-2">
                                <i class="fa-solid fa-building text-primary me-1"></i> Mã Quận/Huyện: <strong class="text-primary font-monospace ms-1">{{ $order->to_district_id ?? 'N/A' }}</strong>
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                <i class="fa-solid fa-location-dot text-primary me-1"></i> Mã Phường/Xã: <strong class="text-primary font-monospace ms-1">{{ $order->to_ward_code ?? 'N/A' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CẬP NHẬT TRẠNG THÁI HỆ THỐNG -->
        <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG HỆ THỐNG</h6>

            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Trạng thái xử lý & giao hàng:</label>
                    <select name="status" class="form-select form-select-sm rounded-3">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý đóng gói (Processing)</option>
                        <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao hàng (Shipping)</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Đã hoàn tất (Completed)</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }} {{ $isDelivering ? 'disabled' : '' }}>
                            Đã hủy (Cancelled) {{ $isDelivering ? '- [Chặn: Đơn đang giao GHN]' : '' }}
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Trạng thái vận chuyển GHN:</label>
                    <select name="shipping_status" class="form-select form-select-sm rounded-3">
                        <option value="">-- Chưa khởi tạo vận đơn --</option>
                        <option value="ready_to_pick" {{ $order->shipping_status === 'ready_to_pick' ? 'selected' : '' }}>Chờ lấy hàng (ready_to_pick)</option>
                        <option value="picking" {{ $order->shipping_status === 'picking' ? 'selected' : '' }}>Đang lấy hàng (picking)</option>
                        <option value="storing" {{ $order->shipping_status === 'storing' ? 'selected' : '' }}>Đang lưu kho GHN (storing)</option>
                        <option value="transporting" {{ $order->shipping_status === 'transporting' ? 'selected' : '' }}>Đang trung chuyển (transporting)</option>
                        <option value="delivering" {{ $order->shipping_status === 'delivering' ? 'selected' : '' }}>Đang giao tới khách (delivering)</option>
                        <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>Đã giao thành công (delivered)</option>
                        <option value="cancel" {{ $order->shipping_status === 'cancel' ? 'selected' : '' }}>Đã hủy vận đơn (cancel)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Trạng thái thanh toán:</label>
                    <select name="payment_status" class="form-select form-select-sm rounded-3">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chưa thanh toán (Pending)</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại (Failed)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold btn-sm py-2 rounded-3">Lưu Thay Đổi</button>
            </form>
        </div>

        <!-- THÔNG TIN KHÁCH HÀNG -->
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="fa-solid fa-user me-2 text-primary"></i>THÔNG TIN KHÁCH HÀNG & GIAO HÀNG</h6>
            <div class="small lh-lg">
                <div><strong>Họ tên:</strong> {{ $order->customer_name }}</div>
                <div><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                <div><strong>Email:</strong> {{ $order->customer_email }}</div>
                <div><strong>Địa chỉ giao:</strong> {{ $order->shipping_address }}</div>
                <div><strong>Phương thức thanh toán:</strong> <span class="badge bg-secondary text-uppercase">{{ $order->payment_method }}</span></div>
                <div><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</div>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH SẢN PHẨM HÓA ĐƠN -->
    <div class="col-lg-7">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>DANH SÁCH MÁY CHIẾU TRONG ĐƠN</h6>

            <table class="table align-middle small mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>SL</th>
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
                            <td colspan="3" class="text-end fw-bold text-success">Giảm giá Coupon:</td>
                            <td class="text-success fw-bold">-{{ number_format($order->discount_amount) }} đ</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-end fw-bold text-primary">Phí giao hàng GHN:</td>
                        <td class="text-primary fw-bold">{{ number_format($order->shipping_fee) }} đ</td>
                    </tr>
                    <tr class="fs-6 border-top">
                        <td colspan="3" class="text-end fw-bold text-dark">TỔNG CỘNG THANH TOÁN:</td>
                        <td class="fw-bold text-danger fs-5">{{ number_format($order->total_amount) }} đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
