<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Trang xem chi tiết máy chiếu cho khách hàng
    public function show(Product $product)
    {
        $product->load(['category', 'reviews.user']);

        // Lấy 4 sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->take(4)
                                  ->get();

        $userWishlisted = false;
        if (Auth::check()) {
            $userWishlisted = Wishlist::where('user_id', Auth::id())
                                      ->where('product_id', $product->id)
                                      ->exists();
        }

        return view('products.show', compact('product', 'relatedProducts', 'userWishlisted'));
    }

    // API tìm kiếm live search trên thanh Header
    public function searchApi(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'price', 'image', 'brand')
            ->take(5)
            ->get();

        return response()->json($products);
    }
}
