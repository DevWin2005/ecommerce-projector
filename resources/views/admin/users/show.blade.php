@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách người dùng
        </a>
        <h3 class="fw-bold text-dark mb-0">Hồ Sơ Người Dùng: <span class="text-primary">{{ $user->name }}</span></h3>
    </div>
    <div>
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning fw-bold btn-sm rounded-pill px-3">
            <i class="fa-solid fa-pen me-1"></i> Chỉnh Sửa Tài Khoản
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- THÔNG TIN CÁ NHÂN -->
    <div class="col-lg-4">
        <div class="bg-white p-4 rounded-4 border shadow-sm text-center mb-4">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold display-4 mb-3" style="width: 90px; height: 90px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
            <p class="text-muted small font-monospace mb-2">{{ $user->email }}</p>

            <div class="d-flex justify-content-center gap-2 mb-3">
                @if($user->role === 'admin')
                    <span class="badge bg-danger text-uppercase px-3 py-2"><i class="fa-solid fa-shield-halved me-1"></i> Quản Trị Viên</span>
                @else
                    <span class="badge bg-info text-dark text-uppercase px-3 py-2"><i class="fa-solid fa-user me-1"></i> Khách Hàng</span>
                @endif

                @if($user->verify || $user->email_verified_at)
                    <span class="badge bg-success px-3 py-2"><i class="fa-solid fa-check me-1"></i> Đã xác thực</span>
                @else
                    <span class="badge bg-secondary text-dark bg-opacity-10 border px-3 py-2">Chưa xác thực</span>
                @endif
            </div>

            <hr class="my-3">

            <div class="text-start small lh-lg">
                <div><strong>ID Tài khoản:</strong> <span class="font-monospace">#{{ $user->id }}</span></div>
                <div><strong>Ngày tạo tài khoản:</strong> {{ $user->created_at->format('d/m/Y H:i:s') }}</div>
                <div><strong>Cập nhật gần nhất:</strong> {{ $user->updated_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- LỊCH SỬ ĐƠN HÀNG ĐÃ ĐẶT -->
    <div class="col-lg-8">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-box-archive text-primary me-2"></i>Lịch Sử Đơn Hàng Của Khách Hàng</h6>
                <span class="badge bg-primary rounded-pill px-3">{{ $user->orders->count() }} Đơn hàng</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>PTTT</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái đơn</th>
                            <th class="text-end">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->orders as $ord)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $ord->id) }}" class="fw-bold text-primary">{{ $ord->order_code }}</a></td>
                                <td class="text-muted">{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold text-danger">{{ number_format($ord->total_amount) }} đ</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $ord->payment_method }}</span></td>
                                <td>
                                    <span class="badge {{ $ord->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $ord->payment_status === 'paid' ? 'Đã TT' : 'Chưa TT' }}
                                    </span>
                                </td>
                                <td><span class="badge bg-info text-dark text-uppercase">{{ $ord->status }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1">
                                        Xem ->
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Người dùng này chưa phát sinh đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
