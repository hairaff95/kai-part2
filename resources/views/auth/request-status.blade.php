<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Status Permintaan — KAI Tracker App</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Auto Theme Script (WIB 17:00 - 07:00 Auto Dark Mode) -->
    <x-theme-script />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] } } } }</script>
</head>

<body class="min-h-screen bg-white dark:bg-[#1F2123] font-sans antialiased text-gray-900 dark:text-white selection:bg-blue-100 selection:text-[#0066FF] flex flex-col justify-center items-center px-4 py-8 sm:py-12 transition-colors duration-200">

    <div class="w-full max-w-[360px] sm:max-w-[400px]">

        {{-- Illustration --}}
        <div class="flex justify-center mb-6 sm:mb-8">
            <x-icon name="asset-status-permintaan" class="w-full max-w-[280px] sm:max-w-[320px] h-auto" />
        </div>

        {{-- Heading --}}
        <h1 class="text-2xl sm:text-[28px] font-bold text-gray-950 dark:text-white tracking-tight mb-2 sm:mb-2.5">
            Status Permintaan
        </h1>

        @if($resetRequest)

            {{-- ── STATUS: PENDING ────────────────────────────────────────────────── --}}
            @if($resetRequest->status === 'pending')
                @php
                    $isBlocked = $resetRequest->isBlocked();
                @endphp

                {{-- Subtitle --}}
                <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-5 sm:mb-6">
                    Permintaan Anda sedang dalam antrian. Super Admin akan mereview dan menyetujui permintaan ini.
                </p>

                {{-- Tombol aksi --}}
                <a href="{{ route('password.verify', array_filter(['email' => request('email')])) }}"
                    class="w-full inline-flex items-center justify-center rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer tracking-wide shadow-sm mb-5 sm:mb-6">
                    Verifikasi Sekarang
                </a>

                {{-- Teks Bawah: Permintaan Ulang --}}
                <p class="text-center text-xs text-gray-500 dark:text-[#9AA0A6]">
                    Tidak mendapatkan kode? <a href="{{ route('password.request') }}" class="text-[#0066FF] dark:text-[#3B82F6] hover:underline font-medium">Ajukan Permintaan ulang</a>
                </p>

            {{-- ── STATUS: APPROVED ───────────────────────────────────────────────── --}}
            @elseif($resetRequest->status === 'approved')

                <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-5 sm:mb-6">
                    ✓ Request Anda telah disetujui. Kode OTP telah dikirim ke email Anda.
                    @if($resetRequest->otp_expires_at)
                        Berlaku hingga <strong>{{ $resetRequest->otp_expires_at->format('H:i') }} WIB</strong>.
                    @endif
                </p>

                {{-- Tampilkan OTP langsung --}}
                @if($resetRequest->otp_code)
                    <div class="bg-white dark:bg-[#282A2C] border-2 border-[#0066FF] dark:border-[#3B82F6] rounded-2xl p-5 mb-5 text-center">
                        <p class="text-xs font-semibold text-gray-500 dark:text-[#9AA0A6] uppercase tracking-wider mb-3">Kode OTP Anda</p>
                        <div class="flex items-center justify-center gap-2 mb-2">
                            @foreach(str_split($resetRequest->otp_code) as $digit)
                                <div class="w-10 h-12 flex items-center justify-center rounded-xl border-2 border-[#0066FF] dark:border-[#3B82F6] bg-blue-50 dark:bg-blue-950/30 text-xl font-bold text-[#0066FF] dark:text-[#3B82F6]">
                                    {{ $digit }}
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 dark:text-[#9AA0A6]">Jangan bagikan kode ini ke siapapun.</p>
                    </div>
                @endif

                <a href="{{ route('password.verify') }}"
                    class="w-full inline-flex items-center justify-center rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer tracking-wide shadow-sm mb-5 sm:mb-6">
                    Masukkan Kode OTP →
                </a>

            {{-- ── STATUS: LAINNYA (rejected, completed, auto_reset) ──────────────── --}}
            @else

                @if($resetRequest->status === 'rejected')
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-5 sm:mb-6">
                        ✕ Request reset password Anda ditolak oleh Super Admin. Silakan hubungi Super Admin untuk informasi lebih lanjut.
                    </p>
                @elseif($resetRequest->status === 'completed')
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-5 sm:mb-6">
                        ✓ Proses reset password telah selesai. Silakan login dengan password baru Anda.
                    </p>
                @elseif($resetRequest->status === 'auto_reset')
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-5 sm:mb-6">
                        ℹ Sistem telah mengirimkan password sementara ke email Anda karena Super Admin belum merespons dalam 2 menit.
                    </p>
                @endif

                <a href="{{ route('login') }}"
                    class="w-full inline-flex items-center justify-center rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer tracking-wide shadow-sm mb-5 sm:mb-6">
                    Kembali ke Halaman Masuk
                </a>

            @endif

        @else
            {{-- Tidak ada request aktif --}}
            <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-5 sm:mb-6">
                Anda belum memiliki request reset password yang sedang diproses.
            </p>
            <a href="{{ route('password.request') }}"
                class="w-full inline-flex items-center justify-center rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer tracking-wide shadow-sm mb-5 sm:mb-6">
                Buat Request Reset Password
            </a>
        @endif

    </div>

    {{-- Global Toast Notification --}}
    <x-toast />

    {{-- Scripts: auto-polling --}}
    @if(isset($resetRequest) && $resetRequest && $resetRequest->status === 'pending')
    <script>
        // Auto-polling setiap 10 detik
        const pollInterval = setInterval(async () => {
            try {
                const res  = await fetch('{{ route('password.request.poll') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'approved' && data.redirect_url) {
                    clearInterval(pollInterval);
                    if (window.showToast) {
                        window.showToast('Permintaan disetujui! Mengalihkan ke halaman verifikasi...', 'success', 3000);
                    }
                    setTimeout(() => window.location.href = data.redirect_url, 1200);
                    return;
                }

                if (data.temp_pwd_sent === true) {
                    window.location.reload();
                }
            } catch (e) {
                // Abaikan error jaringan
            }
        }, 10000);

        // Auto-reload saat temp password expired
        @if($resetRequest->temp_password_sent_at && $resetRequest->isTempPasswordValid())
        const tempExpAt     = {{ $resetRequest->temp_password_expires_at->timestamp }};
        const msUntilExpiry = (tempExpAt - Math.floor(Date.now() / 1000)) * 1000;
        if (msUntilExpiry > 0) {
            setTimeout(() => window.location.reload(), msUntilExpiry + 500);
        }
        @endif
    </script>
    @endif

</body>
</html>