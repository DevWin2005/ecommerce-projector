@extends('layouts.app')

@section('content')
<div class="max-w-700 mx-auto my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small text-nowrap shadow-sm bg-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Trang chủ
        </a>
    </div>
    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm mb-4 text-center">
        <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3">
            <i class="fa-solid fa-magnifying-glass-location fa-2x"></i>
        </span>
        <h3 class="fw-bold text-dark mb-2">Tra Cứu Tiến Độ Đơn Hàng</h3>
        <p class="text-muted small mb-4">Nhập mã đơn hàng và số điện thoại mua hàng để xem tình trạng giao máy chiếu.</p>

        <form action="{{ route('order.lookup.post') }}" method="POST" class="text-start">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-dark">Mã Đơn Hàng <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="order_code" 
                           class="form-control form-control-lg text-uppercase fw-bold" 
                           placeholder="Ví dụ: ORD-982341" 
                           value="{{ old('order_code', request('order_code')) }}" 
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-dark">Số Điện Thoại Đặt Hàng <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="phone" 
                           class="form-control form-control-lg fw-bold" 
                           placeholder="Ví dụ: 0988776655" 
                           value="{{ old('phone', request('phone')) }}" 
                           required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-3 fw-bold fs-6 shadow-sm">
                        <i class="fa-solid fa-search me-2"></i> KẾT QUẢ TRA CỨU
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- NẾU CÓ KẾT QUẢ TRA CỨU ĐƠN HÀNG -->
    @if(isset($order))
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Mã Đơn: <span class="text-primary">{{ $order->order_code }}</span></h5>
                    <span class="text-muted small">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <span class="badge bg-primary fs-6 px-3 py-2 text-uppercase">{{ $order->status }}</span>
            </div>

            <div class="row g-3 mb-4 small">
                <div class="col-md-6">
                    <strong>Khách hàng:</strong> {{ $order->customer_name }} ({{ $order->customer_phone }})<br>
                    <strong>Địa chỉ:</strong> {{ $order->shipping_address }}
                </div>
                <div class="col-md-6 text-md-end">
                    <strong>PTTT:</strong> {{ strtoupper($order->payment_method) }}<br>
                    <strong>Thanh toán:</strong> 
                    <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                </div>
            </div>

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
                            <td>{{ $item->product_name }}</td>
                            <td>{{ number_format($item->price) }}đ</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="fw-bold text-danger">{{ number_format($item->subtotal) }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Tổng thanh toán:</td>
                        <td class="fw-bold text-danger fs-6">{{ number_format($order->total_amount) }}đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
@endsection
