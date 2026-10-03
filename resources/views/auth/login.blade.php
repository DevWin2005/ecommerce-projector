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
            <div class="card-header bg-dark text-white font-weight-bold text-center">
                ĐĂNG NHẬP TÀI KHOẢN
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Địa chỉ Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Mật Khẩu</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ghi nhớ tôi</label>
                    </div>

                    <button type="submit" class="btn btn-warning btn-block font-weight-bold">Đăng nhập</button>
                </form>

                <div class="text-center mt-3">
                    <span>Chưa có tài khoản? </span><a href="{{ route('register') }}">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
