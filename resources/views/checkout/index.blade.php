@extends('layouts.app')

@section('content')
<!-- Select2 & Bootstrap 5 Theme CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container--bootstrap-5 .select2-selection {
    font-size: 13px;
    border-radius: 8px;
    padding: 4px 8px;
    min-height: 38px;
}
.select2-container--bootstrap-5 .select2-dropdown {
    font-size: 13px;
    border-radius: 12px;
}
.payment-card {
    transition: all 0.2s ease;
    cursor: pointer;
    background-color: #fff;
    border: 1px solid #e9ecef !important;
}
.payment-card:hover {
    border-color: #0d6efd !important;
    background-color: #f8fafc;
}
.payment-card:has(input:checked) {
    border-color: #0d6efd !important;
    background-color: #f0f7ff !important;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.08);
}
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-credit-card text-primary me-2"></i> Đặt Hàng & Thanh Toán</h3>
        <p class="text-muted small mb-0">Vui lòng kiểm tra lại thông tin nhận hàng và chọn phương thức thanh toán phù hợp.</p>
    </div>
    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small text-nowrap shadow-sm bg-white">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Giỏ hàng
    </a>
</div>

@php
    $prefill = session()->get('checkout_prefill', []);
@endphp

<form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
    @csrf
    <input type="hidden" name="shipping_fee" id="inputShippingFee" value="{{ $shippingFee }}">

    <div class="row g-4">
        <!-- CỘT BÊN TRÁI: THÔNG TIN GIAO HÀNG & THANH TOÁN -->
        <div class="col-lg-7">
            <!-- THÔNG TIN NGƯỜI NHẬN -->
            <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="fa-solid fa-truck-fast text-primary me-2"></i> Thông Tin Giao Hàng</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Họ và tên nhận hàng <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="customer_name" 
                               class="form-control @error('customer_name') is-invalid @enderror" 
                               placeholder="Ví dụ: Nguyễn Văn Nam" 
                               value="{{ old('customer_name', $prefill['customer_name'] ?? (Auth::check() ? Auth::user()->name : '')) }}" 
                               required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="customer_phone" 
                               class="form-control @error('customer_phone') is-invalid @enderror" 
                               placeholder="Ví dụ: 0988776655" 
                               value="{{ old('customer_phone', $prefill['customer_phone'] ?? '') }}" 
                               maxlength="10"
                               minlength="10"
                               pattern="^(03|05|07|08|09)[0-9]{8}$"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                               required>
                        @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-dark">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="customer_email" 
                               class="form-control @error('customer_email') is-invalid @enderror" 
                               placeholder="Ví dụ: khachhang@gmail.com (Dùng nhận thông báo đơn hàng)" 
                               value="{{ old('customer_email', $prefill['customer_email'] ?? (Auth::check() ? Auth::user()->email : '')) }}" 
                               required>
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- CHỌN / TÌM KIẾM ĐỊA CHỈ HÀNH CHÍNH TÍNH PHÍ VẬN CHUYỂN -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                        <select id="provinceSelect" class="form-select form-select-sm searchable-select" required>
                            <option value="">-- Chọn Tỉnh/Thành --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Quận / Huyện <span class="text-danger">*</span></label>
                        <select id="districtSelect" name="to_district_id" class="form-select form-select-sm searchable-select" required disabled>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Phường / Xã <span class="text-danger">*</span></label>
                        <select id="wardSelect" name="to_ward_code" class="form-select form-select-sm searchable-select" required disabled>
                            <option value="">-- Chọn Phường/Xã --</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-dark">Địa chỉ giao hàng chi tiết (Số nhà, Tên đường) <span class="text-danger">*</span></label>
                        <textarea name="shipping_address" 
                                  id="shippingAddressTextarea"
                                  class="form-control @error('shipping_address') is-invalid @enderror" 
                                  rows="2" 
                                  placeholder="Ví dụ: 102 Nguyễn Trãi..." 
                                  required>{{ old('shipping_address', $prefill['shipping_address'] ?? '') }}</textarea>
                        @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-dark">Ghi chú đơn hàng (Tùy chọn)</label>
                        <input type="text" name="note" class="form-control" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao 15 phút..." value="{{ old('note', $prefill['note'] ?? '') }}">
                    </div>
                </div>
            </div>

            <!-- PHƯƠNG THỨC THANH TOÁN -->
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="fa-solid fa-credit-card text-primary me-2"></i> Chọn Phương Thức Thanh Toán</h6>

                <div class="d-flex flex-column gap-3">
                    <!-- 1. COD -->
                    <label class="payment-card border p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="cod" class="form-check-input mt-0" checked>
                            <div>
                                <strong class="text-dark d-block">Thanh toán khi nhận hàng (COD)</strong>
                                <small class="text-muted">Nhận sản phẩm và kiểm tra trước khi thanh toán cho nhân viên giao hàng.</small>
                            </div>
                        </div>
                        <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-money-bill-wave text-success fs-5"></i>
                        </div>
                    </label>

                    <!-- 2. BANK TRANSFER QR -->
                    <label class="payment-card border p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="bank_transfer" class="form-check-input mt-0">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="text-dark">Chuyển khoản Ngân hàng (VietQR Tự Động)</strong>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium" style="font-size: 11px;">Khuyên dùng</span>
                                </div>
                                <small class="text-muted">Quét mã QR tự động điền số tiền và nội dung chuyển khoản nhanh chóng.</small>
                            </div>
                        </div>
                        <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-qrcode text-primary fs-5"></i>
                        </div>
                    </label>

                    <!-- 3. VÍ MOMO QR -->
                    <label class="payment-card border p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="momo" class="form-check-input mt-0">
                            <div>
                                <strong class="text-dark d-block">Ví Điện Tử MoMo (Quét QR / App)</strong>
                                <small class="text-muted">Thanh toán nhanh chóng và an toàn bằng ứng dụng MoMo.</small>
                            </div>
                        </div>
                        <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-wallet text-danger fs-5"></i>
                        </div>
                    </label>

                    <!-- 4. THẺ ATM NỘI ĐỊA MOMO -->
                    <label class="payment-card border p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="momo_atm" class="form-check-input mt-0">
                            <div>
                                <strong class="text-dark d-block">Thẻ ATM Nội Địa / Internet Banking</strong>
                                <small class="text-muted">Thanh toán bằng thẻ ATM các ngân hàng nội địa qua cổng MoMo.</small>
                            </div>
                        </div>
                        <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-credit-card text-info fs-5"></i>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- CỘT BÊN PHẢI: TÓM TẮT SẢN PHẨM & ĐẶT HÀNG -->
        <div class="col-lg-5">
            <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px;">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">TÓM TẮT ĐƠN HÀNG ({{ count($cart) }} sản phẩm)</h6>

                <!-- DANH SÁCH SP RÚT GỌN -->
                <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                    @foreach($cart as $item)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                @if(!empty($item['image']))
                                    <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="rounded-2 border" 
                                         style="width: 45px; height: 45px; object-fit: contain;">
                                @endif
                                <div>
                                    <div class="fw-bold small text-dark text-truncate" style="max-width: 200px;">{{ $item['name'] }}</div>
                                    <small class="text-muted">x {{ $item['quantity'] }}</small>
                                </div>
                            </div>
                            <strong class="small text-danger">{{ number_format($item['price'] * $item['quantity']) }} đ</strong>
                        </div>
                    @endforeach
                </div>

                <!-- KHU VỰC NHẬP BẰNG TAY VÀ CHỌN VOUCHER ADMIN -->
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark small"><i class="fa-solid fa-ticket text-warning me-1"></i> Mã Giảm Giá / Voucher:</strong>
                    </div>

                    @if($appliedCoupon)
                        <div class="d-flex align-items-center justify-content-between bg-success bg-opacity-10 border border-success p-2 rounded-3 small">
                            <div>
                                <span class="badge bg-success font-monospace me-1">{{ $appliedCoupon['code'] }}</span>
                                <span class="text-success fw-bold">Đã giảm -{{ number_format($appliedCoupon['discount']) }}đ</span>
                            </div>
                            <button type="submit" form="removeCouponFormCheckout" class="btn btn-sm text-danger p-0 border-0 fw-bold ms-2" title="Bỏ áp dụng mã">
                                <i class="fa-solid fa-xmark"></i> Xóa
                            </button>
                        </div>
                    @else
                        <!-- 1. Ô NHẬP MÃ BẰNG TAY -->
                        <div class="d-flex gap-1 mb-2">
                            <input type="text" form="applyCouponFormCheckout" name="coupon_code" class="form-control form-control-sm text-uppercase fw-bold" placeholder="Nhập mã (VD: DISCOUNT10)">
                            <button type="submit" form="applyCouponFormCheckout" class="btn btn-dark btn-sm px-3 fw-bold text-nowrap">Áp dụng</button>
                        </div>
                    @endif

                    <!-- 2. DANH SÁCH CHỌN MÃ ADMIN ĐÃ SETUP -->
                    @if(isset($availableCoupons) && $availableCoupons->count() > 0)
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px;">Hoặc chọn mã ưu đãi từ cửa hàng:</small>
                            <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                @foreach($availableCoupons as $v)
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-white border" style="font-size: 11.5px;">
                                        <div class="overflow-hidden me-1">
                                            <span class="badge bg-dark font-monospace text-uppercase" style="font-size: 10px;">{{ $v->code }}</span>
                                            <strong class="text-success ms-1">{{ $v->type === 'percent' ? 'Giảm ' . $v->value . '%' : 'Giảm ' . number_format($v->value) . ' đ' }}</strong>
                                            @if($v->min_order_amount > 0)
                                                <small class="text-muted d-block text-truncate" style="font-size: 10px;">Đơn tối thiểu: {{ number_format($v->min_order_amount) }} đ</small>
                                            @endif
                                        </div>
                                        @if(!$appliedCoupon || $appliedCoupon['code'] !== $v->code)
                                            <button type="submit" form="applyCouponForm_{{ $v->id }}" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold flex-shrink-0" style="font-size: 10.5px;">
                                                Chọn
                                            </button>
                                        @else
                                            <span class="badge bg-success flex-shrink-0" style="font-size: 9.5px;">Đã chọn</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- TÍNH TOÁN TIỀN -->
                <div class="d-flex justify-content-between mb-2 text-secondary small">
                    <span>Tạm tính:</span>
                    <strong class="text-dark" id="subtotalDisplay" data-subtotal="{{ $subtotal }}">{{ number_format($subtotal) }} đ</strong>
                </div>

                @if($discountAmount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success small">
                        <span>Giảm giá Voucher ({{ $appliedCoupon['code'] }}):</span>
                        <strong data-discount="{{ $discountAmount }}">-{{ number_format($discountAmount) }} đ</strong>
                    </div>
                @endif

                <div class="d-flex justify-content-between mb-3 text-secondary small">
                    <span>Phí vận chuyển:</span>
                    <strong class="text-primary" id="shippingFeeDisplay">
                        {{ $shippingFee > 0 ? number_format($shippingFee) . ' đ' : 'Vui lòng chọn địa chỉ' }}
                    </strong>
                </div>

                <div class="border-top pt-3 d-flex justify-content-between align-items-baseline mb-4">
                    <span class="fw-bold text-dark">TỔNG THANH TOÁN:</span>
                    <span class="text-danger fw-extrabold fs-4" id="totalAmountDisplay">{{ number_format($total) }} đ</span>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-3 fw-bold fs-6 shadow-sm">
                    <i class="fa-solid fa-check-circle me-2"></i> XÁC NHẬN ĐẶT HÀNG
                </button>
                <small class="text-muted text-center d-block mt-2" style="font-size: 11px;"><i class="fa-solid fa-shield-halved text-success me-1"></i> Thông tin thanh toán của bạn được bảo mật tuyệt đối.</small>
            </div>
        </div>
    </div>
</form>

<!-- AUXILIARY FORMS FOR VOUCHER SELECTION IN CHECKOUT -->
<form action="{{ route('checkout.coupon') }}" method="POST" id="applyCouponFormCheckout">@csrf</form>
<form action="{{ route('checkout.coupon.remove') }}" method="POST" id="removeCouponFormCheckout">@csrf @method('DELETE')</form>
@if(isset($availableCoupons))
    @foreach($availableCoupons as $v)
        <form action="{{ route('checkout.coupon') }}" method="POST" id="applyCouponForm_{{ $v->id }}">
            @csrf
            <input type="hidden" name="coupon_code" value="{{ $v->code }}">
        </form>
    @endforeach
@endif

<script>
$(document).ready(function() {
    $('#provinceSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Chọn hoặc gõ tìm Tỉnh/Thành --'
    });

    $('#districtSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Chọn hoặc gõ tìm Quận/Huyện --'
    });

    $('#wardSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Chọn hoặc gõ tìm Phường/Xã --'
    });

    const provinceSelect = $('#provinceSelect');
    const districtSelect = $('#districtSelect');
    const wardSelect = $('#wardSelect');
    const shippingFeeDisplay = document.getElementById('shippingFeeDisplay');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');
    const inputShippingFee = document.getElementById('inputShippingFee');
    const subtotal = parseFloat(document.getElementById('subtotalDisplay').getAttribute('data-subtotal')) || 0;
    const discount = parseFloat("{{ $discountAmount }}") || 0;

    // Load tất cả Tỉnh/Thành Việt Nam thực từ API GHN
    fetch("{{ route('locations.provinces') }}")
        .then(res => res.json())
        .then(res => {
            const provinces = res.data || [];
            provinceSelect.empty().append('<option value="">-- Chọn hoặc gõ tìm Tỉnh/Thành --</option>');
            provinces.forEach(p => {
                const opt = new Option(p.ProvinceName, p.ProvinceID, false, false);
                provinceSelect.append(opt);
            });
            provinceSelect.trigger('change.select2');
        })
        .catch(err => console.error('Lỗi tải Tỉnh/Thành:', err));

    // Khi chọn Tỉnh/Thành -> Load Quận/Huyện
    provinceSelect.on('change', function() {
        const provId = $(this).val();
        districtSelect.empty().append('<option value="">-- Chọn hoặc gõ tìm Quận/Huyện --</option>').prop('disabled', true);
        wardSelect.empty().append('<option value="">-- Chọn hoặc gõ tìm Phường/Xã --</option>').prop('disabled', true);
        districtSelect.trigger('change.select2');
        wardSelect.trigger('change.select2');

        if (!provId) return;

        fetch(`/locations/districts/${provId}`)
            .then(res => res.json())
            .then(res => {
                const districts = res.data || [];
                districts.forEach(d => {
                    const opt = new Option(d.DistrictName, d.DistrictID, false, false);
                    districtSelect.append(opt);
                });
                districtSelect.prop('disabled', false).trigger('change.select2');
            });
    });

    // Khi chọn Quận/Huyện -> Load Phường/Xã
    districtSelect.on('change', function() {
        const distId = $(this).val();
        wardSelect.empty().append('<option value="">-- Chọn hoặc gõ tìm Phường/Xã --</option>').prop('disabled', true);
        wardSelect.trigger('change.select2');

        if (!distId) return;

        fetch(`/locations/wards/${distId}`)
            .then(res => res.json())
            .then(res => {
                const wards = res.data || [];
                wards.forEach(w => {
                    const opt = new Option(w.WardName, w.WardCode, false, false);
                    wardSelect.append(opt);
                });
                wardSelect.prop('disabled', false).trigger('change.select2');
            });
    });

    // Khi chọn Phường/Xã -> Tính Phí Vận Chuyển GHN
    wardSelect.on('change', function() {
        const distId = districtSelect.val();
        const wardCode = $(this).val();

        if (!distId || !wardCode) return;

        shippingFeeDisplay.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tính phí...';

        fetch("{{ route('locations.fee') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                to_district_id: distId,
                to_ward_code: wardCode
            })
        })
        .then(res => res.json())
        .then(res => {
            let fee = 30000;
            if (res.code === 200 && res.data && res.data.total) {
                fee = res.data.total;
            }
            inputShippingFee.value = fee;
            shippingFeeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(fee) + ' đ';

            const total = Math.max(0, subtotal - discount + fee);
            totalAmountDisplay.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
        })
        .catch(err => {
            console.error('Lỗi tính phí GHN:', err);
            shippingFeeDisplay.textContent = '30,000 đ';
            inputShippingFee.value = 30000;
        });
    });

    // Tự động ghép nối Tỉnh/Huyện/Xã vào chuỗi địa chỉ giao hàng trước khi submit
    document.getElementById('checkoutForm').addEventListener('submit', function() {
        const provText = $('#provinceSelect option:selected').text();
        const distText = $('#districtSelect option:selected').text();
        const wardText = $('#wardSelect option:selected').text();
        const addrTextarea = document.getElementById('shippingAddressTextarea');

        if (provText && distText && wardText && !provText.includes('--')) {
            const fullLocationStr = `${wardText}, ${distText}, ${provText}`;
            if (!addrTextarea.value.includes(provText)) {
                addrTextarea.value = addrTextarea.value.trim() + ' (' + fullLocationStr + ')';
            }
        }
    });
});
</script>
@endsection
