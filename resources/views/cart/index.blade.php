@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Giỏ Hàng Của Bạn</h3>
        <p class="text-muted small mb-0">Tích chọn các sản phẩm bạn muốn thanh toán trước khi tiến hành đặt hàng.</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small text-nowrap shadow-sm bg-white">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Trang chủ
    </a>
</div>

@if(count($cart) > 0)
    @php
        $selectedIdsMap = array_flip(array_map('strval', $selectedIds ?? []));
        $allChecked = count($selectedIds ?? []) === count($cart) && count($cart) > 0;
    @endphp

    <div class="row g-4">
        <!-- DANH SÁCH SẢN PHẨM TRONG GIỎ (KÉO DÀI TOÀN BỘ CHIỀU NGANG COL-12) -->
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <!-- THANH THỐNG KÊ SỐ LƯỢNG CHỌN -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap gap-2">
                    <div class="form-check d-flex align-items-center gap-2 m-0">
                        <input type="checkbox" id="selectAllCart" class="form-check-input border-secondary" style="width: 20px; height: 20px; cursor: pointer;" {{ $allChecked ? 'checked' : '' }}>
                        <label for="selectAllCart" class="form-check-label fw-bold text-dark cursor-pointer fs-6 mb-0">
                            Chọn Tất Cả (<span id="totalCartCountText">{{ count($cart) }}</span> sản phẩm)
                        </label>
                    </div>

                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill small fw-bold">
                        <i class="fa-solid fa-square-check me-1"></i> Đã chọn: <span id="selectedCountText">{{ count($selectedIds ?? []) }}</span> / {{ count($cart) }} sản phẩm
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-uppercase small text-muted">
                            <tr>
                                <th style="width: 50px;" class="text-center"></th>
                                <th>Sản phẩm</th>
                                <th class="text-end" style="width: 160px;">Đơn giá</th>
                                <th class="text-center" style="width: 150px;">Số lượng</th>
                                <th class="text-end" style="width: 170px;">Thành tiền</th>
                                <th style="width: 70px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $id => $item)
                                @php
                                    $isSelected = isset($selectedIdsMap[(string)$id]);
                                    $itemSubtotal = $item['price'] * $item['quantity'];
                                @endphp
                                <tr data-id="{{ $id }}" class="{{ $isSelected ? 'table-active bg-light bg-opacity-50' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox" 
                                               name="selected_items[]" 
                                               value="{{ $id }}" 
                                               class="form-check-input cart-item-check border-secondary cursor-pointer"
                                               style="width: 20px; height: 20px;"
                                               data-price="{{ $item['price'] }}"
                                               data-qty="{{ $item['quantity'] }}"
                                               {{ $isSelected ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if(!empty($item['image']))
                                                <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="rounded-3 border flex-shrink-0"
                                                     style="width: 70px; height: 70px; object-fit: contain;">
                                            @else
                                                <div class="bg-light rounded-3 p-2 text-center text-muted flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">No img</div>
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $id) }}" class="fw-bold text-dark text-decoration-none d-block mb-1 fs-6">
                                                    {{ $item['name'] }}
                                                </a>
                                                <span class="badge bg-light text-secondary border small">{{ $item['brand'] ?? 'Chính hãng' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-dark fs-6">
                                        {{ number_format($item['price']) }} đ
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center justify-content-center">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="{{ $item['quantity'] }}" 
                                                   min="1" 
                                                   max="99"
                                                   class="form-control form-control-sm text-center fw-bold item-qty-input" 
                                                   style="width: 75px;"
                                                   onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold text-danger item-subtotal-text fs-6">
                                        {{ number_format($itemSubtotal) }} đ
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm border-0 rounded-circle" title="Xóa sản phẩm">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- NÚT TIẾP TỤC MUA HÀNG -->
                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold small">
                        <i class="fa-solid fa-arrow-left me-2"></i> Tiếp Tục Mua Hàng
                    </a>
                </div>
            </div>
        </div>

        <!-- PHẦN KHU VỰC VOUCHER & THANH TOÁN KÉO DÀI VỪA CHIỀU NGANG -->
        <div class="col-12">
            <div class="row g-4">
                <!-- Ô NHẬP & CHỌN MÃ GIẢM GIÁ (COUPON) -->
                <div class="col-lg-6">
                    <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-ticket text-warning me-2"></i> Mã Giảm Giá / Voucher</h6>
                        </div>

                        @php
                            $appliedCoupon = session()->get('applied_coupon', null);
                        @endphp

                        @if($appliedCoupon)
                            <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3 d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-success me-1 fs-6">{{ $appliedCoupon['code'] }}</span>
                                    <small class="text-success fw-bold d-block mt-1">Đã giảm <span id="couponDiscountText">{{ number_format($appliedCoupon['discount']) }}</span>đ vào đơn hàng</small>
                                </div>
                                <form action="{{ route('checkout.coupon.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">Xóa mã</button>
                                </form>
                            </div>
                        @else
                            <!-- FORM NHẬP MÃ BẰNG TAY -->
                            <form action="{{ route('checkout.coupon') }}" method="POST" class="d-flex gap-2 mb-3">
                                @csrf
                                <input type="text" name="coupon_code" id="couponCodeInput" class="form-control text-uppercase fw-bold" placeholder="Nhập mã ưu đãi (VD: DISCOUNT10)" required>
                                <button type="submit" class="btn btn-dark px-4 fw-bold text-nowrap rounded-3">Áp Dụng</button>
                            </form>
                        @endif

                        <!-- KHU VỰC CHỌN NHANH CÁC MÃ ADMIN ĐÃ SETUP -->
                        @if(isset($availableCoupons) && $availableCoupons->count() > 0)
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <small class="text-muted fw-bold">
                                        <i class="fa-solid fa-gift text-danger me-1"></i> Các mã ưu đãi hiện có:
                                    </small>
                                    <span class="badge bg-light text-muted border">{{ $availableCoupons->count() }} Voucher</span>
                                </div>

                                <div class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                                    @foreach($availableCoupons as $v)
                                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between gap-2">
                                            <div class="overflow-hidden">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-dark font-monospace text-uppercase fs-6">{{ $v->code }}</span>
                                                    <strong class="text-success small">
                                                        {{ $v->type === 'percent' ? 'Giảm ' . $v->value . '%' : 'Giảm ' . number_format($v->value) . ' đ' }}
                                                    </strong>
                                                </div>
                                                @if($v->min_order_amount > 0)
                                                    <div class="text-muted text-truncate mt-1 small">
                                                        Đơn tối thiểu: <strong>{{ number_format($v->min_order_amount) }} đ</strong>
                                                    </div>
                                                @else
                                                    <div class="text-muted text-truncate mt-1 small">Cho mọi đơn hàng</div>
                                                @endif
                                            </div>

                                            @if(!$appliedCoupon || $appliedCoupon['code'] !== $v->code)
                                                <form action="{{ route('checkout.coupon') }}" method="POST" class="m-0 flex-shrink-0">
                                                    @csrf
                                                    <input type="hidden" name="coupon_code" value="{{ $v->code }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                                        Dùng Mã
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-success flex-shrink-0 px-3 py-2 rounded-pill">Đang dùng</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- BẢNG TỔNG TIỀN & ĐẶT HÀNG -->
                <div class="col-lg-6">
                    <div class="bg-white p-4 rounded-4 border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark pb-3 border-bottom mb-3">TÓM TẮT ĐƠN HÀNG CHỌN MUA</h6>

                            @php
                                $discount = $appliedCoupon ? $appliedCoupon['discount'] : 0;
                                $finalTotal = max(0, $selectedTotal - $discount);
                                $hasSelection = count($selectedIds ?? []) > 0;
                            @endphp

                            <div class="d-flex justify-content-between mb-2 text-secondary">
                                <span>Tạm tính (<span id="summarySelectedCount">{{ count($selectedIds ?? []) }}</span> sản phẩm):</span>
                                <strong class="text-dark fs-6" id="summarySubtotalDisplay">{{ number_format($selectedTotal) }} đ</strong>
                            </div>

                            <div id="summaryDiscountRow" class="d-flex justify-content-between mb-2 text-success" style="display: {{ $discount > 0 ? 'flex' : 'none' }};">
                                <span>Giảm giá Voucher:</span>
                                <strong class="fs-6" id="summaryDiscountDisplay">-{{ number_format($discount) }} đ</strong>
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-secondary small">
                                <span>Phí vận chuyển:</span>
                                <span class="text-muted fst-italic">Tính ở bước thanh toán</span>
                            </div>

                            <div class="border-top pt-3 d-flex justify-content-between align-items-baseline mb-4">
                                <span class="fw-bold text-dark fs-5">TỔNG CỘNG:</span>
                                <span class="text-danger fw-extrabold fs-3" id="summaryFinalTotalDisplay">
                                    {{ number_format($finalTotal) }} đ
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('cart.select') }}" method="POST" id="cartCheckoutForm" class="mt-3">
                            @csrf
                            <!-- Dynamic hidden inputs for checked items -->
                            <div id="hiddenSelectedInputsContainer">
                                @foreach($selectedIds ?? [] as $sId)
                                    <input type="hidden" name="selected_items[]" value="{{ $sId }}">
                                @endforeach
                            </div>

                            <button type="submit" id="btnProceedCheckout" class="btn btn-primary-custom w-100 py-3 rounded-3 fw-bold fs-5 shadow-sm" {{ !$hasSelection ? 'disabled' : '' }}>
                                TIẾN HÀNH ĐẶT HÀNG <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                            <div id="noSelectionAlert" class="text-danger small text-center fw-bold mt-2" style="display: {{ !$hasSelection ? 'block' : 'none' }};">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Vui lòng tích chọn ít nhất 1 sản phẩm để thanh toán.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="bg-white p-5 rounded-4 text-center border shadow-sm my-4">
        <i class="fa-solid fa-cart-flatbed fa-4x text-muted mb-3"></i>
        <h4 class="fw-bold text-dark">Giỏ hàng của bạn đang trống!</h4>
        <p class="text-muted small mb-4">Hãy chọn máy chiếu chính hãng ưng ý để bổ sung vào giỏ hàng ngay.</p>
        <a href="{{ route('home') }}" class="btn btn-primary-custom px-4 py-3 rounded-pill fw-bold">
            <i class="fa-solid fa-store me-2"></i> KHÁM PHÁ SẢN PHẨM NGAY
        </a>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCb = document.getElementById('selectAllCart');
    const itemCbs = document.querySelectorAll('.cart-item-check');
    const selectedCountText = document.getElementById('selectedCountText');
    const summarySelectedCount = document.getElementById('summarySelectedCount');
    const summarySubtotalDisplay = document.getElementById('summarySubtotalDisplay');
    const summaryDiscountRow = document.getElementById('summaryDiscountRow');
    const summaryDiscountDisplay = document.getElementById('summaryDiscountDisplay');
    const summaryFinalTotalDisplay = document.getElementById('summaryFinalTotalDisplay');
    const btnProceedCheckout = document.getElementById('btnProceedCheckout');
    const noSelectionAlert = document.getElementById('noSelectionAlert');
    const hiddenInputsContainer = document.getElementById('hiddenSelectedInputsContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function syncSelectionState() {
        const checkedItems = Array.from(itemCbs).filter(cb => cb.checked);
        const selectedIds = checkedItems.map(cb => cb.value);

        // Update select all checkbox state
        if (selectAllCb) {
            selectAllCb.checked = checkedItems.length === itemCbs.length && itemCbs.length > 0;
        }

        // Update counts
        if (selectedCountText) selectedCountText.innerText = selectedIds.length;
        if (summarySelectedCount) summarySelectedCount.innerText = selectedIds.length;

        // Update hidden inputs for form submission fallback
        if (hiddenInputsContainer) {
            hiddenInputsContainer.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_items[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });
        }

        // Update row highlight
        itemCbs.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) {
                    tr.classList.add('table-active', 'bg-light', 'bg-opacity-50');
                } else {
                    tr.classList.remove('table-active', 'bg-light', 'bg-opacity-50');
                }
            }
        });

        // Toggle checkout button
        if (selectedIds.length === 0) {
            if (btnProceedCheckout) btnProceedCheckout.disabled = true;
            if (noSelectionAlert) noSelectionAlert.style.display = 'block';
        } else {
            if (btnProceedCheckout) btnProceedCheckout.disabled = false;
            if (noSelectionAlert) noSelectionAlert.style.display = 'none';
        }

        // Send AJAX to sync session
        fetch("{{ route('cart.select') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ selected_items: selectedIds })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const formattedSubtotal = new Intl.NumberFormat('vi-VN').format(data.selectedSubtotal) + ' đ';
                const formattedFinal = new Intl.NumberFormat('vi-VN').format(data.finalTotal) + ' đ';

                if (summarySubtotalDisplay) summarySubtotalDisplay.innerText = formattedSubtotal;
                if (summaryFinalTotalDisplay) summaryFinalTotalDisplay.innerText = formattedFinal;

                if (data.discount > 0) {
                    const formattedDiscount = '-' + new Intl.NumberFormat('vi-VN').format(data.discount) + ' đ';
                    if (summaryDiscountDisplay) summaryDiscountDisplay.innerText = formattedDiscount;
                    if (summaryDiscountRow) summaryDiscountRow.style.setProperty('display', 'flex', 'important');
                } else {
                    if (summaryDiscountRow) summaryDiscountRow.style.setProperty('display', 'none', 'important');
                }
            }
        })
        .catch(err => console.error('Lỗi cập nhật sản phẩm được chọn:', err));
    }

    if (selectAllCb) {
        selectAllCb.addEventListener('change', function () {
            itemCbs.forEach(cb => cb.checked = this.checked);
            syncSelectionState();
        });
    }

    itemCbs.forEach(cb => {
        cb.addEventListener('change', syncSelectionState);
    });
});
</script>
@endsection