<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Mail\TempPasswordMail;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetRequestController extends Controller
{
    /**
     * Tampilkan form request reset password.
     */
    public function showRequestForm()
    {
        return view('auth.request-reset');
    }

    /**
     * Admin/User submit request reset password.
     */
    public function submitRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->input('email'))
            ->where('is_active', true)
            ->first();

        // Keamanan: jika email tidak ditemukan, tetap tampilkan pesan sukses
        if (! $user) {
            return redirect()->route('password.request')
                ->with('success', 'Jika email terdaftar, request akan segera diproses oleh Super Admin.');
        }

        // Ambil request aktif terakhir milik user ini
        $existing = PasswordResetRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->latest('id')
            ->first();

        // ── Kasus 1: sudah ada request APPROVED yang masih berlaku ──
        if ($existing && $existing->status === 'approved' && $existing->isOtpValid()) {
            session(['pending_reset_user_id' => $user->id]);

            return redirect()->route('password.request.status', ['email' => $user->email])
                ->with('error', 'Request Anda sudah disetujui. Silakan cek email untuk kode OTP.');
        }

        // ── Kasus 2: ada request PENDING yang sedang di-block (sudah 3x request) ──
        if ($existing && $existing->isPending() && $existing->isBlocked()) {
            session(['pending_reset_user_id' => $user->id]);

            return redirect()->route('password.request.status', ['email' => $user->email])
                ->with('error', 'Anda telah mencapai batas maksimal request ('.PasswordResetRequest::MAX_REQUESTS_PER_CYCLE.'x). Silakan tunggu Super Admin memproses request terakhir Anda.');
        }

        // ── Kasus 3: ada request PENDING yang belum di-block ──
        if ($existing && $existing->isPending() && ! $existing->isBlocked()) {

            // Sub-kasus 3a: temp password sudah dikirim dan MASIH berlaku
            if ($existing->temp_password_sent_at !== null && $existing->isTempPasswordValid()) {
                session(['pending_reset_user_id' => $user->id]);

                return redirect()->route('password.request.status', ['email' => $user->email])
                    ->with('error', 'Password sementara Anda masih berlaku. Silakan login menggunakan password sementara yang telah dikirim ke email Anda.');
            }

            // Sub-kasus 3b: temp password sudah expired ATAU waktu tunggu temp password (2 menit) sudah terlewati
            $tempExpired = $existing->temp_password_sent_at !== null && ! $existing->isTempPasswordValid();
            $waitExpired = $existing->temp_password_sent_at === null
                && $existing->created_at->diffInSeconds(now()) >= PasswordResetRequest::TEMP_PASSWORD_DELAY_SECONDS;

            if ($tempExpired || $waitExpired) {
                // Posisi dalam siklus
                $currentCount = $existing->request_count ?: 1;
                $newCount = $currentCount + 1;

                if ($newCount > PasswordResetRequest::MAX_REQUESTS_PER_CYCLE) {
                    $existing->update(['request_count' => PasswordResetRequest::MAX_REQUESTS_PER_CYCLE]);
                    session(['pending_reset_user_id' => $user->id]);

                    return redirect()->route('password.request.status', ['email' => $user->email])
                        ->with('error', 'Anda telah mencapai batas maksimal request ('.PasswordResetRequest::MAX_REQUESTS_PER_CYCLE.'x). Silakan tunggu Super Admin memproses request terakhir Anda.');
                }

                // Tutup request lama
                $existing->update([
                    'status' => $existing->temp_password_sent_at ? 'auto_reset' : 'rejected',
                ]);

                // Buat request baru dalam siklus yang sama
                PasswordResetRequest::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'request_count' => $newCount,
                    'request_expires_at' => now()->addHours(24),
                ]);

                Cache::forget("poll_status_user_{$user->id}");
                Cache::forget('settings_pending_count');

                session(['pending_reset_user_id' => $user->id]);

                $remaining = PasswordResetRequest::MAX_REQUESTS_PER_CYCLE - $newCount;
                $msg = 'Request baru ('.$newCount.'/'.PasswordResetRequest::MAX_REQUESTS_PER_CYCLE.') berhasil dikirim. Password sementara baru akan dikirim ke email Anda dalam 2 menit jika Super Admin belum merespons.';
                if ($remaining > 0) {
                    $msg .= " (Sisa kesempatan request: {$remaining}x)";
                } else {
                    $msg .= ' Ini adalah request terakhir Anda dalam siklus ini.';
                }

                return redirect()->route('password.request.status', ['email' => $user->email])
                    ->with('success', $msg);
            }

            // Sub-kasus 3c: temp password belum dikirim dan masih dalam masa tunggu (< 2 menit)
            session(['pending_reset_user_id' => $user->id]);

            return redirect()->route('password.request.status', ['email' => $user->email])
                ->with('error', 'Request Anda sedang diproses. Password sementara akan dikirim ke email jika Super Admin tidak merespons dalam 2 menit.');
        }

        // ── Kasus 4: tidak ada request aktif — buat request baru ──
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'request_count' => 1,
            'request_expires_at' => now()->addHours(24),
        ]);

        Cache::forget("poll_status_user_{$user->id}");
        Cache::forget('settings_pending_count');

        session(['pending_reset_user_id' => $user->id]);

        return redirect()->route('password.request.status', ['email' => $user->email])
            ->with('success', 'Request berhasil dikirim. Menunggu persetujuan Super Admin.');
    }

    /**
     * Tampilkan status request.
     */
    public function requestStatus(Request $request)
    {
        $resetRequest = null;

        // Prioritas 1: user sedang login
        if (auth()->check()) {
            $userId = auth()->id();
            $resetRequest = PasswordResetRequest::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->latest('id')
                ->first();
        }

        // Prioritas 2: ada session pending_reset_user_id
        if (! $resetRequest && session('pending_reset_user_id')) {
            $userId = session('pending_reset_user_id');
            $resetRequest = PasswordResetRequest::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->latest('id')
                ->first();
        }

        // Prioritas 3: fallback via email
        if (! $resetRequest && $request->filled('email')) {
            $user = User::where('email', $request->input('email'))
                ->where('is_active', true)
                ->first();
            if ($user) {
                session(['pending_reset_user_id' => $user->id]);
                $resetRequest = PasswordResetRequest::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->latest('id')
                    ->first();
            }
        }

        // Jika approved, simpan di session
        if ($resetRequest && $resetRequest->status === 'approved') {
            session([
                'reset_request_id' => $resetRequest->id,
                'otp_session_expires_at' => $resetRequest->otp_expires_at?->timestamp,
            ]);
        }

        return view('auth.request-status', compact('resetRequest'));
    }

    /**
     * Polling endpoint — return JSON status request untuk auto-redirect & auto-trigger kirim temp password.
     */
    public function pollStatus()
    {
        $userId = auth()->id() ?? session('pending_reset_user_id');

        if (! $userId) {
            return response()->json(['status' => 'none']);
        }

        $cacheKey = "poll_status_user_{$userId}";

        $result = Cache::remember($cacheKey, 5, function () use ($userId) {
            $resetRequest = PasswordResetRequest::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->latest('id')
                ->first();

            if (! $resetRequest) {
                return ['status' => 'none'];
            }

            if ($resetRequest->status === 'approved' && $resetRequest->isOtpValid()) {
                return [
                    'status' => 'approved',
                    'redirect_url' => route('password.verify'),
                    '_request_id' => $resetRequest->id,
                    '_otp_expires' => $resetRequest->otp_expires_at->timestamp,
                ];
            }

            if ($resetRequest->status === 'pending') {
                $sent = $this->trySendTempPassword($resetRequest);

                if ($sent) {
                    Cache::forget("poll_status_user_{$userId}");
                }

                return [
                    'status' => 'pending',
                    'temp_pwd_sent' => $sent,
                ];
            }

            return ['status' => $resetRequest->status];
        });

        if (($result['status'] ?? '') === 'approved') {
            session([
                'reset_request_id' => $result['_request_id'],
                'otp_session_expires_at' => $result['_otp_expires'],
            ]);

            return response()->json([
                'status' => 'approved',
                'redirect_url' => $result['redirect_url'],
            ]);
        }

        return response()->json($result);
    }

    /**
     * Generate dan kirim temp password ke email user jika sudah pending >= 2 menit.
     */
    private function trySendTempPassword(PasswordResetRequest $resetRequest): bool
    {
        if (! $resetRequest->user || ! $resetRequest->user->is_active) {
            return false;
        }

        $now = now();
        $cutoff = $now->copy()->subSeconds(PasswordResetRequest::TEMP_PASSWORD_DELAY_SECONDS);

        $plainPassword = Str::random(12);
        $expiresAt = $now->copy()->addMinutes(PasswordResetRequest::TEMP_PASSWORD_LIFETIME_MINS);

        $affected = DB::table('password_reset_requests')
            ->where('id', $resetRequest->id)
            ->where('status', 'pending')
            ->whereNull('temp_password_sent_at')
            ->where('created_at', '<=', $cutoff)
            ->update([
                'temp_password' => $plainPassword,
                'temp_password_sent_at' => $now,
                'temp_password_expires_at' => $expiresAt,
            ]);

        if ($affected === 0) {
            return false;
        }

        try {
            Mail::to($resetRequest->user->email)
                ->send(new TempPasswordMail($resetRequest->user, $plainPassword));

            Log::info("TempPassword: Sent to {$resetRequest->user->email}, expires {$expiresAt}");

            return true;
        } catch (\Exception $e) {
            Log::error("TempPassword: Failed to send to {$resetRequest->user->email} — {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Tampilkan form verifikasi OTP.
     */
    public function showVerifyOtp()
    {
        if (! session('reset_request_id')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi tidak valid. Silakan ulangi proses.');
        }

        return view('auth.verify-code');
    }

    /**
     * Kirim ulang OTP ke email admin/user.
     */
    public function resendOtp()
    {
        $requestId = session('reset_request_id');
        $resetRequest = PasswordResetRequest::find($requestId);

        if (! $resetRequest || ! $resetRequest->isApproved()) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi tidak valid. Silakan ulangi proses.');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $resetRequest->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(PasswordResetRequest::OTP_SESSION_LIFETIME_MINS),
        ]);

        try {
            Mail::to($resetRequest->user->email)
                ->send(new OtpMail($resetRequest->user, $otp, $resetRequest));
        } catch (\Exception $e) {
            Log::error('Gagal kirim ulang OTP: '.$e->getMessage());
        }

        return redirect()->route('password.verify')
            ->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    /**
     * Verifikasi OTP yang dimasukkan user.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $requestId = session('reset_request_id');
        $resetRequest = PasswordResetRequest::find($requestId);

        if (! $resetRequest || ! $resetRequest->isApproved()) {
            return redirect()->route('password.request')
                ->with('error', 'Request tidak ditemukan atau belum disetujui.');
        }

        if (! $resetRequest->isOtpValid()) {
            return redirect()->route('password.request')
                ->with('error', 'Kode OTP sudah kedaluwarsa. Minta Super Admin untuk approve ulang.');
        }

        if ($resetRequest->otp_code !== $request->input('otp')) {
            return back()->withErrors(['otp' => 'Kode OTP tidak sesuai.']);
        }

        session([
            'otp_verified' => true,
            'reset_request_id' => $resetRequest->id,
            'pending_reset_user_id' => $resetRequest->user_id,
        ]);

        return redirect()->route('password.reset');
    }

    /**
     * Form ubah password baru.
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
     * Proses ganti password baru.
     */
    public function resetPassword(Request $request)
    {
        if (! session('otp_verified') || ! session('reset_request_id')) {
            return redirect()->route('password.verify')
                ->with('error', 'Sesi tidak valid. Silakan ulangi proses.');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[0-9]/',
                'regex:/[A-Z]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.regex' => 'Kata sandi harus memiliki angka, huruf kapital, dan simbol.',
        ]);

        $resetRequest = PasswordResetRequest::find(session('reset_request_id'));

        if (! $resetRequest) {
            return redirect()->route('password.request')
                ->with('error', 'Request tidak ditemukan.');
        }

        $resetRequest->user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        $resetRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
            'otp_code' => null,
        ]);

        session()->forget([
            'otp_verified',
            'reset_request_id',
            'pending_reset_user_id',
            'otp_session_expires_at',
            'is_using_temp_password',
            'temp_password_expires_at',
            'temp_password_request_id',
        ]);

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
    }

    /**
     * User/Admin akses halaman OTP via link di email (tanpa login).
     */
    public function accessViaToken(PasswordResetRequest $resetRequest)
    {
        if (! $resetRequest->isApproved() || ! $resetRequest->isOtpValid()) {
            return redirect()->route('login')
                ->with('error', 'Link tidak valid atau sudah kedaluwarsa.');
        }

        session([
            'reset_request_id' => $resetRequest->id,
            'pending_reset_user_id' => $resetRequest->user_id,
        ]);

        return redirect()->route('password.verify');
    }
}
