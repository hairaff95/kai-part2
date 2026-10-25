<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Verifikasi Kode — KAI Tracker App</title>

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

    <div class="w-full max-w-[360px] sm:max-w-[380px] text-center">

        {{-- Heading 'Verifikasi Kode' --}}
        <h1 class="text-2xl sm:text-[28px] font-bold text-gray-950 dark:text-white tracking-tight mb-2 sm:mb-2.5">
            Verifikasi Kode
        </h1>

        {{-- Subtitle --}}
        <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-6 sm:mb-8">
            Masukan kode 6 digit yang di kirimkan di email admin
        </p>

        {{-- Form Verifikasi Kode --}}
        <form method="POST" action="{{ route('password.verify.post') }}" id="otp-form">
            @csrf

            {{-- Hidden input untuk OTP yang dikumpulkan dari 6 kotak --}}
            <input type="hidden" name="otp" id="otp-combined">

            {{-- 6 Digit OTP Inputs --}}
            <div class="grid grid-cols-6 gap-2 sm:gap-2.5 mb-3 sm:mb-3.5">
                @for ($i = 1; $i <= 6; $i++)
                    <input
                        type="text"
                        maxlength="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        id="otp-{{ $i }}"
                        class="otp-input w-full h-12 sm:h-13 text-center text-lg sm:text-xl font-bold text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#282A2C] focus:border-[#3285FF] focus:outline-none transition"
                        autocomplete="off"
                    >
                @endfor
            </div>

            @error('otp')
                <p class="mb-3 text-left text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror

            {{-- Resend Code Link --}}
            <div class="text-left text-xs text-gray-500 dark:text-[#9AA0A6] font-normal mb-6 sm:mb-8">
                Tidak mendapat kode?
                <button
                    type="button"
                    onclick="document.getElementById('resend-otp-form').submit()"
                    class="text-[#0066FF] dark:text-[#3B82F6] font-semibold hover:underline transition cursor-pointer ml-0.5"
                >
                    kirim ulang
                </button>
            </div>

            {{-- Submit Button --}}
            <div>
                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer flex items-center justify-center tracking-wide shadow-sm"
                >
                    Verifikasi
                </button>
            </div>

        </form>

        {{-- Hidden Form for Resend OTP --}}
        <form id="resend-otp-form" method="POST" action="{{ route('password.resend-otp') }}" class="hidden">
            @csrf
        </form>

    </div>

    {{-- Script for OTP Inputs Auto Focus & Paste --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs   = document.querySelectorAll('.otp-input');
            const combined = document.getElementById('otp-combined');

            function updateCombined() {
                combined.value = Array.from(inputs).map(i => i.value).join('');
            }

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    if (value.length >= 1) {
                        e.target.value = value[0];
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                    updateCombined();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d+$/.test(pasteData)) {
                        const digits = pasteData.slice(0, inputs.length).split('');
                        digits.forEach((digit, i) => {
                            if (inputs[i]) inputs[i].value = digit;
                        });
                        const nextFocus = Math.min(digits.length, inputs.length - 1);
                        inputs[nextFocus].focus();
                        updateCombined();
                    }
                });
            });

            const firstInput = document.getElementById('otp-1');
            if (firstInput) firstInput.focus();
        });
    </script>

    {{-- Global Toast Notification --}}
    <x-toast />
</body>
</html>
