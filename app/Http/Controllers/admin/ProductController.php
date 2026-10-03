<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Trang danh sách quản lý máy chiếu (trong admin)
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    // Form thêm máy chiếu
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // Lưu máy chiếu mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,avif,heic|max:10240'
        ], [
            'name.required' => 'Vui lòng nhập tên máy chiếu.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'image.max' => 'Dung lượng ảnh vượt quá 10MB.'
        ]);

        $data = $request->only([
            'name', 'category_id', 'brand', 'price', 
            'brightness', 'resolution', 'display_tech'
        ]);

        $data['slug'] = Str::slug($request->name) . '-' . time();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projectors', 'public');
        }

        $specifications = [];
        if ($request->filled('speaker')) {
            $specifications['speaker'] = $request->speaker;
        }
        if ($request->filled('ports')) {
            $specifications['ports'] = $request->ports;
        }
        if (!empty($specifications)) {
            $data['specifications'] = $specifications;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Thêm máy chiếu mới thành công!');
    }

    // Form sửa máy chiếu
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Cập nhật máy chiếu
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,avif,heic|max:10240'
        ]);

        $data = $request->only([
            'name', 'category_id', 'brand', 'price', 
            'brightness', 'resolution', 'display_tech'
        ]);

        if ($product->name !== $request->name) {
            $data['slug'] = Str::slug($request->name) . '-' . time();
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('projectors', 'public');
        }

        $specifications = $product->specifications ?? [];
        if ($request->filled('speaker')) {
            $specifications['speaker'] = $request->speaker;
        } else {
            unset($specifications['speaker']);
        }

        if ($request->filled('ports')) {
            $specifications['ports'] = $request->ports;
        } else {
            unset($specifications['ports']);
        }

        $data['specifications'] = !empty($specifications) ? $specifications : null;

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật thông tin máy chiếu thành công!');
    }

    // Xóa máy chiếu
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Đã xóa máy chiếu thành công!');
    }

    /**
     * Xử lý hàng loạt sản phẩm (Bulk Delete)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'action' => 'required|string',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một sản phẩm để xử lý hàng loạt.',
        ]);

        if ($request->action === 'delete') {
            $products = Product::whereIn('id', $request->ids)->get();
            foreach ($products as $prod) {
                if ($prod->image && Storage::disk('public')->exists($prod->image)) {
                    Storage::disk('public')->delete($prod->image);
                }
                $prod->delete();
            }
            $count = count($products);
            return redirect()->back()->with('success', "Đã xóa hàng loạt {$count} sản phẩm máy chiếu thành công!");
        }

        return redirect()->back()->with('error', 'Thao tác không hợp lệ.');
    }
}
