<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    /**
     * Danh sách nhật ký giao dịch thanh toán
     */
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['order.user'])->latest();

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Lọc theo trạng thái giao dịch
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo Mã giao dịch hoặc Mã đơn hàng
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oQ) use ($search) {
                      $oQ->where('order_code', 'like', "%{$search}%")
                         ->orWhere('customer_name', 'like', "%{$search}%")
                         ->orWhere('customer_phone', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        $totalAmount = (clone $query)->where('status', 'success')->sum('amount');

        $counts = [
            'all' => PaymentTransaction::count(),
            'success' => PaymentTransaction::where('status', 'success')->count(),
            'pending' => PaymentTransaction::where('status', 'pending')->count(),
            'failed' => PaymentTransaction::where('status', 'failed')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'totalAmount', 'counts'));
    }
}
