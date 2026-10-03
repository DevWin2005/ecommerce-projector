@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-boxes-packing text-primary me-2"></i>Quản Lý Đơn Hàng Admin</h3>
        <p class="text-muted small mb-0">Quản lý, đồng bộ trạng thái Giao Hàng Nhanh (GHN) và lọc đơn hàng chuyên nghiệp.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3">
            <i class="fa-solid fa-chart-pie me-1"></i> Báo Cáo Tài Chính
        </a>
    </div>
</div>

<!-- TAB FILTER TRẠNG THÁI NHANH -->
<div class="nav nav-pills bg-white p-2 rounded-4 border shadow-sm mb-4 gap-1 flex-wrap">
    <a href="{{ route('admin.orders.index') }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ !request('status') ? 'active bg-primary text-white' : 'text-dark' }}">
        Tất cả đơn <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'pending' ? 'active bg-warning text-dark' : 'text-dark' }}">
        <i class="fa-regular fa-clock me-1"></i> Chờ xác nhận <span class="badge bg-light text-dark border ms-1">{{ $counts['pending'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'processing'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'processing' ? 'active bg-info text-white' : 'text-dark' }}">
        <i class="fa-solid fa-box-open me-1"></i> Đang xử lý <span class="badge bg-light text-dark border ms-1">{{ $counts['processing'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'shipping'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'shipping' ? 'active bg-primary text-white' : 'text-dark' }}">
        <i class="fa-solid fa-truck-fast me-1"></i> Đang giao GHN <span class="badge bg-light text-dark border ms-1">{{ $counts['shipping'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'completed' ? 'active bg-success text-white' : 'text-dark' }}">
        <i class="fa-solid fa-circle-check me-1"></i> Hoàn thành <span class="badge bg-light text-dark border ms-1">{{ $counts['completed'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'cancelled' ? 'active bg-danger text-white' : 'text-dark' }}">
        <i class="fa-solid fa-ban me-1"></i> Đã hủy <span class="badge bg-light text-dark border ms-1">{{ $counts['cancelled'] ?? 0 }}</span>
    </a>
</div>

<!-- BỘ LỌC TÌM KIẾM ĐƠN HÀNG NÂNG CAO -->
<div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
        <!-- Từ khóa -->
        <div class="col-md-3">
            <label class="form-label text-muted small fw-bold mb-1">Từ khóa tìm kiếm</label>
            <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Mã đơn, KH, SĐT, Mã GHN..." value="{{ request('search') }}">
        </div>

        <!-- Trạng thái hệ thống -->
        <div class="col-md-2">
            <label class="form-label text-muted small fw-bold mb-1">Trạng thái hệ thống</label>
            <select name="status" class="form-select form-select-sm rounded-3">
                <option value="">-- Tất cả --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>

        <!-- Trạng thái vận chuyển GHN -->
        <div class="col-md-2">
            <label class="form-label text-muted small fw-bold mb-1">Trạng thái GHN</label>
            <select name="shipping_status" class="form-select form-select-sm rounded-3">
                <option value="">-- Tất cả vận đơn --</option>
                <option value="ready_to_pick" {{ request('shipping_status') == 'ready_to_pick' ? 'selected' : '' }}>Chờ lấy hàng</option>
                <option value="picking" {{ request('shipping_status') == 'picking' ? 'selected' : '' }}>Đang lấy hàng</option>
                <option value="storing" {{ request('shipping_status') == 'storing' ? 'selected' : '' }}>Đang lưu kho</option>
                <option value="transporting" {{ request('shipping_status') == 'transporting' ? 'selected' : '' }}>Đang trung chuyển</option>
                <option value="delivering" {{ request('shipping_status') == 'delivering' ? 'selected' : '' }}>Đang giao tới khách</option>
                <option value="delivered" {{ request('shipping_status') == 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                <option value="cancel" {{ request('shipping_status') == 'cancel' ? 'selected' : '' }}>Đã hủy vận đơn</option>
            </select>
        </div>

        <!-- Trạng thái thanh toán -->
        <div class="col-md-2">
            <label class="form-label text-muted small fw-bold mb-1">Thanh toán</label>
            <select name="payment_status" class="form-select form-select-sm rounded-3">
                <option value="">-- Tất cả PTTT --</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
            </select>
        </div>

        <!-- Ngày tạo -->
        <div class="col-md-3">
            <label class="form-label text-muted small fw-bold mb-1">Khoảng thời gian</label>
            <div class="input-group input-group-sm">
                <input type="date" name="date_from" class="form-control rounded-start-3" value="{{ request('date_from') }}">
                <span class="input-group-text bg-light text-muted">-</span>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                <button type="submit" class="btn btn-primary fw-bold rounded-end-3" title="Áp dụng lọc">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- FORM XỬ LÝ HÀNG LOẠT (BULK ACTION) -->
<form action="{{ route('admin.orders.bulkAction') }}" method="POST" id="bulkForm">
    @csrf
    <!-- TOOLBAR THAO TÁC HÀNG LOẠT -->
    <div class="bg-white p-3 rounded-4 border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="selectAll" class="form-check-input my-0" style="width: 18px; height: 18px; cursor: pointer;">
            <label for="selectAll" class="form-check-label fw-bold small text-dark cursor-pointer">Chọn tất cả</label>
            <span id="selectedCountBadge" class="badge bg-primary rounded-pill ms-1 d-none">Đã chọn 0 mục</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <select name="action" class="form-select form-select-sm rounded-3 fw-medium" style="max-width: 260px;" required>
                <option value="">-- Chọn thao tác hàng loạt --</option>
                <option value="status_pending">Chuyển trạng thái: Chờ xác nhận</option>
                <option value="status_processing">Chuyển trạng thái: Đang xử lý</option>
                <option value="status_shipping">Chuyển trạng thái: Đang giao hàng</option>
                <option value="status_completed">Chuyển trạng thái: Hoàn thành</option>
                <option value="status_cancelled">Chuyển trạng thái: Hủy đơn hàng</option>
                <option value="payment_paid">Thanh toán: Đã thanh toán</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn thực hiện thao tác hàng loạt cho các đơn hàng đã chọn?')">
                <i class="fa-solid fa-bolt me-1"></i> Áp Dụng Hàng Loạt
            </button>
        </div>
    </div>

    <!-- BẢNG ĐƠN HÀNG -->
    <div class="bg-white rounded-4 border shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-3" style="width: 40px;"></th>
                        <th>Mã Đơn</th>
                        <th>Thời gian</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>PTTT</th>
                        <th>Thanh toán</th>
                        <th>Mã GHN</th>
                        <th>Trạng thái GHN</th>
                        <th>Trạng thái Đơn</th>
                        <th class="text-end pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" name="ids[]" value="{{ $ord->id }}" class="item-checkbox form-check-input" style="cursor: pointer;">
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $ord->order_code }}
                                </a>
                            </td>
                        <td class="text-muted">{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong class="d-block text-dark">{{ $ord->customer_name }}</strong>
                            <small class="text-muted">{{ $ord->customer_phone }}</small>
                        </td>
                        <td class="fw-bold text-danger">{{ number_format($ord->total_amount) }} đ</td>
                        <td><span class="badge bg-secondary text-uppercase">{{ $ord->payment_method }}</span></td>
                        <td>
                            <span class="badge {{ $ord->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $ord->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                            </span>
                        </td>
                        <td>
                            @if($ord->ghn_order_code)
                                <span class="font-monospace text-primary fw-bold">{{ $ord->ghn_order_code }}</span>
                            @else
                                <span class="text-muted fst-italic">Chưa tạo</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $ghnBadge = 'bg-secondary';
                                $ghnText = $ord->shipping_status ?? 'N/A';
                                switch($ord->shipping_status) {
                                    case 'ready_to_pick': $ghnBadge = 'bg-primary'; $ghnText = 'Chờ lấy'; break;
                                    case 'picking': $ghnBadge = 'bg-info text-dark'; $ghnText = 'Đang lấy'; break;
                                    case 'storing': $ghnBadge = 'bg-secondary'; $ghnText = 'Lưu kho GHN'; break;
                                    case 'transporting': $ghnBadge = 'bg-info text-dark'; $ghnText = 'Trung chuyển'; break;
                                    case 'delivering': $ghnBadge = 'bg-warning text-dark'; $ghnText = 'Đang giao'; break;
                                    case 'delivered': $ghnBadge = 'bg-success'; $ghnText = 'Đã giao'; break;
                                    case 'cancel': $ghnBadge = 'bg-danger'; $ghnText = 'Đã hủy GHN'; break;
                                }
                            @endphp
                            @if($ord->shipping_status)
                                <span class="badge {{ $ghnBadge }}">{{ $ghnText }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusBadge = 'bg-secondary';
                                switch($ord->status) {
                                    case 'pending': $statusBadge = 'bg-warning text-dark'; break;
                                    case 'processing': $statusBadge = 'bg-info text-dark'; break;
                                    case 'shipping': $statusBadge = 'bg-primary'; break;
                                    case 'completed': $statusBadge = 'bg-success'; break;
                                    case 'cancelled': $statusBadge = 'bg-danger'; break;
                                }
                            @endphp
                            <span class="badge {{ $statusBadge }} text-uppercase">{{ $ord->status }}</span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                Xem & Cập nhật
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào phù hợp với bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 bg-light border-top">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedBadge = document.getElementById('selectedCountBadge');

    function updateBadge() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            selectedBadge.innerText = `Đã chọn ${checkedCount} đơn`;
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
