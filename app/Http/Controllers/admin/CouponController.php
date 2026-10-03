<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($request->code)),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Tạo mã giảm giá mới thành công!');
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'required|boolean',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
        ]);

        $coupon->update([
            'code' => strtoupper(trim($request->code)),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'expires_at' => $request->expires_at,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin mã giảm giá thành công!');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->back()->with('success', 'Đã xóa mã giảm giá thành công.');
    }

    /**
     * Xử lý hàng loạt mã giảm giá (Bulk Delete)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:coupons,id',
            'action' => 'required|string',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một mã giảm giá để xử lý hàng loạt.',
        ]);

        if ($request->action === 'delete') {
            $count = Coupon::whereIn('id', $request->ids)->delete();
            return redirect()->back()->with('success', "Đã xóa hàng loạt {$count} mã giảm giá thành công!");
        }

        return redirect()->back()->with('error', 'Thao tác không hợp lệ.');
    }
}
