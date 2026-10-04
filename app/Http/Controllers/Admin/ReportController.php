<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Phương thức index(): Bảng thống kê số liệu và tổng quan tài chính
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Tạo Query nền tảng cho Order theo khoảng thời gian
        $orderQuery = Order::query();
        $this->applyDateFilter($orderQuery, $period, $dateFrom, $dateTo);

        // 1. TỔNG QUAN KPI THỐNG KÊ
        $totalOrders = (clone $orderQuery)->count();
        $totalCustomers = User::where('role', 'user')->count();
        
        // Tổng doanh thu (không bao gồm các đơn đã bị hủy)
        $totalRevenue = (clone $orderQuery)->where('status', '!=', 'cancelled')->sum('total_amount');
        
        // Doanh thu thực nhận (Đã thanh toán)
        $paidRevenue = (clone $orderQuery)->where('payment_status', 'paid')->sum('total_amount');
        
        // Doanh thu chờ xử lý / COD chưa thu
        $pendingRevenue = (clone $orderQuery)->where('payment_status', 'pending')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Tổng cước phí GHN thực tế & Giảm giá coupon
        $totalGhnFees = (clone $orderQuery)->where('status', '!=', 'cancelled')->sum(DB::raw('COALESCE(ghn_total_fee, shipping_fee)'));
        $totalDiscounts = (clone $orderQuery)->where('status', '!=', 'cancelled')->sum('discount_amount');

        // 2. DOANH THU THEO DANH MỤC SẢN PHẨM
        $categoryRevenue = OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', '!=', 'cancelled');

        if ($period !== 'all' || ($dateFrom && $dateTo)) {
            $this->applyDateFilterJoin($categoryRevenue, 'orders.created_at', $period, $dateFrom, $dateTo);
        }

        $categoryStats = $categoryRevenue->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // 3. DOANH THU THEO PHƯƠNG THỨC THANH TOÁN
        $paymentStats = (clone $orderQuery)->select(
                'payment_method',
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_amount')
            )
            ->where('status', '!=', 'cancelled')
            ->groupBy('payment_method')
            ->get();

        // 4. DOANH THU THEO THỜI GIAN (NGÀY / THÁNG / NĂM)
        $timeGroupBy = $request->get('group_by', 'daily'); // daily, monthly, yearly
        $timeQuery = (clone $orderQuery)->where('status', '!=', 'cancelled');

        if ($timeGroupBy === 'yearly') {
            $timeStats = $timeQuery->select(
                DB::raw('YEAR(created_at) as time_label'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue')
            )->groupBy(DB::raw('YEAR(created_at)'))
             ->orderBy(DB::raw('YEAR(created_at)'), 'desc')->get();
        } elseif ($timeGroupBy === 'monthly') {
            $timeStats = $timeQuery->select(
                DB::raw('DATE_FORMAT(created_at, "%m/%Y") as time_label'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue')
            )->groupBy(DB::raw('DATE_FORMAT(created_at, "%m/%Y")'))
             ->orderBy(DB::raw('MIN(created_at)'), 'desc')->get();
        } else {
            // daily
            $timeStats = $timeQuery->select(
                DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as time_label'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue')
            )->groupBy(DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y")'))
             ->orderBy(DB::raw('MIN(created_at)'), 'desc')->limit(30)->get();
        }

        // 5. DANH SÁCH GIAO DỊCH / ĐƠN HÀNG TÀI CHÍNH GẦN ĐÂY
        $recentTransactions = (clone $orderQuery)->with(['user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'period',
            'dateFrom',
            'dateTo',
            'timeGroupBy',
            'totalOrders',
            'totalCustomers',
            'totalRevenue',
            'paidRevenue',
            'pendingRevenue',
            'totalGhnFees',
            'totalDiscounts',
            'categoryStats',
            'paymentStats',
            'timeStats',
            'recentTransactions'
        ));
    }

    /**
     * Phương thức charts(): Trả về dữ liệu JSON cho biểu đồ Chart.js
     */
    public function charts(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $orderQuery = Order::query()->where('status', '!=', 'cancelled');
        $this->applyDateFilter($orderQuery, $period, $dateFrom, $dateTo);

        // 1. Biểu đồ Doanh thu theo chuỗi thời gian
        $timelineData = (clone $orderQuery)->select(
                DB::raw('DATE(created_at) as date_key'),
                DB::raw('DATE_FORMAT(created_at, "%d/%m") as formatted_date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(id) as order_count')
            )
            ->groupBy('date_key', 'formatted_date')
            ->orderBy('date_key', 'asc')
            ->get();

        $timelineLabels = $timelineData->pluck('formatted_date');
        $timelineRevenue = $timelineData->pluck('revenue');
        $timelineOrders = $timelineData->pluck('order_count');

        // 2. Biểu đồ Doanh thu theo Danh mục
        $categoryData = OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', '!=', 'cancelled');

        if ($period !== 'all' || ($dateFrom && $dateTo)) {
            $this->applyDateFilterJoin($categoryData, 'orders.created_at', $period, $dateFrom, $dateTo);
        }

        $categoryChart = $categoryData->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $categoryLabels = $categoryChart->pluck('category_name');
        $categoryValues = $categoryChart->pluck('total_revenue');

        // 3. Biểu đồ Doanh thu theo Phương thức thanh toán
        $paymentData = (clone $orderQuery)->select(
                'payment_method',
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('payment_method')
            ->get();

        $methodNameMap = [
            'cod' => 'Thanh toán COD',
            'bank_transfer' => 'Chuyển khoản VietQR',
            'momo' => 'Ví MoMo',
            'momo_atm' => 'MoMo Thẻ ATM',
            'vnpay' => 'Cổng VNPay',
        ];

        $paymentLabels = $paymentData->map(fn($item) => $methodNameMap[$item->payment_method] ?? strtoupper($item->payment_method));
        $paymentValues = $paymentData->pluck('total_revenue');

        return response()->json([
            'success' => true,
            'period' => $period,
            'timeline' => [
                'labels' => $timelineLabels,
                'revenue' => $timelineRevenue,
                'orders' => $timelineOrders,
            ],
            'categories' => [
                'labels' => $categoryLabels,
                'values' => $categoryValues,
            ],
            'payment_methods' => [
                'labels' => $paymentLabels,
                'values' => $paymentValues,
            ],
            'summary' => [
                'total_revenue' => (clone $orderQuery)->sum('total_amount'),
                'paid_revenue' => (clone $orderQuery)->where('payment_status', 'paid')->sum('total_amount'),
                'total_orders' => (clone $orderQuery)->count(),
            ]
        ]);
    }

    /**
     * Helper lọc thời gian cho Eloquent Query
     */
    private function applyDateFilter($query, $period, $dateFrom = null, $dateTo = null)
    {
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
            return;
        }

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'all':
            default:
                // Không giới hạn
                break;
        }
    }

    /**
     * Helper lọc thời gian cho Query Builder Join
     */
    private function applyDateFilterJoin($query, $column, $period, $dateFrom = null, $dateTo = null)
    {
        if ($dateFrom && $dateTo) {
            $query->whereBetween($column, [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
            return;
        }

        switch ($period) {
            case 'today':
                $query->whereDate($column, Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween($column, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth($column, Carbon::now()->month)
                      ->whereYear($column, Carbon::now()->year);
                break;
            case 'this_year':
                $query->whereYear($column, Carbon::now()->year);
                break;
            case 'all':
            default:
                break;
        }
    }
}
