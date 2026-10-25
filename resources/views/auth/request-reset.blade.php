<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reset Kata Sandi — KAI Tracker App</title>

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

        {{-- Heading 'Reset Kata Sandi' --}}
        <h1 class="text-2xl sm:text-[28px] font-bold text-gray-950 dark:text-white tracking-tight mb-2 sm:mb-2.5">
            Reset Kata Sandi
        </h1>

        {{-- Subtitle --}}
        <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-normal leading-relaxed mb-4 sm:mb-6">
            Masukkan email yang terhubung dengan akun Anda, dan kami akan mengirimkan email berisi petunjuk untuk mengatur ulang kata sandi.
        </p>

        {{-- Form Kirim Permintaan --}}
        <form method="POST" action="{{ route('password.submit-request') }}" class="space-y-3 sm:space-y-4 lg:space-y-5">
            @csrf

            {{-- Alamat Email --}}
            <div>
                <label for="email-input" class="block text-xs font-semibold text-gray-700 dark:text-white mb-1.5 lg:mb-2">
                    Alamat Email
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                        <x-icon name="icon-email-login" class="w-5 h-5 block shrink-0" />
                    </span>
                    <input
                        type="email"
                        name="email"
                        id="email-input"
                        value="{{ old('email') }}"
                        placeholder="Masukan email yang terdaftar"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#282A2C] py-2.5 sm:py-3 pl-11 pr-4 text-xs sm:text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#3285FF] focus:outline-none transition @error('email') border-red-400 @enderror"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="pt-1.5 sm:pt-2">
                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#0066FF] hover:bg-blue-700 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold text-white transition active:scale-98 cursor-pointer flex items-center justify-center tracking-wide"
                >
                    Kirim Permintaan
                </button>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs text-gray-500 dark:text-[#9AA0A6] hover:text-[#0066FF] dark:hover:text-[#3B82F6] transition inline-flex items-center gap-1.5">
                    ← Kembali ke halaman login
                </a>
            </div>
        </form>

    </div>

    {{-- Global Toast Notification --}}
    <x-toast />

</body>
</html>
