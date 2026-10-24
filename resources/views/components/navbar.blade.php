@props(['active' => 'dashboard'])

<!-- ===== TOP NAVIGATION BAR ===== -->
<header id="mainNavbar" class="sticky top-0 z-[100] w-full bg-[#F6F7F9]/95 dark:bg-[#282A2C]/95 backdrop-blur-md transition-all duration-200 border-b border-transparent">
    <nav class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-10 h-14 sm:h-16 lg:h-20 flex items-center justify-between gap-3">

        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="flex items-center sm:gap-2 text-[15px] sm:text-[16px] lg:text-[18px] font-bold italic tracking-tight text-gray-950 dark:text-white shrink-0 transition hover:opacity-90">
            <x-icon name="kai-logo" class="h-[19px] sm:h-5 lg:h-[24px] w-auto shrink-0" />
            <span class="leading-none select-none text-gray-950 dark:text-white">Tracker<span class="text-[#0066FF]">App</span></span>
        </a>

        <!-- Menu Desktop (lg+) -->
        <ul class="hidden lg:flex items-center gap-6 xl:gap-8 text-sm text-[#4A4A4A] dark:text-[#9AA0A6]">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="inline-block py-1 pb-1.5 transition-all duration-150 {{ $active === 'dashboard' ? 'font-semibold text-[#0066FF] dark:text-[#3B82F6] border-b-2 border-[#0066FF] dark:border-[#3B82F6]' : 'font-medium hover:text-gray-950 dark:hover:text-white border-b-2 border-transparent' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('contracts.index') }}"
                   class="inline-block py-1 pb-1.5 transition-all duration-150 {{ $active === 'contracts' ? 'font-semibold text-[#0066FF] dark:text-[#3B82F6] border-b-2 border-[#0066FF] dark:border-[#3B82F6]' : 'font-medium hover:text-gray-950 dark:hover:text-white border-b-2 border-transparent' }}">
                    Daftar Kontrak
                </a>
            </li>
            <li>
                <a href="{{ route('payments.index') }}"
                   class="inline-block py-1 pb-1.5 transition-all duration-150 {{ $active === 'payments' ? 'font-semibold text-[#0066FF] dark:text-[#3B82F6] border-b-2 border-[#0066FF] dark:border-[#3B82F6]' : 'font-medium hover:text-gray-950 dark:hover:text-white border-b-2 border-transparent' }}">
                    Data Tagihan
                </a>
            </li>
        </ul>

        <!-- Aksi Kanan: Theme Toggle + Import -->
        <div class="flex items-center gap-2 shrink-0">

            <!-- Link Import (Desktop) -->
            <a href="{{ route('import.index') }}"
               class="hidden lg:flex items-center gap-1.5 h-9 px-3 rounded-[10px] border border-gray-300/80 dark:border-white/15 bg-white dark:bg-[#2D3034] text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 text-xs font-semibold transition"
               title="Import CSV">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import</span>
            </a>
            <button
                type="button"
                id="themeToggleBtn"
                onclick="toggleTheme()"
                class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-[10px] border border-gray-300/80 dark:border-white/15 bg-transparent text-gray-700 dark:text-white hover:bg-gray-200/70 dark:hover:bg-white/10 active:scale-95 transition shadow-none cursor-pointer"
                title="Ganti Mode"
            >
                <x-icon name="moon" class="theme-icon-moon h-[18px] w-[18px] sm:h-5 sm:w-5 text-[#262626] dark:text-white" />
                <x-icon name="sun"  class="theme-icon-sun hidden h-[18px] w-[18px] sm:h-5 sm:w-5 text-white" />
            </button>
        </div>

    </nav>
</header>

<!-- ===== BOTTOM MOBILE NAVIGATION (hidden on lg+) ===== -->
<div class="lg:hidden fixed bottom-0 inset-x-0 z-[120] pointer-events-none select-none px-4 pb-4"
     style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 1rem));">
    <nav id="mobileBottomNav"
         class="pointer-events-auto max-w-[340px] sm:max-w-[360px] mx-auto bg-white/95 dark:bg-[#1F2123]/95 backdrop-blur-xl rounded-full p-1.5 flex items-center justify-between shadow-[0_8px_35px_rgba(0,0,0,0.12)] dark:shadow-[0_8px_35px_rgba(0,0,0,0.6)] border border-gray-200/80 dark:border-white/10 transition-all duration-300">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="mobile-nav-item flex-1 h-11 flex items-center justify-center rounded-[22px] gap-1.5 transition-all duration-200
                  {{ $active === 'dashboard' ? 'bg-gray-100 dark:bg-[#2D3034] text-[#0066FF] dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 active:scale-95' }}"
           title="Dashboard">
            <x-icon name="nav-home" class="h-5 w-5 sm:h-[22px] sm:w-[22px]" />
            @if($active === 'dashboard')
                <span class="text-[11px] font-semibold hidden xs:inline">Dashboard</span>
            @endif
        </a>

        <!-- Daftar Kontrak -->
        <a href="{{ route('contracts.index') }}"
           class="mobile-nav-item flex-1 h-11 flex items-center justify-center rounded-[22px] gap-1.5 transition-all duration-200
                  {{ $active === 'contracts' ? 'bg-gray-100 dark:bg-[#2D3034] text-[#0066FF] dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 active:scale-95' }}"
           title="Daftar Kontrak">
            <x-icon name="nav-contract" class="h-5 w-5 sm:h-[22px] sm:w-[22px]" />
            @if($active === 'contracts')
                <span class="text-[11px] font-semibold hidden xs:inline">Kontrak</span>
            @endif
        </a>

        <!-- Data Tagihan -->
        <a href="{{ route('payments.index') }}"
           class="mobile-nav-item flex-1 h-11 flex items-center justify-center rounded-[22px] gap-1.5 transition-all duration-200
                  {{ $active === 'payments' ? 'bg-gray-100 dark:bg-[#2D3034] text-[#0066FF] dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 active:scale-95' }}"
           title="Data Tagihan">
            <x-icon name="nav-card" class="h-5 w-5 sm:h-[22px] sm:w-[22px]" />
            @if($active === 'payments')
                <span class="text-[11px] font-semibold hidden xs:inline">Tagihan</span>
            @endif
        </a>

    </nav>
</div>

<script>
    // Scroll shadow on navbar
    (function () {
        const nav = document.getElementById('mainNavbar');
        if (!nav) return;
        window.addEventListener('scroll', function () {
            if (window.scrollY > 8) {
                nav.classList.add('navbar-scrolled');
            } else {
                nav.classList.remove('navbar-scrolled');
            }
        }, { passive: true });
    })();
</script>
