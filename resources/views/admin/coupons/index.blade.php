@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-ticket text-warning me-2"></i>Quản Lý Mã Giảm Giá (Coupon / Voucher)</h3>
        <p class="text-muted small mb-0">Tạo và quản lý mã khuyến mãi cho khách hàng mua máy chiếu.</p>
    </div>
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- TẠO MÃ GIẢM GIÁ MỚI -->
    <div class="col-lg-4">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">+ Tạo Mã Giảm Giá Mới</h6>

            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mã Coupon (Ví dụ: MAYCHIEU10):</label>
                    <input type="text" name="code" class="form-control form-control-sm text-uppercase fw-bold" placeholder="NHẬP MÃ" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Loại giảm giá:</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="fixed">Giảm theo số tiền cố định (VND)</option>
                        <option value="percent">Giảm theo phần trăm (%)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Giá trị giảm:</label>
                    <input type="number" name="value" class="form-control form-control-sm" placeholder="VD: 500000 hoặc 10" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Giá trị đơn hàng tối thiểu (VND):</label>
                    <input type="number" name="min_order_amount" class="form-control form-control-sm" placeholder="VD: 1000000" value="0">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Hạn sử dụng:</label>
                    <input type="date" name="expires_at" class="form-control form-control-sm">
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold btn-sm py-2">Tạo Mã Coupon</button>
            </form>
        </div>
    </div>

    <!-- DANH SÁCH MÃ GIẢM GIÁ -->
    <div class="col-lg-8">
        <form action="{{ route('admin.coupons.bulkAction') }}" method="POST" id="bulkCouponForm">
            @csrf
            <div class="bg-white p-3 rounded-4 border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="selectAll" class="form-check-input my-0" style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="selectAll" class="form-check-label fw-bold small text-dark cursor-pointer">Chọn tất cả</label>
                    <span id="selectedCountBadge" class="badge bg-primary rounded-pill ms-1 d-none">Đã chọn 0 mục</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn XÓA HÀNG LOẠT các mã giảm giá đã chọn?')">
                        <i class="fa-solid fa-trash me-1"></i> Xóa Hàng Loạt
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-4 border shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="ps-3"></th>
                                <th>Mã</th>
                                <th>Loại & Giá trị</th>
                                <th>Đơn tối thiểu</th>
                                <th>Hạn dùng</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $cp)
                                <tr>
                                    <td class="ps-3">
                                        <input type="checkbox" name="ids[]" value="{{ $cp->id }}" class="item-checkbox form-check-input" style="cursor: pointer;">
                                    </td>
                                    <td><span class="badge bg-dark fs-6">{{ $cp->code }}</span></td>
                                    <td class="fw-bold text-success">
                                        {{ $cp->type === 'percent' ? $cp->value . '%' : number_format($cp->value) . ' đ' }}
                                    </td>
                                    <td>{{ number_format($cp->min_order_amount) }} đ</td>
                                    <td>{{ $cp->expires_at ? $cp->expires_at->format('d/m/Y') : 'Không giới hạn' }}</td>
                                    <td>
                                        <span class="badge {{ $cp->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $cp->is_active ? 'Đang hoạt động' : 'Tắt' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <!-- Nút Sửa -->
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 me-1" data-bs-toggle="modal" data-bs-target="#editCouponModal{{ $cp->id }}">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                                        </button>

                                        <!-- Nút Xóa -->
                                        <form action="{{ route('admin.coupons.destroy', $cp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1">
                                                <i class="fa-solid fa-trash me-1"></i> Xóa
                                            </button>
                                        </form>

                                        <!-- MODAL EDIT COUPON #{{ $cp->id }} -->
                                        <div class="modal fade text-start" id="editCouponModal{{ $cp->id }}" tabindex="-1" aria-labelledby="editCouponModalLabel{{ $cp->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                                    <div class="modal-header bg-dark text-white border-0 py-3">
                                                        <h6 class="modal-title fw-bold" id="editCouponModalLabel{{ $cp->id }}">
                                                            <i class="fa-solid fa-pen-to-square text-warning me-2"></i>Chỉnh Sửa Mã Giảm Giá: {{ $cp->code }}
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.coupons.update', $cp->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Mã Coupon (Ví dụ: MAYCHIEU10):</label>
                                                                <input type="text" name="code" class="form-control form-control-sm text-uppercase fw-bold" value="{{ old('code', $cp->code) }}" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Loại giảm giá:</label>
                                                                <select name="type" class="form-select form-select-sm">
                                                                    <option value="fixed" {{ old('type', $cp->type) === 'fixed' ? 'selected' : '' }}>Giảm theo số tiền cố định (VND)</option>
                                                                    <option value="percent" {{ old('type', $cp->type) === 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Giá trị giảm:</label>
                                                                <input type="number" name="value" class="form-control form-control-sm" value="{{ old('value', $cp->value) }}" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Giá trị đơn hàng tối thiểu (VND):</label>
                                                                <input type="number" name="min_order_amount" class="form-control form-control-sm" value="{{ old('min_order_amount', $cp->min_order_amount) }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Hạn sử dụng:</label>
                                                                <input type="date" name="expires_at" class="form-control form-control-sm" value="{{ old('expires_at', $cp->expires_at ? $cp->expires_at->format('Y-m-d') : '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Trạng thái mã:</label>
                                                                <select name="is_active" class="form-select form-select-sm">
                                                                    <option value="1" {{ old('is_active', $cp->is_active) ? 'selected' : '' }}>Đang hoạt động</option>
                                                                    <option value="0" {{ !old('is_active', $cp->is_active) ? 'selected' : '' }}>Tắt (Khóa)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light border-0 py-2">
                                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                                                            <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
                                                                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Cập Nhật
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có mã giảm giá nào.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedBadge = document.getElementById('selectedCountBadge');

    function updateBadge() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            selectedBadge.innerText = `Đã chọn ${checkedCount} mã`;
            selectedBadge.classList.remove('d-none');
        } else {
            selectedBadge.classList.add('d-none');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBadge();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked && selectAll) {
                selectAll.checked = false;
            }
            updateBadge();
        });
    });
});
</script>
@endsection
