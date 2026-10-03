@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-6">
        <div class="mb-3">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small shadow-sm bg-white">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Trang chủ
            </a>
        </div>
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white font-weight-bold text-center">
                <h4>ĐĂNG KÝ TÀI KHOẢN</h4>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nhập họ và tên" required>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label>Mật Khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                    </div>

                    <div class="form-group">
                        <label>Xác Nhận Mật Khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-4 font-weight-bold">ĐĂNG KÝ</button>
                </form>

                <div class="text-center mt-3">
                    <span>Đã có tài khoản? </span><a href="{{ route('login') }}">Đăng nhập ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection