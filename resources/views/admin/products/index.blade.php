@extends('layouts.admin')

@section('content')
<div class="my-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-video text-primary me-2"></i>Quản Lý Danh Sách Máy Chiếu</h3>
            <p class="text-muted small mb-0">Quản lý toàn bộ kho sản phẩm máy chiếu và phụ kiện trên hệ thống.</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-plus me-1"></i> Thêm Máy Chiếu Mới
            </a>
        </div>
    </div>

    <!-- FORM XỬ LÝ HÀNG LOẠT SẢN PHẨM -->
    <form action="{{ route('admin.products.bulkAction') }}" method="POST" id="bulkProductForm">
        @csrf
        <div class="bg-white p-3 rounded-4 border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" id="selectAll" class="form-check-input my-0" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="selectAll" class="form-check-label fw-bold small text-dark cursor-pointer">Chọn tất cả máy chiếu</label>
                <span id="selectedCountBadge" class="badge bg-primary rounded-pill ms-1 d-none">Đã chọn 0 mục</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn XÓA HÀNG LOẠT tất cả sản phẩm máy chiếu đã chọn không?')">
                    <i class="fa-solid fa-trash me-1"></i> Xóa Hàng Loạt Sản Phẩm Được Chọn
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th style="width: 40px;" class="ps-3"></th>
                            <th style="width: 80px;" class="text-center">Hình ảnh</th>
                            <th>Tên máy chiếu</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th>Độ sáng</th>
                            <th class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="item-checkbox form-check-input" style="cursor: pointer;">
                                </td>
                                <td class="text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" width="50" height="50" class="rounded-3 object-fit-contain border">
                                    @else
                                        <span class="badge bg-light text-muted border">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark d-block">{{ $product->name }}</strong>
                                    @if($product->brand)<small class="text-muted"><i class="fa-solid fa-tag me-1"></i>Hãng: {{ $product->brand }}</small>@endif
                                </td>
                                <td><span class="badge bg-info text-dark">{{ $product->category->name ?? 'N/A' }}</span></td>
                                <td class="text-danger fw-extrabold">{{ number_format($product->price) }} đ</td>
                                <td>{{ $product->brightness ? $product->brightness . ' Lumens' : 'N/A' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-warning btn-sm rounded-pill px-2">Sửa</a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa máy chiếu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có máy chiếu nào trong cơ sở dữ liệu.</td></tr>
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
            selectedBadge.innerText = `Đã chọn ${checkedCount} sản phẩm`;
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