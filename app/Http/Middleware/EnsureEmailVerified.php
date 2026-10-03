<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->verify === null) {
            $request->session()->put('pending_verification_user_id', Auth::id());
            $request->session()->put('pending_verification_email', Auth::user()->email);

            return redirect()
                ->route('verification.notice')
                ->with('error', 'Vui lòng xác thực email để tiếp tục sử dụng chức năng này.');
        }

        return $next($request);
    }
}
