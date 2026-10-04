<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        
        $query = Product::with('category')->where('is_active', true);
        
        // Tìm kiếm từ khóa
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Lọc theo thương hiệu (Brand)
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Lọc theo độ phân giải (Resolution)
        if ($request->filled('resolution')) {
            $query->where('resolution', 'like', '%' . $request->resolution . '%');
        }

        // Lọc theo khoảng giá (Price Range)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sắp xếp sản phẩm (Sorting)
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->where('is_featured', true)->latest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Danh sách các thương hiệu để lọc
        $brands = Product::whereNotNull('brand')->distinct()->pluck('brand');

        return view('home', compact('products', 'categories', 'brands'));
    }
}
