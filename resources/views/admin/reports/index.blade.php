@extends('layouts.admin')

@section('content')
<!-- Include Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- HEADER BÁO CÁO -->
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i>Báo Cáo & Thống Kê Tài Chính</h3>
        <p class="text-muted small mb-0">Theo dõi doanh thu, phân tích số liệu kinh doanh, cước vận chuyển GHN và phương thức thanh toán.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-boxes-packing me-1"></i> Quản Lý Đơn Hàng
        </a>
        <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> In / Xuất Báo Cáo
        </button>
    </div>
</div>

<!-- THANH LỌC THỜI GIAN NHANH -->
<div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="btn-group w-100" role="group">
                <a href="{{ route('admin.reports.index', ['period' => 'today']) }}" 
                   class="btn btn-sm {{ $period === 'today' ? 'btn-primary active' : 'btn-outline-primary' }} fw-bold">Hôm nay</a>
                <a href="{{ route('admin.reports.index', ['period' => 'this_week']) }}" 
                   class="btn btn-sm {{ $period === 'this_week' ? 'btn-primary active' : 'btn-outline-primary' }} fw-bold">Tuần này</a>
                <a href="{{ route('admin.reports.index', ['period' => 'this_month']) }}" 
                   class="btn btn-sm {{ $period === 'this_month' ? 'btn-primary active' : 'btn-outline-primary' }} fw-bold">Tháng này</a>
                <a href="{{ route('admin.reports.index', ['period' => 'this_year']) }}" 
                   class="btn btn-sm {{ $period === 'this_year' ? 'btn-primary active' : 'btn-outline-primary' }} fw-bold">Năm nay</a>
                <a href="{{ route('admin.reports.index', ['period' => 'all']) }}" 
                   class="btn btn-sm {{ $period === 'all' ? 'btn-primary active' : 'btn-outline-primary' }} fw-bold">Tất cả</a>
            </div>
        </div>

        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted fw-bold">Từ ngày:</span>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                <span class="input-group-text bg-light text-muted fw-bold">Đến ngày:</span>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                <button type="submit" class="btn btn-primary fw-bold px-3">Lọc Dữ Liệu</button>
            </div>
        </div>

        <div class="col-md-2 text-end">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="group_by" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                <option value="daily" {{ $timeGroupBy === 'daily' ? 'selected' : '' }}>Gom theo Ngày</option>
                <option value="monthly" {{ $timeGroupBy === 'monthly' ? 'selected' : '' }}>Gom theo Tháng</option>
                <option value="yearly" {{ $timeGroupBy === 'yearly' ? 'selected' : '' }}>Gom theo Năm</option>
            </select>
        </div>
    </form>
</div>

<!-- THẺ THỐNG KÊ KPI TÀI CHÍNH TỔNG QUAN -->
<div class="row g-3 mb-4">
    <!-- Tổng Số Đơn Hàng -->
    <div class="col-md-4 col-lg-2">
        <div class="bg-white p-3 rounded-4 border shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold">Tổng Đơn Hàng</span>
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                    <i class="fa-solid fa-cart-shopping fa-lg"></i>
                </div>
            </div>
            <h4 class="fw-extrabold text-dark mb-0">{{ number_format($totalOrders) }}</h4>
            <small class="text-muted">đơn hàng phát sinh</small>
        </div>
    </div>

    <!-- Tổng Số Khách Hàng -->
    <div class="col-md-4 col-lg-2">
        <div class="bg-white p-3 rounded-4 border shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold">Khách Hàng</span>
                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                    <i class="fa-solid fa-users fa-lg"></i>
                </div>
            </div>
            <h4 class="fw-extrabold text-dark mb-0">{{ number_format($totalCustomers) }}</h4>
            <small class="text-muted">tài khoản hệ thống</small>
        </div>
    </div>

    <!-- Tổng Doanh Thu -->
    <div class="col-md-4 col-lg-3">
        <div class="bg-white p-3 rounded-4 border shadow-sm h-100 border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold">Tổng Doanh Thu</span>
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                    <i class="fa-solid fa-sack-dollar fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-extrabold text-primary mb-0">{{ number_format($totalRevenue) }} đ</h3>
            <small class="text-muted">tổng giá trị các đơn thành công</small>
        </div>
    </div>

    <!-- Doanh Thu Thực Nhận (Paid) -->
    <div class="col-md-6 col-lg-3">
        <div class="bg-white p-3 rounded-4 border shadow-sm h-100 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold">Doanh Thu Thực Nhận</span>
                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                    <i class="fa-solid fa-vault fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-extrabold text-success mb-0">{{ number_format($paidRevenue) }} đ</h3>
            <small class="text-muted">đã thanh toán thành công</small>
        </div>
    </div>

    <!-- Doanh Thu Chờ Thu (Pending / COD) -->
    <div class="col-md-6 col-lg-2">
        <div class="bg-white p-3 rounded-4 border shadow-sm h-100 border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold">Doanh Thu Chờ Thu</span>
                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                    <i class="fa-solid fa-hand-holding-dollar fa-lg"></i>
                </div>
            </div>
            <h4 class="fw-extrabold text-warning mb-0">{{ number_format($pendingRevenue) }} đ</h4>
            <small class="text-muted">COD / Chờ thanh toán</small>
        </div>
    </div>
</div>

<!-- KHU VỰC BIỂU ĐỒ ĐỒ HỌA (CHARTS - CHART.JS) -->
<div class="row g-4 mb-4">
    <!-- BIỂU ĐỒ BIẾN ĐỘNG DOANH THU THEO THỜI GIAN -->
    <div class="col-lg-8">
        <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chart-area text-primary me-2"></i>Biểu Đồ Biến Động Doanh Thu</h6>
                    <small class="text-muted">Doanh thu ghi nhận qua các mốc thời gian</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold">Biến Động Doanh Thu</span>
            </div>
            <div style="height: 320px; position: relative;">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- BIỂU ĐỒ TỶ LỆ DOANH THU THEO DANH MỤC -->
    <div class="col-lg-4">
        <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
            <div class="border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chart-pie text-success me-2"></i>Tỷ Lệ Theo Danh Mục</h6>
                <small class="text-muted">Doanh thu phân bổ theo các dòng máy chiếu</small>
            </div>
            <div style="height: 320px; position: relative;" class="d-flex align-items-center justify-content-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- KHU VỰC BẢNG SỐ LIỆU CHI TIẾT THỐNG KÊ (TABLES) -->
<div class="row g-4 mb-4">
    <!-- BẢNG 1: DOANH THU THEO DANH MỤC SẢN PHẨM -->
    <div class="col-lg-6">
        <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Thống Kê Doanh Thu Theo Danh Mục</h6>
                <span class="badge bg-light text-dark border">{{ $categoryStats->count() }} danh mục</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                        <tr>
                            <th>Danh Mục Sản Phẩm</th>
                            <th class="text-center">Số Đơn</th>
                            <th class="text-center">Số Lượng Bán</th>
                            <th class="text-end">Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryStats as $cat)
                            <tr>
                                <td><strong class="text-dark">{{ $cat->category_name }}</strong></td>
                                <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $cat->total_orders }}</span></td>
                                <td class="text-center fw-bold">{{ number_format($cat->total_quantity) }} cái</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($cat->total_revenue) }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BẢNG 2: DOANH THU THEO PHƯƠNG THỨC THANH TOÁN -->
    <div class="col-lg-6">
        <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-credit-card text-warning me-2"></i>Thống Kê Theo Phương Thức Thanh Toán</h6>
                <span class="badge bg-light text-dark border">{{ $paymentStats->count() }} hình thức</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                        <tr>
                            <th>Hình Thức Thanh Toán</th>
                            <th class="text-center">Số Đơn</th>
                            <th class="text-end">Tổng Giá Trị</th>
                            <th class="text-end">Thực Nhận (Paid)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentStats as $pay)
                            @php
                                $methodName = strtoupper($pay->payment_method);
                                if ($pay->payment_method === 'cod') $methodName = 'COD (Tiền mặt khi nhận)';
                                elseif ($pay->payment_method === 'bank_transfer') $methodName = 'Chuyển khoản VietQR';
                                elseif ($pay->payment_method === 'momo') $methodName = 'Ví MoMo';
                            @endphp
                            <tr>
                                <td><strong class="text-dark">{{ $methodName }}</strong></td>
                                <td class="text-center"><span class="badge bg-primary rounded-pill">{{ $pay->total_orders }}</span></td>
                                <td class="text-end fw-bold text-dark">{{ number_format($pay->total_revenue) }} đ</td>
                                <td class="text-end fw-bold text-success">{{ number_format($pay->paid_amount) }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Chưa có giao dịch.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- BẢNG 3: BẢNG DOANH THU CHI TIẾT THEO KHOẢNG THỜI GIAN (NGÀY / THÁNG / NĂM) -->
<div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
        <div>
            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i>Bảng Tổng Hợp Doanh Thu Theo {{ strtoupper($timeGroupBy) }}</h6>
            <small class="text-muted">Chi tiết con số tổng doanh thu và thực nhận theo mốc thời gian</small>
        </div>
        <span class="badge bg-light text-dark border">{{ $timeStats->count() }} dòng ghi nhận</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 small">
            <thead class="bg-light">
                <tr>
                    <th>Mốc Thời Gian ({{ ucfirst($timeGroupBy) }})</th>
                    <th class="text-center">Số Lượng Đơn</th>
                    <th class="text-end">Tổng Doanh Thu Phụ</th>
                    <th class="text-end">Doanh Thu Thực Nhận</th>
                    <th class="text-center">Tỷ Lệ Thu Tiền</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timeStats as $ts)
                    @php
                        $rate = $ts->total_revenue > 0 ? round(($ts->paid_revenue / $ts->total_revenue) * 100) : 0;
                    @endphp
                    <tr>
                        <td><strong class="text-primary font-monospace">{{ $ts->time_label }}</strong></td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $ts->total_orders }} đơn</span></td>
                        <td class="text-end fw-bold text-dark">{{ number_format($ts->total_revenue) }} đ</td>
                        <td class="text-end fw-bold text-success">{{ number_format($ts->paid_revenue) }} đ</td>
                        <td class="text-center">
                            <div class="progress rounded-pill" style="height: 18px;">
                                <div class="progress-bar bg-success" style="width: {{ $rate }}%;">{{ $rate }}%</div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Không có dữ liệu trong khoảng thời gian này.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- BẢNG 4: DANH SÁCH GIAO DỊCH / ĐƠN HÀNG GẦN ĐÂY -->
<div class="bg-white p-4 rounded-4 border shadow-sm">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-primary me-2"></i>Nhật Ký Giao Dịch Đơn Hàng Gần Đây</h6>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link text-decoration-none">Xem toàn bộ đơn -></a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="bg-light">
                <tr>
                    <th>Mã Đơn</th>
                    <th>Thời gian</th>
                    <th>Khách hàng</th>
                    <th>PTTT</th>
                    <th>Trạng thái thanh toán</th>
                    <th class="text-end">Tổng thanh toán</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $tx)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $tx->id) }}" class="fw-bold text-primary">{{ $tx->order_code }}</a></td>
                        <td class="text-muted">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $tx->customer_name }}</strong> ({{ $tx->customer_phone }})</td>
                        <td><span class="badge bg-secondary text-uppercase">{{ $tx->payment_method }}</span></td>
                        <td>
                            <span class="badge {{ $tx->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $tx->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-danger">{{ number_format($tx->total_amount) }} đ</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có giao dịch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- JAVASCRIPT AJAX FETCH CHART DATA AND RENDER WITH CHART.JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartUrl = "{{ route('admin.reports.charts') }}?period={{ $period }}&date_from={{ $dateFrom }}&date_to={{ $dateTo }}";

    fetch(chartUrl)
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            // 1. Biểu đồ đường (Line Chart) Biến động doanh thu theo thời gian
            const timelineCtx = document.getElementById('timelineChart').getContext('2d');
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: data.timeline.labels,
                    datasets: [
                        {
                            label: 'Doanh thu (VNĐ)',
                            data: data.timeline.revenue,
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2, 132, 199, 0.1)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#0284c7'
                        },
                        {
                            label: 'Số đơn hàng',
                            data: data.timeline.orders,
                            borderColor: '#f59e0b',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            yAxisID: 'yOrders',
                            pointRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' đ';
                                }
                            }
                        },
                        yOrders: {
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // 2. Biểu đồ tròn (Doughnut Chart) Phân bổ doanh thu theo danh mục
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: data.categories.labels,
                    datasets: [{
                        data: data.categories.values,
                        backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let val = context.raw || 0;
                                    return label + ': ' + val.toLocaleString('vi-VN') + ' đ';
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(err => console.error('Lỗi khi tải dữ liệu biểu đồ:', err));
});
</script>
@endsection
