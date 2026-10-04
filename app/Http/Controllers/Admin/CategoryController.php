<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->only('name'));
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only('name'));
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục thành công!');
    }

    /**
     * Xử lý hàng loạt danh mục (Bulk Delete)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
            'action' => 'required|string',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một danh mục để xử lý hàng loạt.',
        ]);

        if ($request->action === 'delete') {
            $count = Category::whereIn('id', $request->ids)->delete();
            return redirect()->back()->with('success', "Đã xóa hàng loạt {$count} danh mục sản phẩm thành công!");
        }

        return redirect()->back()->with('error', 'Thao tác không hợp lệ.');
    }
}
