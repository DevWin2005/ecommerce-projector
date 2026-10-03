<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Xem trang giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        $selectedIds = session()->get('selected_cart_items', null);

        // Mặc định chọn tất cả sản phẩm nếu chưa có cấu hình trong session
        if ($selectedIds === null) {
            $selectedIds = array_keys($cart);
            session()->put('selected_cart_items', $selectedIds);
        }

        $total = 0;
        $selectedTotal = 0;

        foreach ($cart as $id => $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $total += $itemTotal;
            if (in_array((string)$id, array_map('strval', $selectedIds))) {
                $selectedTotal += $itemTotal;
            }
        }

        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->get();

        return view('cart.index', compact('cart', 'selectedIds', 'total', 'selectedTotal', 'availableCoupons'));
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart(Request $request, int $id)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản Quản trị viên không thể mua hàng.'
                ], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản Quản trị viên không thể mua hàng.');
        }

        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $qty = max(1, (int)$request->input('quantity', 1));
        $effectivePrice = ($product->sale_price && $product->sale_price > 0) ? $product->sale_price : $product->price;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'quantity' => $qty,
                'price' => $effectivePrice,
                'image' => $product->image,
                'brand' => $product->brand,
            ];
        }

        session()->put('cart', $cart);

        // Khi thêm sản phẩm mới vào giỏ, tự động chọn sản phẩm này
        $selectedIds = session()->get('selected_cart_items', array_keys($cart));
        if (!in_array((string)$id, array_map('strval', $selectedIds))) {
            $selectedIds[] = $id;
        }
        session()->put('selected_cart_items', array_values($selectedIds));

        $cartCount = array_sum(array_column($cart, 'quantity'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm "' . $product->name . '" vào giỏ hàng thành công!',
                'cartCount' => $cartCount
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
    }

    // Cập nhật lựa chọn sản phẩm thanh toán
    public function updateSelection(Request $request)
    {
        $rawSelected = $request->input('selected_items', []);
        $selectedIds = is_array($rawSelected) ? array_map('intval', $rawSelected) : [];

        session()->put('selected_cart_items', $selectedIds);

        // Tính lại mã giảm giá nếu đã áp dụng
        $cart = session()->get('cart', []);
        $selectedSubtotal = 0;
        foreach ($cart as $id => $item) {
            if (in_array((string)$id, array_map('strval', $selectedIds))) {
                $selectedSubtotal += $item['price'] * $item['quantity'];
            }
        }

        $appliedCoupon = session()->get('applied_coupon', null);
        $discount = 0;
        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon['code'])->where('is_active', true)->first();
            if ($coupon && $selectedSubtotal >= $coupon->min_order_amount) {
                if ($coupon->type === 'percent') {
                    $discount = ($selectedSubtotal * $coupon->value) / 100;
                } else {
                    $discount = $coupon->value;
                }
                session()->put('applied_coupon', [
                    'code' => $coupon->code,
                    'discount' => $discount,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                ]);
            } else {
                session()->forget('applied_coupon');
                $discount = 0;
            }
        }

        $finalTotal = max(0, $selectedSubtotal - $discount);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'selectedCount' => count($selectedIds),
                'selectedSubtotal' => $selectedSubtotal,
                'discount' => $discount,
                'finalTotal' => $finalTotal,
            ]);
        }

        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
        }

        return redirect()->route('checkout.index');
    }

    // Cập nhật số lượng
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);
            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = max(1, (int)$request->quantity);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Cập nhật số lượng giỏ hàng thành công!');
        }
    }

    // Xóa khỏi giỏ hàng
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        $selectedIds = session()->get('selected_cart_items', []);
        $selectedIds = array_diff($selectedIds, [$id]);
        session()->put('selected_cart_items', array_values($selectedIds));

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}
