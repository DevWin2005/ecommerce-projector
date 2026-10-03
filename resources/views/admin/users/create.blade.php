@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách người dùng
    </a>
    <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-plus text-primary me-2"></i>Thêm Tài Khoản Người Dùng Mới</h3>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Họ và tên -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Họ và tên người dùng <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nhập họ và tên..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nhapemail@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vai trò -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phân quyền vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select rounded-3 @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Khách Hàng (User)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản Trị Viên (Admin)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mật khẩu -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror" placeholder="Tối thiểu 6 ký tự" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Xác nhận mật khẩu -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Nhập lại mật khẩu" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill px-4">Hủy Bỏ</a>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-check me-1"></i> Lưu Tài Khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
