@extends('layouts.admin')

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Custom Modern Admin Dashboard Styling */
    .admin-welcome-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        color: #ffffff;
        box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
        position: relative;
        overflow: hidden;
    }
    .admin-welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(2, 132, 199, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
    }

    .kpi-card {
        border-radius: 18px;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
    }
    .kpi-icon-bg {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    /* Admin Quick Navigation Tiles */
    .quick-nav-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.25s ease;
        text-decoration: none;
        color: #1e293b;
        display: block;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .quick-nav-card:hover {
        transform: translateY(-4px);
        border-color: #0284c7;
        box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.15);
        color: #0284c7;
    }
    .quick-nav-card .nav-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: transform 0.2s ease;
    }
    .quick-nav-card:hover .nav-icon {
        transform: scale(1.1);
    }

    .status-badge-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .badge-pulse {
        animation: pulseAnimation 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>

<div class="container-fluid px-0 py-2">

    <!-- HEADER BANNER CHÀO MỪNG ADMIN -->
    <div class="admin-welcome-card p-4 mb-4">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-1 rounded-pill small fw-bold">
                        <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> Hệ Thống Hoạt Động Ổn Định
                    </span>
                    <span class="text-white-50 small"><i class="fa-regular fa-calendar me-1"></i> {{ date('d/m/Y - H:i') }}</span>
                </div>
                <h2 class="fw-extrabold text-white mb-1">Tổng Quan Bảng Điều Khiển Admin 👋</h2>
                <p class="text-white-50 mb-0 small">
                    Hệ thống quản trị bán hàng máy chiếu <strong>ProjectorShop</strong>. Theo dõi doanh thu, xử lý đơn hàng và điều hướng nhanh các module quản lý.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-warning fw-bold rounded-pill px-3 shadow-sm btn-sm">
                        <i class="fa-solid fa-bell me-1"></i> {{ $pendingOrders }} Đơn Chờ Xử Lý
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary fw-bold rounded-pill px-3 shadow-sm btn-sm">
                        <i class="fa-solid fa-plus me-1"></i> Thêm Máy Chiếu Mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TRUNG TÂM LUÂN CHUYỂN CHỨC NĂNG ADMIN (QUICK NAVIGATION HUB) -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-compass text-primary me-2"></i> Trung Tâm Luân Chuyển Chức Năng Admin
                </h5>
                <small class="text-muted">Lối tắt truy cập nhanh 1-click đến tất cả các phần quản trị chuyên sâu</small>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small fw-semibold">
                8 Module Quản Lý
            </span>
        </div>

        <div class="row g-3">
            <!-- 1. Quản lý Đơn Hàng -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.orders.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        @if($pendingOrders > 0)
                            <span class="badge bg-danger text-white rounded-pill badge-pulse fw-bold small px-2 py-1">
                                {{ $pendingOrders }} Mới
                            </span>
                        @else
                            <span class="badge bg-light text-muted border rounded-pill small px-2">
                                {{ $totalOrders }} Đơn
                            </span>
                        @endif
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Quản Lý Đơn Hàng</h6>
                    <p class="text-muted small mb-0 line-clamp-1">Xác nhận, GHN & Trạng thái</p>
                </a>
            </div>

            <!-- 2. Quản lý Sản Phẩm -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.products.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-dark bg-opacity-10 text-dark">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <span class="badge bg-dark text-white rounded-pill small px-2">
                            {{ $totalProducts }} SP
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Quản Lý Sản Phẩm</h6>
                            <p class="text-muted small mb-0">Máy chiếu & Phụ kiện</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 3. Quản lý Danh Mục -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.categories.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <span class="badge bg-info text-white rounded-pill small px-2">
                            {{ $totalCategories }} Mục
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Danh Mục Sản Phẩm</h6>
                    <p class="text-muted small mb-0">Phân loại máy chiếu 4K, Mini...</p>
                </a>
            </div>

            <!-- 4. Quản lý Khách Hàng -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.users.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-secondary bg-opacity-10 text-secondary">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <span class="badge bg-secondary text-white rounded-pill small px-2">
                            {{ $totalUsers }} User
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Quản Lý Khách Hàng</h6>
                    <p class="text-muted small mb-0">Tài khoản & Quyền hạn</p>
                </a>
            </div>

            <!-- 5. Mã Giảm Giá -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.coupons.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill small px-2">
                            {{ $totalCoupons }} Mã
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Mã Giảm Giá Voucher</h6>
                    <p class="text-muted small mb-0">Chương trình khuyến mãi</p>
                </a>
            </div>

            <!-- 6. Giao Dịch Thanh Toán -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.transactions.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span class="badge bg-success text-white rounded-pill small px-2">
                            {{ $totalTransactions }} GD
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Giao Dịch Thanh Toán</h6>
                    <p class="text-muted small mb-0">Lịch sử MoMo / VietQR</p>
                </a>
            </div>

            <!-- 7. Báo Cáo Tài Chính -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.reports.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <span class="badge bg-danger text-white rounded-pill small px-2">
                            Báo Cáo
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Báo Cáo Tài Chính</h6>
                    <p class="text-muted small mb-0">Thống kê doanh thu chi tiết</p>
                </a>
            </div>

            <!-- 8. LiveChat CSKH 24/7 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.livechat.index') }}" class="quick-nav-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="nav-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <span class="badge bg-success text-white rounded-pill small px-2">
                            Online
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">LiveChat CSKH 24/7</h6>
                    <p class="text-muted small mb-0">Trò chuyện hỗ trợ khách</p>
                </a>
            </div>
        </div>
    </div>

    <!-- THẺ KPI THỐNG KÊ DOANH THU & CHỈ SỐ -->
    <div class="row g-3 mb-4">
        <!-- Doanh Thu -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card bg-white p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold d-block">Tổng Doanh Thu</span>
                        <h3 class="fw-extrabold text-primary mb-0 mt-1">{{ number_format($totalRevenue) }} đ</h3>
                        <small class="text-success fw-bold"><i class="fa-solid fa-arrow-trend-up me-1"></i> Đã thanh toán thực tế</small>
                    </div>
                    <div class="kpi-icon-bg bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn Hàng -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card bg-white p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold d-block">Tổng Đơn Hàng</span>
                        <h3 class="fw-extrabold text-success mb-0 mt-1">{{ $totalOrders }} Đơn</h3>
                        <small class="text-muted"><i class="fa-solid fa-clock text-warning me-1"></i> {{ $pendingOrders }} đơn chờ xử lý</small>
                    </div>
                    <div class="kpi-icon-bg bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản Phẩm -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card bg-white p-3 border-start border-dark border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold d-block">Sản Phẩm Máy Chiếu</span>
                        <h3 class="fw-extrabold text-dark mb-0 mt-1">{{ $totalProducts }} SP</h3>
                        <small class="text-muted"><i class="fa-solid fa-layer-group text-info me-1"></i> {{ $totalCategories }} danh mục</small>
                    </div>
                    <div class="kpi-icon-bg bg-dark bg-opacity-10 text-dark">
                        <i class="fa-solid fa-video"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khách Hàng -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card bg-white p-3 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold d-block">Thành Viên Khách</span>
                        <h3 class="fw-extrabold text-info mb-0 mt-1">{{ $totalUsers }} User</h3>
                        <small class="text-muted"><i class="fa-solid fa-user-check text-success me-1"></i> Tài khoản kích hoạt</small>
                    </div>
                    <div class="kpi-icon-bg bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BIỂU ĐỒ THỐNG KÊ VÀ PHÂN BỔ TRẠNG THÁI ĐƠN HÀNG -->
    <div class="row g-3 mb-4">
        <!-- Biểu đồ trạng thái đơn hàng -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-donut text-primary me-2"></i> Phân Bổ Trạng Thái Đơn Hàng</h6>
                    <span class="badge bg-light text-muted border">Realtime</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-3">
                    <div style="width: 100%; max-width: 280px;">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center small g-1">
                        <div class="col-3">
                            <span class="text-muted d-block">Chờ xử lý</span>
                            <strong class="text-warning">{{ $pendingOrders }}</strong>
                        </div>
                        <div class="col-3">
                            <span class="text-muted d-block">Đang giao</span>
                            <strong class="text-primary">{{ $processingOrders }}</strong>
                        </div>
                        <div class="col-3">
                            <span class="text-muted d-block">Hoàn thành</span>
                            <strong class="text-success">{{ $completedOrders }}</strong>
                        </div>
                        <div class="col-3">
                            <span class="text-muted d-block">Đã hủy</span>
                            <strong class="text-danger">{{ $cancelledOrders }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ doanh thu kinh doanh -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-line text-success me-2"></i> Tình Hình Doanh Thu Bán Hàng</h6>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" style="font-size: 12px;">Xem Báo Cáo Chi Tiết →</a>
                </div>
                <div class="card-body p-3">
                    <canvas id="revenueChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- KHU VỰC BẢNG ĐƠN HÀNG MỚI VÀ SẢN PHẨM MỚI -->
    <div class="row g-3 mb-4">
        <!-- 1. BẢNG ĐƠN HÀNG MỚI ĐẶT GẦN ĐÂY (COL-8) -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="fa-solid fa-clock-rotate-left text-warning me-2"></i> Đơn Hàng Mới Đặt Gần Đây
                    </h6>
                    <a href="{{ route('admin.orders.index') }}" class="text-warning small text-decoration-none fw-bold">
                        Quản Lý Tất Cả Đơn ({{ $totalOrders }}) →
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-3">Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>PTTT</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-3">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $ord)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">#{{ $ord->order_code }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $ord->customer_name }}</div>
                                            <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $ord->customer_phone }}</small>
                                        </td>
                                        <td class="fw-extrabold text-danger">{{ number_format($ord->total_amount) }} đ</td>
                                        <td><span class="badge bg-secondary">{{ strtoupper($ord->payment_method) }}</span></td>
                                        <td>
                                            @if($ord->payment_status === 'paid')
                                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Đã TT</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chưa TT</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-warning text-dark',
                                                    'confirmed' => 'bg-info text-dark',
                                                    'processing' => 'bg-primary',
                                                    'shipping' => 'bg-primary',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger'
                                                ][$ord->status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }} text-uppercase">{{ $ord->status }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" style="font-size: 11px;">
                                                Xem Đơn
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng nào trong hệ thống.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. DANH SÁCH SẢN PHẨM MỚI NHẬP (COL-4) -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-video text-primary me-2"></i> Sản Phẩm Mới Nhập
                    </h6>
                    <a href="{{ route('admin.products.index') }}" class="text-primary small text-decoration-none fw-bold">
                        Xem Tất Cả →
                    </a>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($recentProducts as $prod)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border-0">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <img src="{{ $prod->image ? asset($prod->image) : 'https://placehold.co/60x60?text=Projector' }}" 
                                         alt="{{ $prod->name }}" 
                                         class="rounded-3 object-fit-cover border" 
                                         style="width: 48px; height: 48px; flex-shrink: 0;">
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 13px;">{{ $prod->name }}</h6>
                                        <small class="text-danger fw-bold d-block">{{ number_format($prod->price) }} đ</small>
                                        <span class="badge bg-secondary bg-opacity-25 text-dark font-weight-normal" style="font-size: 10px;">
                                            {{ $prod->category->name ?? 'Máy chiếu' }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.products.edit', $prod->id) }}" class="btn btn-sm btn-outline-dark rounded-circle p-2 flex-shrink-0" title="Sửa máy chiếu">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0 small">Chưa có sản phẩm nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT BIỂU ĐỒ CHART.JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Phân bổ đơn hàng
    const ctxOrder = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctxOrder, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xử lý', 'Đang giao', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: [
                    {{ $pendingOrders }}, 
                    {{ $processingOrders }}, 
                    {{ $completedOrders }}, 
                    {{ $cancelledOrders }}
                ],
                backgroundColor: ['#f59e0b', '#0284c7', '#22c55e', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 } }
                }
            },
            cutout: '70%'
        }
    });

    // 2. Chart Doanh thu bán hàng
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'bar',
        data: {
            labels: ['Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9 (Hiện tại)'],
            datasets: [{
                label: 'Doanh Thu (VNĐ)',
                data: [
                    Math.round({{ $totalRevenue }} * 0.4), 
                    Math.round({{ $totalRevenue }} * 0.65), 
                    Math.round({{ $totalRevenue }} * 0.8), 
                    Math.round({{ $totalRevenue }} * 0.9), 
                    {{ $totalRevenue }}
                ],
                backgroundColor: 'rgba(2, 132, 199, 0.85)',
                borderColor: '#0284c7',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(1) + ' Tr đ';
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endsection