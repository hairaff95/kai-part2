<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Masuk — KAI Tracker App</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Auto Theme Script (WIB 17:00 - 07:00 Auto Dark Mode) -->
    <x-theme-script />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        @media (min-width: 1024px) {
            .blue-curved-banner {
                clip-path: url(#blue-curve-clip);
                -webkit-clip-path: url(#blue-curve-clip);
            }
        }
    </style>
</head>

<body class="min-h-[100dvh] h-[100dvh] bg-white dark:bg-[#1F2123] font-sans antialiased text-gray-900 dark:text-white selection:bg-blue-100 selection:text-[#3285FF] overflow-hidden transition-colors duration-200">

    {{-- SVG CLIP-PATH DEFINITION FOR EXACT GEOMETRIC BLUE SILHOUETTE (Desktop) --}}
    <svg class="absolute w-0 h-0 pointer-events-none" aria-hidden="true">
        <defs>
            <clipPath id="blue-curve-clip" clipPathUnits="objectBoundingBox">
                <path d="M 0,0 L 0.72,0 C 0.80,0 0.86,0.07 0.86,0.20 L 0.86,0.82 C 0.86,0.93 0.92,1 1,1 L 0,1 Z" />
            </clipPath>
        </defs>
    </svg>

    <div class="h-full min-h-[100dvh] w-full flex flex-col lg:grid lg:grid-cols-[1.12fr_0.88fr] xl:grid-cols-[1.18fr_0.82fr] overflow-hidden">

        {{-- BLUE BANNER: HERO HEADER DI MOBILE (Gambar Besar, Jelas & Proporsional), FULL CURVED SPLIT-SCREEN DI DESKTOP --}}
        <div class="blue-curved-banner relative bg-[#3285FF] text-white px-5 pt-3.5 pb-3 sm:px-8 sm:pt-5 sm:pb-4 lg:p-14 lg:pr-24 flex flex-col justify-between overflow-hidden h-[36dvh] sm:h-[40dvh] lg:h-full shrink-0 rounded-b-[28px] sm:rounded-b-[36px] lg:rounded-none shadow-sm lg:shadow-none z-10">
            
            {{-- Lingkaran Aksen #5197FF --}}
            <div class="absolute -top-10 -right-10 lg:-top-12 lg:right-12 w-40 h-40 sm:w-56 sm:h-56 lg:w-[480px] lg:h-[480px] rounded-full bg-[#5197FF] pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 lg:-bottom-20 lg:-left-16 w-36 h-36 sm:w-48 sm:h-48 lg:w-[450px] lg:h-[450px] rounded-full bg-[#5197FF] pointer-events-none"></div>

            {{-- Top: KAI Tracker App Official White Logo --}}
            <div class="relative z-20 flex items-center select-none lg:translate-x-[10px] shrink-0">
                <x-icon name="logo-login-page" class="h-6 sm:h-7 lg:h-11 w-auto" />
            </div>

            {{-- Center: Security Illustration terpusat via <x-icon name="aset-gambar-login-page" /> (Besar, Jelas & Tidak Terpotong) --}}
            <div class="relative z-20 flex-1 min-h-0 w-full lg:max-w-[86%] flex items-center justify-center py-1 sm:py-2 lg:py-6 my-auto">
                <x-icon
                    name="aset-gambar-login-page"
                    class="h-full max-h-[155px] sm:max-h-[190px] lg:max-h-none lg:h-auto w-auto max-w-[85%] sm:max-w-[340px] lg:max-w-[500px] object-contain select-none pointer-events-none drop-shadow-md lg:drop-shadow-2xl mx-auto transition-transform"
                />
            </div>

            {{-- Bottom Footer Text (Desktop only) --}}
            <div class="hidden lg:block relative z-20 text-xs text-white/70 select-none shrink-0">
                &copy; {{ date('Y') }} PT Kereta Api Indonesia (Persero). All rights reserved.
            </div>

        </div>


        {{-- RIGHT / BOTTOM SIDE: FORM LOGIN (Mobile & Desktop) --}}
        <div class="flex-1 flex flex-col justify-between items-center px-6 sm:px-12 lg:px-16 py-3.5 sm:py-6 lg:py-8 bg-white dark:bg-[#1F2123] z-20 transition-colors h-full overflow-y-auto lg:overflow-hidden">
            
            <div class="w-full max-w-[360px] lg:max-w-[380px] my-auto">

                {{-- Heading 'Masuk' --}}
                <h1 class="text-2xl sm:text-[28px] lg:text-[34px] font-bold text-gray-950 dark:text-white tracking-tight mb-3.5 sm:mb-5 lg:mb-8">
                    Masuk
                </h1>

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login.post') }}" class="space-y-3 sm:space-y-4 lg:space-y-5">
                    @csrf

                    {{-- Notif: temp password expired --}}
                    @if(session('temp_password_expired'))
                    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 text-xs text-red-700 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ session('error', 'Password sementara Anda sudah tidak valid. Silakan tunggu password sementara berikutnya atau hubungi Super Admin.') }}</span>
                    </div>
                    @elseif($errors->has('login'))
                    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 text-xs text-red-700 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ $errors->first('login') }}</span>
                    </div>
                    @endif

                    {{-- 1. Alamat Email --}}
                    <div>
                        <label for="email-input" class="block text-xs font-semibold text-gray-700 dark:text-white mb-1.5 lg:mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                                <x-icon name="icon-email-login" class="w-5 h-5 block shrink-0" />
                            </span>
                            <input
                                type="text"
                                name="login"
                                id="email-input"
                                placeholder="masukan alamat email"
                                class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#282A2C] py-2.5 sm:py-3 pl-11 pr-4 text-xs sm:text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#3285FF] focus:outline-none transition"
                            >
                        </div>
                    </div>

                    {{-- 2. Kata Sandi --}}
                    <div>
                        <label for="password-input" class="block text-xs font-semibold text-gray-700 dark:text-white mb-1.5 lg:mb-2">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                                <x-icon name="icon-kunci-login" class="w-5 h-5 block shrink-0" />
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="password-input"
                                placeholder="* * * * * * * *"
                                class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#282A2C] py-2.5 sm:py-3 pl-11 pr-10 lg:pr-11 text-xs sm:text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#3285FF] focus:outline-none transition"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition cursor-pointer"
                                title="Lihat kata sandi"
                            >
                                <span id="icon-eye-off" class="flex items-center justify-center">
                                    <x-icon name="icon-show-kunci-login" class="w-5 h-5" />
                                </span>
                                <span id="icon-eye-on" class="hidden flex items-center justify-center">
                                    <x-icon name="off-show-kunci-login" class="w-5 h-5" />
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Lupa kata sandi link --}}
                    <div class="pt-0.5">
                        <a href="{{ route('password.request') }}" class="text-xs text-gray-500 dark:text-[#9AA0A6] hover:text-[#0066FF] dark:hover:text-[#3B82F6] underline transition inline-block cursor-pointer">
                            Lupa kata sandi?
                        </a>
                    </div>

                    {{-- 3. Submit Button 'Masuk' & 'Masuk Tamu' --}}
                    <div class="pt-1.5 sm:pt-2 space-y-2 sm:space-y-2.5">
                        <button
                            type="submit"
                            class="w-full rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer flex items-center justify-center tracking-wide"
                        >
                            Masuk
                        </button>

                        <a
                            href="{{ route('dashboard') }}"
                            class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-[#282A2C] hover:bg-gray-50 dark:hover:bg-white/5 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-gray-800 dark:text-white transition active:scale-98 cursor-pointer flex items-center justify-center tracking-wide text-center"
                        >
                            Masuk Tamu
                        </a>
                    </div>

                </form>

            </div>

            {{-- Mobile Bottom Copyright Footer (Hidden on Desktop) --}}
            <div class="lg:hidden text-center text-[10px] text-gray-400 dark:text-[#9AA0A6] pb-1 select-none">
                &copy; {{ date('Y') }} PT Kereta Api Indonesia (Persero). All rights reserved.
            </div>

        </div>

    </div>

    {{-- Script for Password Visibility Toggle --}}
    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password-input');
            const iconOff = document.getElementById('icon-eye-off');
            const iconOn = document.getElementById('icon-eye-on');

            if (pwd) {
                if (pwd.type === 'password') {
                    pwd.type = 'text';
                    if (iconOff) iconOff.classList.add('hidden');
                    if (iconOn) iconOn.classList.remove('hidden');
                } else {
                    pwd.type = 'password';
                    if (iconOff) iconOff.classList.remove('hidden');
                    if (iconOn) iconOn.classList.add('hidden');
                }
            }
        }
    {{-- Global Toast Notification --}}
    <x-toast />
</body>
</html>