<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'momo/ipn',
            'api/momo/ipn',
            'livechat/*',
            'admin/livechat/*',
        ]);

        // Đăng ký alias 'admin'
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'email.verified' => \App\Http\Middleware\EnsureEmailVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Xử lý khi CSRF token hết hạn (session timeout)
        // Redirect về trang trước đó với thông báo lỗi thân thiện thay vì hiển thị 404/419
        $exceptions->renderable(function (TokenMismatchException $e, Request $request) {
            return redirect()->back()->with('error', 'Phiên làm việc đã hết hạn. Vui lòng thử lại.');
        });
    })->create();