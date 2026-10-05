<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Danh sách người dùng (User & Admin)
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders')->latest();

        // Lọc theo vai trò (role: admin/user)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    /**
     * Giao diện tạo người dùng mới
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:admin,user',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên người dùng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã tồn tại trên hệ thống.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm mới người dùng thành công!');
    }

    /**
     * Xem chi tiết thông tin và lịch sử đơn hàng của người dùng
     */
    public function show($id)
    {
        $user = User::with(['orders' => function ($q) {
            $q->latest();
        }, 'orders.items'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Giao diện chỉnh sửa người dùng
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật thông tin người dùng thành công!');
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không thể xóa tài khoản Admin đang đăng nhập!');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Đã xóa tài khoản người dùng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa tài khoản này do có dữ liệu liên quan (đơn hàng, đánh giá...).');
        }
    }

    /**
     * Xử lý hàng loạt tài khoản người dùng (Bulk Delete)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|string',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một người dùng để xử lý hàng loạt.',
        ]);

        if ($request->action === 'delete') {
            // Không xóa tài khoản đang đăng nhập
            $ids = array_diff($request->ids, [Auth::id()]);
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'Không có tài khoản hợp lệ nào được chọn để xóa.');
            }

            $count = 0;
            $failed = 0;
            
            foreach (User::whereIn('id', $ids)->get() as $userToDelete) {
                try {
                    $userToDelete->delete();
                    $count++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }

            if ($count > 0) {
                $msg = "Đã xóa hàng loạt {$count} tài khoản người dùng thành công!";
                if ($failed > 0) {
                    $msg .= " ({$failed} tài khoản không thể xóa do có dữ liệu liên quan)";
                }
                return redirect()->back()->with('success', $msg);
            }

            return redirect()->back()->with('error', 'Không thể xóa các tài khoản đã chọn do có dữ liệu liên quan.');
        }

        return redirect()->back()->with('error', 'Thao tác không hợp lệ.');
    }
}
