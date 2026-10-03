<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Trang tổng quan Dashboard
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::whereIn('status', ['processing', 'shipping', 'confirmed'])->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $totalCoupons = Coupon::count();
        $totalTransactions = PaymentTransaction::count();

        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $recentProducts = Product::with('category')->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalCategories', 
            'totalUsers', 
            'totalOrders', 
            'totalRevenue', 
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalCoupons',
            'totalTransactions',
            'recentOrders', 
            'recentProducts'
        ));
    }
}

