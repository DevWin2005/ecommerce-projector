<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực email</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>Chào {{ $user->name }},</h2>
    <p>Cảm ơn bạn đã đăng ký tài khoản tại PROJECTOR SHOP.</p>
    <p>Vui lòng nhấn vào nút bên dưới để xác thực email:</p>

    <p>
        <a href="{{ $verificationUrl }}"
           style="display: inline-block; padding: 10px 18px; background: #0ea5e9; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold;">
            Xác thực email
        </a>
    </p>

    <p>Liên kết này sẽ hết hạn sau 60 phút.</p>
    <p>Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.</p>
</body>
</html>
