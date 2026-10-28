<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Ubah Kata Sandi Baru — KAI Tracker App</title>

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

    <div class="w-full max-w-[360px] sm:max-w-[380px]">

        {{-- Heading 'Ubah Kata Sandi Baru' --}}
        <h1 class="text-2xl sm:text-[28px] font-bold text-gray-950 dark:text-white tracking-tight mb-2 sm:mb-2.5">
            Ubah Kata Sandi Baru
        </h1>

        {{-- Subtitle --}}
        <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-6 sm:mb-8">
            Buat kata sandi baru yang kuat untuk mengamankan akun Anda.
        </p>

        {{-- Form Ubah Kata Sandi --}}
        <form method="POST" action="{{ route('password.reset.post') }}" class="space-y-3.5 sm:space-y-4" id="reset-password-form">
            @csrf

            {{-- 1. Kata Sandi Baru --}}
            <div>
                <label for="new-password" class="block text-xs font-semibold text-gray-700 dark:text-white mb-1.5 lg:mb-2">
                    Kata Sandi Baru
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                        <x-icon name="icon-kunci-login" class="w-5 h-5 block shrink-0" />
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="new-password"
                        placeholder="* * * * * * * *"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#282A2C] py-2.5 sm:py-3 pl-11 pr-11 text-xs sm:text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#3285FF] focus:outline-none transition"
                        oninput="validatePasswordRules()"
                        required
                    >
                    <button
                        type="button"
                        onclick="toggleVisibility('new-password', 'eye-new-off', 'eye-new-on')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition cursor-pointer"
                        title="Lihat kata sandi"
                    >
                        <span id="eye-new-off" class="flex items-center justify-center">
                            <x-icon name="icon-show-kunci-login" class="w-5 h-5" />
                        </span>
                        <span id="eye-new-on" class="hidden flex items-center justify-center">
                            <x-icon name="off-show-kunci-login" class="w-5 h-5" />
                        </span>
                    </button>
                </div>
                <p id="password-error-msg" class="hidden text-xs text-red-500 dark:text-red-400 mt-1.5 font-medium">
                    Silakan penuhi semua kriteria untuk membuat kata sandi yang aman.
                </p>
                @error('password')
                    <p class="text-xs text-red-500 dark:text-red-400 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2. Konfirmasi Kata Sandi --}}
            <div>
                <label for="confirm-password" class="block text-xs font-semibold text-gray-700 dark:text-white mb-1.5 lg:mb-2">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                        <x-icon name="icon-kunci-login" class="w-5 h-5 block shrink-0" />
                    </span>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="confirm-password"
                        placeholder="* * * * * * * *"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#282A2C] py-2.5 sm:py-3 pl-11 pr-11 text-xs sm:text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#3285FF] focus:outline-none transition"
                        oninput="validatePasswordRules()"
                        required
                    >
                    <button
                        type="button"
                        onclick="toggleVisibility('confirm-password', 'eye-confirm-off', 'eye-confirm-on')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition cursor-pointer"
                        title="Lihat kata sandi"
                    >
                        <span id="eye-confirm-off" class="flex items-center justify-center">
                            <x-icon name="icon-show-kunci-login" class="w-5 h-5" />
                        </span>
                        <span id="eye-confirm-on" class="hidden flex items-center justify-center">
                            <x-icon name="off-show-kunci-login" class="w-5 h-5" />
                        </span>
                    </button>
                </div>
                <p id="confirm-error-msg" class="hidden text-xs text-red-500 dark:text-red-400 mt-1.5 font-medium">
                    Konfirmasi kata sandi tidak cocok.
                </p>
            </div>

            {{-- 3. Password Rules --}}
            <div class="pt-1">
                <p class="text-xs text-gray-600 dark:text-gray-300 font-normal mb-2">
                    Silakan ikuti aturan untuk membuat kata sandi yang aman.
                </p>
                <ul class="space-y-1.5 text-xs text-gray-500 dark:text-[#9AA0A6] font-normal" id="rules-list">
                    <li id="rule-length" class="flex items-center gap-2 transition-colors">
                        <span class="rule-dot w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600 shrink-0"></span>
                        <span class="rule-text">Minimal 8 karakter</span>
                    </li>
                    <li id="rule-number" class="flex items-center gap-2 transition-colors">
                        <span class="rule-dot w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600 shrink-0"></span>
                        <span class="rule-text">Memiliki minimal satu angka</span>
                    </li>
                    <li id="rule-capital" class="flex items-center gap-2 transition-colors">
                        <span class="rule-dot w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600 shrink-0"></span>
                        <span class="rule-text">Memiliki minimal satu huruf kapital</span>
                    </li>
                    <li id="rule-special" class="flex items-center gap-2 transition-colors">
                        <span class="rule-dot w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600 shrink-0"></span>
                        <span class="rule-text">Memiliki minimal satu spesial karakter</span>
                    </li>
                </ul>
            </div>

            {{-- 4. Submit Button --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer flex items-center justify-center tracking-wide shadow-sm">
                    Ubah Kata Sandi
                </button>
            </div>

        </form>

    </div>

    <script>
        function toggleVisibility(inputId, offId, onId) {
            const input = document.getElementById(inputId);
            const off = document.getElementById(offId);
            const on = document.getElementById(onId);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                off?.classList.toggle('hidden');
                on?.classList.toggle('hidden');
            }
        }

        function validatePasswordRules() {
            const val = document.getElementById('new-password').value;
            const confirmVal = document.getElementById('confirm-password').value;

            const hasLength = val.length >= 8;
            const hasNumber = /\d/.test(val);
            const hasCapital = /[A-Z]/.test(val);
            const hasSpecial = /[^A-Za-z0-9]/.test(val);

            updateRuleItem('rule-length', hasLength, val.length > 0);
            updateRuleItem('rule-number', hasNumber, val.length > 0);
            updateRuleItem('rule-capital', hasCapital, val.length > 0);
            updateRuleItem('rule-special', hasSpecial, val.length > 0);

            const allValid = hasLength && hasNumber && hasCapital && hasSpecial;
            const errorMsg = document.getElementById('password-error-msg');
            const confirmErrorMsg = document.getElementById('confirm-error-msg');

            if (val.length > 0 && !allValid) {
                errorMsg?.classList.remove('hidden');
            } else {
                errorMsg?.classList.add('hidden');
            }

            if (confirmVal.length > 0 && confirmVal !== val) {
                confirmErrorMsg?.classList.remove('hidden');
            } else {
                confirmErrorMsg?.classList.add('hidden');
            }
        }

        function updateRuleItem(elementId, isValid, isDirty) {
            const el = document.getElementById(elementId);
            if (!el) return;
            const dot = el.querySelector('.rule-dot');

            if (isValid) {
                el.className = "flex items-center gap-2 text-xs text-green-600 dark:text-green-400 font-medium transition-colors";
                if (dot) dot.className = "rule-dot w-1.5 h-1.5 rounded-full bg-green-500 dark:bg-green-400 shrink-0";
            } else if (isDirty) {
                el.className = "flex items-center gap-2 text-xs text-red-500 dark:text-red-400 font-normal transition-colors";
                if (dot) dot.className = "rule-dot w-1.5 h-1.5 rounded-full bg-red-500 dark:bg-red-400 shrink-0";
            } else {
                el.className = "flex items-center gap-2 text-xs text-gray-500 dark:text-[#9AA0A6] font-normal transition-colors";
                if (dot) dot.className = "rule-dot w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600 shrink-0";
            }
        }
    </script>

    {{-- Global Toast Notification --}}
    <x-toast />
</body>
</html>