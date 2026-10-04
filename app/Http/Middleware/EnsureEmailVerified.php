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

        $user = Auth::user();
        if ($user->verify === null && $user->email_verified_at === null && ! $user->hasVerifiedEmail()) {
            $request->session()->put('pending_verification_user_id', $user->id);
            $request->session()->put('pending_verification_email', $user->email);

            return redirect()
                ->route('verification.notice')
                ->with('error', 'Vui lòng xác thực email để tiếp tục sử dụng chức năng này.');
        }

        return $next($request);
    }
}
