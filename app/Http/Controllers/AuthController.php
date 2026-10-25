<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login    = trim($request->input('login'));
        $password = $request->input('password');

        // Cek apakah input berupa email atau username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field     => $login,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()
                    ->withInput($request->only('login'))
                    ->withErrors(['login' => 'Akun Anda dinonaktifkan. Hubungi Administrator.']);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => 'Email/username atau kata sandi tidak sesuai.']);
    }

    /**
     * Handle proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
