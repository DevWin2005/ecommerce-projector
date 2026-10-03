@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-primary me-2"></i>Quản Lý Giao Dịch Thanh Toán</h3>
        <p class="text-muted small mb-0">Theo dõi chi tiết tất cả các giao dịch thanh toán ngân hàng, MoMo, VietQR và COD.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-chart-pie me-1"></i> Xem Báo Cáo Tài Chính
        </a>
    </div>
</div>

<!-- TAB LỌC TRẠNG THÁI GIAO DỊCH -->
<div class="nav nav-pills bg-white p-2 rounded-4 border shadow-sm mb-4 gap-1 flex-wrap">
    <a href="{{ route('admin.transactions.index') }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ !request('status') ? 'active bg-primary text-white' : 'text-dark' }}">
        Tất cả giao dịch <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.transactions.index', array_merge(request()->except('page'), ['status' => 'success'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'success' ? 'active bg-success text-white' : 'text-dark' }}">
        <i class="fa-solid fa-circle-check me-1"></i> Thành công <span class="badge bg-light text-dark border ms-1">{{ $counts['success'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.transactions.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'pending' ? 'active bg-warning text-dark' : 'text-dark' }}">
        <i class="fa-regular fa-clock me-1"></i> Chờ xử lý <span class="badge bg-light text-dark border ms-1">{{ $counts['pending'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.transactions.index', array_merge(request()->except('page'), ['status' => 'failed'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('status') == 'failed' ? 'active bg-danger text-white' : 'text-dark' }}">
        <i class="fa-solid fa-circle-xmark me-1"></i> Thất bại <span class="badge bg-light text-dark border ms-1">{{ $counts['failed'] ?? 0 }}</span>
    </a>
</div>

<!-- BỘ LỌC TÌM KIẾM NÂNG CAO -->
<div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
    <form action="{{ route('admin.transactions.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Mã giao dịch, Mã đơn hàng, Tên KH, SĐT..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="payment_method" class="form-select form-select-sm rounded-3">
                <option value="">-- Tất cả phương thức --</option>
                <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD (Tiền mặt)</option>
                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản VietQR</option>
                <option value="momo" {{ request('payment_method') == 'momo' ? 'selected' : '' }}>Ví MoMo</option>
                <option value="momo_atm" {{ request('payment_method') == 'momo_atm' ? 'selected' : '' }}>MoMo Thẻ ATM</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm rounded-3">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công (Success)</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại (Failed)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-3">Lọc Giao Dịch</button>
        </div>
    </form>
</div>

<!-- BẢNG LỊCH SỬ GIAO DỊCH -->
<div class="bg-white rounded-4 border shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="bg-light text-muted">
                <tr>
                    <th class="ps-3">Mã Giao Dịch</th>
                    <th>Mã Đơn Hàng</th>
                    <th>Khách hàng</th>
                    <th>Số tiền</th>
                    <th>PTTT</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th class="text-end pe-3">Chi tiết đơn</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td class="ps-3 fw-bold font-monospace text-primary">
                            {{ $tx->transaction_id ?? ('TX-' . $tx->id) }}
                        </td>
                        <td>
                            @if($tx->order)
                                <a href="{{ route('admin.orders.show', $tx->order_id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $tx->order->order_code }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($tx->order)
                                <strong class="d-block text-dark">{{ $tx->order->customer_name }}</strong>
                                <small class="text-muted">{{ $tx->order->customer_phone }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-bold text-danger">{{ number_format($tx->amount) }} đ</td>
                        <td>
                            @php
                                $mBadge = 'bg-secondary';
                                if ($tx->payment_method === 'momo' || $tx->payment_method === 'momo_atm') $mBadge = 'bg-danger';
                                elseif ($tx->payment_method === 'bank_transfer') $mBadge = 'bg-primary';
                            @endphp
                            <span class="badge {{ $mBadge }} text-uppercase">{{ $tx->payment_method }}</span>
                        </td>
                        <td>
                            @if($tx->status === 'success' || $tx->status === 'paid')
                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Thành công</span>
                            @elseif($tx->status === 'pending')
                                <span class="badge bg-warning text-dark"><i class="fa-regular fa-clock me-1"></i> Chờ xử lý</span>
                            @else
                                <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Thất bại</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $tx->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="text-end pe-3">
                            @if($tx->order)
                                <a href="{{ route('admin.orders.show', $tx->order_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                    Xem Đơn ->
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">Không tìm thấy giao dịch thanh toán nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 bg-light border-top">
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
