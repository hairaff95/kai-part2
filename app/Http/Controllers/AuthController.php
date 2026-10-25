<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Jika sudah login, redirect ke dashboard.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle proses login (Email atau Username).
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login = trim($request->input('login'));
        $password = $request->input('password');

        // Cek apakah input berupa email atau username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field => $login,
            'password' => $password,
        ];

        // 1. Coba login kredensial biasa
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();

                return back()
                    ->withInput($request->only('login'))
                    ->withErrors(['login' => 'Akun Anda dinonaktifkan. Hubungi Administrator.']);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        // 2. Cek apakah menggunakan Password Sementara (Temp Password) yang masih berlaku
        $user = User::where($field, $login)
            ->where('is_active', true)
            ->first();

        if ($user) {
            $resetRequest = PasswordResetRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereNotNull('temp_password')
                ->whereNotNull('temp_password_expires_at')
                ->where('temp_password_expires_at', '>', now())
                ->latest('temp_password_sent_at')
                ->first();

            if ($resetRequest && hash_equals($resetRequest->temp_password, $password)) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                // Simpan flag & waktu expired di session
                session([
                    'is_using_temp_password' => true,
                    'temp_password_expires_at' => $resetRequest->temp_password_expires_at->timestamp,
                    'temp_password_request_id' => $resetRequest->id,
                ]);

                return redirect()->intended(route('dashboard'));
            }
        }

        return back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => 'Email/username atau kata sandi tidak sesuai.']);
    }

    /**
     * Tampilkan form verifikasi kode OTP.
     */
    public function showVerifyCode()
    {
        if (! session('reset_request_id')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi tidak valid. Silakan ulangi proses.');
        }

        return view('auth.verify-code');
    }

    /**
     * Tampilkan form ubah kata sandi baru.
     */
    public function showResetPassword()
    {
        if (! session('otp_verified')) {
            return redirect()->route('password.verify')
                ->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }

        return view('auth.reset-password');
    }

    /**
     * Handle submit form ubah kata sandi baru.
     */
    public function resetPassword(Request $request)
    {
        return app(PasswordResetRequestController::class)->resetPassword($request);
    }

    /**
     * Handle proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        session()->forget([
            'otp_verified',
            'reset_request_id',
            'pending_reset_user_id',
            'otp_session_expires_at',
            'is_using_temp_password',
            'temp_password_expires_at',
            'temp_password_request_id',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
