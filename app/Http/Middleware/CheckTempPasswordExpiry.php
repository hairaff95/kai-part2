<?php

namespace App\Http\Middleware;

use App\Models\PasswordResetRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTempPasswordExpiry
{
    /**
     * Cek apakah user sedang login menggunakan temp password yang sudah expired.
     * Jika ya: logout dan redirect ke form request baru.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        // Hanya cek jika user login via temp password
        if (! session('is_using_temp_password')) {
            return $next($request);
        }

        $expiresAt = session('temp_password_expires_at'); // Unix timestamp

        // Jika sudah expired
        if ($expiresAt && now()->timestamp > $expiresAt) {
            $userId = Auth::id();

            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            // Cek apakah user masih punya request aktif
            $activeRequest = PasswordResetRequest::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->latest()
                ->first();

            // AJAX / fetch request -> kembalikan JSON 401
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'temp_password_expired' => true,
                    'message' => 'Password sementara Anda sudah habis masa berlakunya (2 menit).',
                    'redirect' => route('login'),
                ], 401);
            }

            // Jika masih ada request pending dan belum di-block -> arahkan ke halaman status
            if ($activeRequest && $activeRequest->isPending() && ! $activeRequest->isBlocked()) {
                return redirect()->route('password.request.status')
                    ->with('error', 'Password sementara Anda sudah habis (2 menit). Ajukan request baru untuk mendapatkan password sementara baru.');
            }

            // Jika sudah di-block -> arahkan ke halaman status
            if ($activeRequest && $activeRequest->isPending() && $activeRequest->isBlocked()) {
                return redirect()->route('password.request.status')
                    ->with('error', 'Password sementara Anda sudah habis. Anda telah mencapai batas maksimal request. Silakan tunggu Super Admin memproses request terakhir Anda.');
            }

            // Tidak ada request aktif -> kembali ke login
            return redirect()->route('login')
                ->with('temp_password_expired', true)
                ->with('error', 'Password sementara Anda sudah habis masa berlakunya (2 menit). Silakan ajukan request reset password baru.');
        }

        // Sisa waktu sebelum expired -> kirim ke view via header agar JS bisa countdown
        $secondsLeft = max(0, $expiresAt - now()->timestamp);
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Temp-Password-Expires-In', $secondsLeft);
        }

        return $response;
    }
}
