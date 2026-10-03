@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white text-center font-weight-bold">
                XÁC THỰC EMAIL
            </div>
            <div class="card-body p-4">
                <p class="mb-2">Tài khoản <strong>{{ $pendingEmail }}</strong> chưa được xác thực.</p>
                <p class="text-muted mb-4">Vui lòng kiểm tra email để bấm vào liên kết xác thực. Nếu chưa nhận được, bạn có thể gửi lại email xác thực.</p>

                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-info btn-block font-weight-bold">Gửi lại email xác thực</button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}">Quay lại trang đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
