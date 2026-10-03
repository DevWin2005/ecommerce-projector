@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách người dùng
    </a>
    <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-pen text-primary me-2"></i>Chỉnh Sửa Tài Khoản: <span class="text-primary">{{ $user->name }}</span></h3>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Họ và tên -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vai trò -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phân quyền vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select rounded-3 @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Khách Hàng (User)</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Quản Trị Viên (Admin)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Đổi Mật khẩu (Không bắt buộc) -->
                    <div class="col-md-12">
                        <hr class="my-3">
                        <h6 class="fw-bold text-muted small uppercase">Đổi Mật Khẩu (Để trống nếu không đổi)</h6>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Mật khẩu mới</label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror" placeholder="Để trống nếu giữ nguyên">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Nhập lại mật khẩu mới">
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill px-4">Hủy Bỏ</a>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Cập Nhật Thông Tin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
