<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Auth\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PaymentTransactionController;
use App\Http\Controllers\User\LiveChatController as UserLiveChatController;
use App\Http\Controllers\Admin\LiveChatController as AdminLiveChatController;

// User Controllers
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\MomoController;

/*
|--------------------------------------------------------------------------
| TRANG LANDING WELCOME & TRANG CHỦ BÁN HÀNG
|--------------------------------------------------------------------------
*/
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Sản phẩm User
Route::get('/products/{product}', [UserProductController::class, 'show'])->name('products.show');
Route::get('/api/products/search', [UserProductController::class, 'searchApi'])->name('api.products.search');

// Tra cứu đơn hàng vãng lai không cần đăng nhập
Route::get('/order-lookup', [UserOrderController::class, 'showLookupForm'])->name('order.lookup');
Route::post('/order-lookup', [UserOrderController::class, 'lookup'])->name('order.lookup.post');

// Cổng thanh toán MoMo Callback & IPN
Route::get('/momo/callback', [MomoController::class, 'callback'])->name('momo.callback');
Route::post('/momo/ipn', [MomoController::class, 'ipn'])->name('momo.ipn');


// LiveChat Hỗ Trợ Khách Hàng (User Side - Dành cho cả Khách vãng lai & Thành viên)
Route::prefix('livechat')->name('livechat.')->group(function () {
    Route::get('/messages', [UserLiveChatController::class, 'getMessages'])->name('messages');
    Route::post('/send', [UserLiveChatController::class, 'sendMessage'])->name('send');
});


/*
|--------------------------------------------------------------------------
| GIỎ HÀNG (CHO CẢ KHÁCH VÃNG LAI & THÀNH VIÊN)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/select', [CartController::class, 'updateSelection'])->name('cart.select');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');


/*
|--------------------------------------------------------------------------
| THANH TOÁN, ĐƠN HÀNG, WISH-LIST (YÊU CẦU ĐĂNG NHẬP / AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Đặt hàng (Checkout / Payment)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/payment', [CheckoutController::class, 'index'])->name('payment.index');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/payment/process', [CheckoutController::class, 'store'])->name('payment.process');
    Route::get('/checkout/success/{orderCode}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Chuyển hướng thanh toán MoMo
    Route::get('/momo/start/{orderId}', [MomoController::class, 'start'])->name('momo.start');
    Route::get('/momo/pay-again/{orderId}', [MomoController::class, 'payAgain'])->name('momo.pay-again');

    // Lịch sử Đơn hàng Cá nhân & Khôi phục đơn (Reorder)
    Route::get('/my-orders', [UserOrderController::class, 'myOrders'])->name('orders.my');
    Route::get('/my-orders/{id}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::post('/my-orders/{id}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/my-orders/{id}/reorder', [UserOrderController::class, 'reorderAndEdit'])->name('orders.reorder');
    Route::post('/my-orders/{id}/change-payment', [UserOrderController::class, 'changePaymentMethod'])->name('orders.changePayment');

    // Danh sách Yêu thích (Wishlist)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Đánh giá sản phẩm
    Route::post('/products/{productId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});


/*
|--------------------------------------------------------------------------
| KHU VỰC XÁC THỰC (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/email/verify', [AuthController::class, 'showVerifyEmailNotice'])->name('verification.notice');
Route::post('/email/verify/resend', [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');


/*
|--------------------------------------------------------------------------
| KHU VỰC QUẢN TRỊ ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Bulk Actions (Xử lý hàng loạt)
    Route::post('/categories/bulk-action', [AdminCategoryController::class, 'bulkAction'])->name('categories.bulkAction');
    Route::resource('categories', AdminCategoryController::class);

    Route::post('/products/bulk-action', [AdminProductController::class, 'bulkAction'])->name('products.bulkAction');
    Route::resource('products', AdminProductController::class)->except(['show']);
    
    // Quản lý đơn hàng Admin
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/bulk-action', [AdminOrderController::class, 'bulkAction'])->name('orders.bulkAction');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/ghn-create', [AdminOrderController::class, 'createGHNOrder'])->name('orders.ghnCreate');
    Route::post('/orders/{id}/ghn-sync', [AdminOrderController::class, 'syncGHNStatus'])->name('orders.ghnSync');

    // Báo cáo & Thống kê Tài chính
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/charts', [ReportController::class, 'charts'])->name('reports.charts');

    // LiveChat Hỗ Trợ Admin
    Route::get('/livechat', [AdminLiveChatController::class, 'index'])->name('livechat.index');
    Route::get('/livechat/conversations', [AdminLiveChatController::class, 'getConversations'])->name('livechat.conversations');
    Route::get('/livechat/search-users', [AdminLiveChatController::class, 'searchUsers'])->name('livechat.searchUsers');
    Route::get('/livechat/messages/{userId}', [AdminLiveChatController::class, 'getMessages'])->name('livechat.messages');
    Route::post('/livechat/send/{userId}', [AdminLiveChatController::class, 'sendMessage'])->name('livechat.send');

    // Quản lý người dùng Admin
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulkAction');
    Route::resource('users', AdminUserController::class);

    // Giao dịch thanh toán Admin
    Route::get('/transactions', [PaymentTransactionController::class, 'index'])->name('transactions.index');

    // Quản lý Coupon Admin
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons/bulk-action', [AdminCouponController::class, 'bulkAction'])->name('coupons.bulkAction');
    Route::put('/coupons/{id}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
});


/*
|--------------------------------------------------------------------------
| API ĐỊA CHỈ & PHÍ VẬN CHUYỂN GHN
|--------------------------------------------------------------------------
*/
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
});