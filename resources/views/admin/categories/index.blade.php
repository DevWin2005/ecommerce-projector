@extends('layouts.admin')

@section('content')
<div class="my-4">
    <!-- Thanh tiêu đề và các nút điều hướng -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Quản Lý Danh Mục Máy Chiếu</h3>
            <p class="text-muted small mb-0">Quản lý và phân loại các nhóm sản phẩm máy chiếu.</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-plus me-1"></i> Thêm Danh Mục Mới
            </a>
        </div>
    </div>

    <!-- FORM XỬ LÝ HÀNG LOẠT DANH MỤC -->
    <form action="{{ route('admin.categories.bulkAction') }}" method="POST" id="bulkCategoryForm">
        @csrf
        <div class="bg-white p-3 rounded-4 border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" id="selectAll" class="form-check-input my-0" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="selectAll" class="form-check-label fw-bold small text-dark cursor-pointer">Chọn tất cả danh mục</label>
                <span id="selectedCountBadge" class="badge bg-primary rounded-pill ms-1 d-none">Đã chọn 0 mục</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn XÓA HÀNG LOẠT tất cả danh mục đã chọn không?')">
                    <i class="fa-solid fa-trash me-1"></i> Xóa Hàng Loạt Danh Mục Được Chọn
                </button>
            </div>
        </div>

        <!-- Bảng danh sách danh mục -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle bg-white">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th style="width: 40px;" class="ps-3"></th>
                            <th style="width: 80px;" class="text-center">ID</th>
                            <th>Tên Danh Mục</th>
                            <th style="width: 220px;" class="text-center">Số lượng máy chiếu</th>
                            <th style="width: 180px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="item-checkbox form-check-input" style="cursor: pointer;">
                                </td>
                                <td class="text-center fw-bold">#{{ $category->id }}</td>
                                <td>
                                    <strong class="text-primary">{{ $category->name }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                        {{ $category->products_count ?? $category->products->count() }} sản phẩm
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm font-weight-bold rounded-pill px-3">
                                        Sửa
                                    </a>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Bạn có chắc muốn xóa danh mục [{{ $category->name }}]? Các sản phẩm thuộc danh mục này có thể bị ảnh hưởng.')">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <em>Chưa có danh mục nào trong hệ thống. Hãy bấm nút "+ Thêm Danh Mục Mới" để tạo.</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedBadge = document.getElementById('selectedCountBadge');

    function updateBadge() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            selectedBadge.innerText = `Đã chọn ${checkedCount} danh mục`;
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