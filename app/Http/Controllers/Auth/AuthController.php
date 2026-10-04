<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'verify' => null,
            'email_verified_at' => null,
        ]);

        event(new Registered($user));

        $request->session()->put('pending_verification_user_id', $user->id);
        $request->session()->put('pending_verification_email', $user->email);

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Xin chào Quản trị viên!');
            }

            if ($user->verify === null && $user->email_verified_at === null) {
                $request->session()->put('pending_verification_user_id', $user->id);
                $request->session()->put('pending_verification_email', $user->email);
                Auth::logout();

                return redirect()
                    ->route('verification.notice')
                    ->with('error', 'Tài khoản chưa xác thực email. Vui lòng xác thực trước khi đăng nhập.');
            }

            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất tài khoản.');
    }

    public function showVerifyEmailNotice(Request $request)
    {
        $pendingEmail = $request->session()->get('pending_verification_email');

        if (!$pendingEmail && Auth::check()) {
            $pendingEmail = Auth::user()->email;
        }

        if (!$pendingEmail) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin xác thực. Vui lòng đăng nhập lại.');
        }

        return view('auth.verify-email', compact('pendingEmail'));
    }

    public function resendVerificationEmail(Request $request)
    {
        $pendingUserId = $request->session()->get('pending_verification_user_id');

        if (!$pendingUserId && Auth::check()) {
            $pendingUserId = Auth::id();
        }

        if (!$pendingUserId) {
            return redirect()->route('login')->with('error', 'Phiên xác thực đã hết hạn. Vui lòng đăng nhập lại.');
        }

        $user = User::find($pendingUserId);

        if (!$user) {
            return redirect()->route('register')->with('error', 'Không tìm thấy tài khoản cần xác thực.');
        }

        if ($user->hasVerifiedEmail() || $user->verify !== null) {
            $request->session()->forget(['pending_verification_user_id', 'pending_verification_email']);

            return redirect()->route('login')->with('success', 'Tài khoản đã được xác thực. Bạn có thể đăng nhập.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Đã gửi lại email xác thực. Vui lòng kiểm tra hộp thư.');
    }

    public function verifyEmail(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            abort(403, 'Liên kết xác thực không hợp lệ.');
        }

        if ($user->verify === null || $user->email_verified_at === null) {
            $user->verify = now();
            $user->email_verified_at = now();
            $user->save();
        }

        $request->session()->forget(['pending_verification_user_id', 'pending_verification_email']);

        return redirect()->route('login')->with('success', 'Xác thực email thành công! Bạn có thể đăng nhập.');
    }
}
