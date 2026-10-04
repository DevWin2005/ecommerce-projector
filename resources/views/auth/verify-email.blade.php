@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-4 py-3">
    <div class="col-md-7 col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-body p-4 p-md-5 text-center">
                
                <!-- Email Icon -->
                <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 76px; height: 76px;">
                    <i class="fa-regular fa-paper-plane fa-2x"></i>
                </div>

                <!-- Title -->
                <h3 class="fw-bold mb-2 text-dark">Xác Thực Email Tài Khoản</h3>

                <!-- Subtitle / Email Address -->
                <p class="text-muted mb-1 fs-6">
                    Chúng tôi đã gửi liên kết xác thực đến địa chỉ:
                </p>
                <div class="p-2 px-3 bg-light rounded-3 d-inline-block text-primary fw-bold mb-3 border">
                    <i class="fa-regular fa-envelope me-1"></i>
                    {{ $pendingEmail ?? (Auth::check() ? Auth::user()->email : '') }}
                </div>

                <p class="text-secondary small mb-4">
                    Vui lòng mở hộp thư Hộp thư đến (hoặc Spam/Junk) và nhấn vào liên kết xác thực để hoàn tất đăng ký và sử dụng dịch vụ.
                </p>

                <!-- Alerts -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 text-start small fw-bold mb-4 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 text-start small fw-bold mb-4 shadow-sm" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Resend Email Action -->
                <form method="POST" action="{{ route('verification.resend') }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5 fw-bold rounded-3 shadow-sm">
                        <i class="fa-solid fa-rotate-right me-1"></i> Gửi lại email xác thực
                    </button>
                </form>

                <!-- Logout / Login Action -->
                @if (Auth::check())
                    <form method="POST" action="{{ route('logout') }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100 py-2.5 fw-bold rounded-3">
                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 py-2.5 fw-bold rounded-3 text-decoration-none d-block mb-3">
                        <i class="fa-regular fa-user me-1"></i> Đăng nhập tài khoản khác
                    </a>
                @endif

                <!-- Back to Home -->
                <div class="mt-4 pt-2 border-top">
                    <a href="{{ route('home') }}" class="text-decoration-none small text-muted fw-bold">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại trang chủ
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
