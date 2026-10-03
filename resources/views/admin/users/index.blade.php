@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-users-gear text-primary me-2"></i>Quản Lý Người Dùng & Thành Viên</h3>
        <p class="text-muted small mb-0">Xem, tạo mới, chỉnh sửa thông tin và quản lý phân quyền thành viên hệ thống.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-bold btn-sm rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Thêm Người Dùng Mới
        </a>
    </div>
</div>

<!-- TAB LỌC THEO VAI TRÒ (ROLE) -->
<div class="nav nav-pills bg-white p-2 rounded-4 border shadow-sm mb-4 gap-1 flex-wrap">
    <a href="{{ route('admin.users.index') }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ !request('role') ? 'active bg-primary text-white' : 'text-dark' }}">
        Tất cả người dùng <span class="badge {{ !request('role') ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $counts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'admin'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('role') == 'admin' ? 'active bg-purple text-white style-purple' : 'text-dark' }}">
        <i class="fa-solid fa-shield-halved me-1"></i> Quản Trị Viên (Admin) <span class="badge bg-light text-dark border ms-1">{{ $counts['admin'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'user'])) }}" 
       class="nav-link px-3 py-2 rounded-3 small fw-bold {{ request('role') == 'user' ? 'active bg-info text-dark' : 'text-dark' }}">
        <i class="fa-solid fa-user me-1"></i> Khách Hàng (User) <span class="badge bg-light text-dark border ms-1">{{ $counts['user'] ?? 0 }}</span>
    </a>
</div>

<!-- BỘ LỌC TÌM KIẾM -->
<div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Tìm theo Họ tên hoặc Email..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select form-select-sm rounded-3">
                <option value="">-- Tất cả vai trò --</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Khách Hàng (User)</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản Trị Viên (Admin)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-3">Tìm Kiếm</button>
        </div>
        @if(request('search') || request('role'))
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-3">Xóa lọc</a>
            </div>
        @endif
    </form>
</div>

<!-- FORM XỬ LÝ HÀNG LOẠT NGƯỜI DÙNG -->
<form action="{{ route('admin.users.bulkAction') }}" method="POST" id="bulkUserForm">
    @csrf
    <div class="bg-white p-3 rounded-4 border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="selectAll" class="form-check-input my-0" style="width: 18px; height: 18px; cursor: pointer;">
            <label for="selectAll" class="form-check-label fw-bold small text-dark cursor-pointer">Chọn tất cả người dùng</label>
            <span id="selectedCountBadge" class="badge bg-primary rounded-pill ms-1 d-none">Đã chọn 0 mục</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn XÓA HÀNG LOẠT các tài khoản người dùng đã chọn?')">
                <i class="fa-solid fa-trash me-1"></i> Xóa Hàng Loạt Tài Khoản Được Chọn
            </button>
        </div>
    </div>

    <!-- BẢNG DANH SÁCH NGƯỜI DÙNG -->
    <div class="bg-white rounded-4 border shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-3" style="width: 40px;"></th>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Xác thực</th>
                        <th>Số đơn hàng</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="ps-3">
                                @if($u->id !== Auth::id())
                                    <input type="checkbox" name="ids[]" value="{{ $u->id }}" class="item-checkbox form-check-input" style="cursor: pointer;">
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-muted fw-bold">#{{ $u->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">{{ $u->name }}</strong>
                                        @if($u->id === Auth::id())
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 10px;">Bạn</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace text-dark">{{ $u->email }}</td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge bg-danger text-uppercase px-2 py-1"><i class="fa-solid fa-shield-halved me-1"></i> Admin</span>
                                @else
                                    <span class="badge bg-info text-dark text-uppercase px-2 py-1"><i class="fa-solid fa-user me-1"></i> Khách Hàng</span>
                                @endif
                            </td>
                            <td>
                                @if($u->verify || $u->email_verified_at)
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Đã xác thực</span>
                                @else
                                    <span class="badge bg-secondary text-dark bg-opacity-10 border"><i class="fa-regular fa-clock me-1"></i> Chưa xác thực</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary rounded-pill px-3">{{ $u->orders_count ?? 0 }} đơn</span>
                            </td>
                            <td class="text-muted">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-3">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" title="Xem chi tiết & lịch sử mua hàng">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA tài khoản {{ $u->name }} không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Xóa người dùng">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">Không tìm thấy người dùng nào phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 bg-light border-top">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedBadge = document.getElementById('selectedCountBadge');

    function updateBadge() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            selectedBadge.innerText = `Đã chọn ${checkedCount} tài khoản`;
            selectedBadge.classList.remove('d-none');
        } else {
            selectedBadge.classList.add('d-none');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBadge();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked && selectAll) {
                selectAll.checked = false;
            }
            updateBadge();
        });
    });
});
</script>
@endsection
