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
                <a href="{{ route('map.index') }}"
                   class="inline-block py-1 pb-1.5 transition-all duration-150 {{ $active === 'map' ? 'font-semibold text-[#0066FF] dark:text-[#3B82F6] border-b-2 border-[#0066FF] dark:border-[#3B82F6]' : 'font-medium hover:text-gray-950 dark:hover:text-white border-b-2 border-transparent' }}">
                    Peta
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

        <!-- Aksi Kanan: Theme Toggle + Import + Profile/Login -->
        <div class="flex items-center gap-2 shrink-0">

            <!-- Link Import (Desktop) -->
            <a href="{{ route('import.index') }}"
               class="hidden lg:flex items-center gap-1.5 h-9 px-3 rounded-[10px] border border-gray-300/80 dark:border-white/15 bg-white dark:bg-[#2D3034] text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 text-xs font-semibold transition"
               title="Import Excel / CSV">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import</span>
            </a>

            <!-- Theme Toggle -->
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

            <!-- Profile / Login (Desktop) -->
            <div class="relative hidden lg:block" id="profileDropdownContainer">
                @auth
                    <!-- Profile Button -->
                    <button
                        id="profileButton"
                        type="button"
                        onclick="toggleProfileDropdown()"
                        class="flex items-center gap-2 rounded-lg lg:rounded-[10px] hover:opacity-90 transition cursor-pointer px-1 py-0.5"
                    >
                        <div class="flex h-9 w-9 lg:h-10 lg:w-10 items-center justify-center rounded-lg lg:rounded-[10px] bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] shrink-0 font-bold">
                            <x-icon name="profile-circle" class="w-5 h-5 lg:w-6 lg:w-6" />
                        </div>

                        <div class="leading-tight text-left pl-0.5">
                            <p class="text-xs sm:text-sm font-bold text-[#171717] dark:text-white truncate max-w-[130px]">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] font-normal">
                                {{ auth()->user()->role_label }}
                            </p>
                        </div>

                        <x-icon name="chevron-down" class="h-3.5 w-3.5 text-gray-400 dark:text-[#9AA0A6] transition-transform duration-200" id="profileArrow" />
                    </button>

                    <!-- Dropdown Desktop -->
                    <div
                        id="profileDropdown"
                        class="absolute right-0 top-full mt-2 w-max min-w-[200px] origin-top-right rounded-2xl p-2 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.7)] opacity-0 invisible scale-95 transition-all duration-200 bg-white dark:bg-[#1F2123] border border-gray-100 dark:border-white/10 z-[110] flex flex-col gap-1"
                    >
                        <div class="px-3 py-2 border-b border-gray-100 dark:border-white/10 text-xs text-gray-500">
                            Masuk sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role_label }})
                        </div>
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('settings.index', ['tab' => 'manajemen-admin']) }}"
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-[#0066FF] dark:hover:text-[#3B82F6] rounded-xl transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#0066FF] dark:text-[#3B82F6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Manajemen Admin</span>
                            </a>
                            <a href="{{ route('settings.index', ['tab' => 'persetujuan-sandi']) }}"
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-[#0066FF] dark:hover:text-[#3B82F6] rounded-xl transition cursor-pointer">
                                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                <span>Persetujuan Reset Sandi</span>
                            </a>
                            <a href="{{ route('settings.index', ['tab' => 'profil-saya']) }}"
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-[#0066FF] dark:hover:text-[#3B82F6] rounded-xl transition cursor-pointer">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Pengaturan Akun</span>
                            </a>
                        @else
                            <a href="{{ route('settings.index', ['tab' => 'profil-saya']) }}"
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-[#0066FF] dark:hover:text-[#3B82F6] rounded-xl transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#0066FF] dark:text-[#3B82F6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                        @endif
                        <a href="{{ route('import.index') }}"
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-[#0066FF] dark:hover:text-[#3B82F6] rounded-xl transition cursor-pointer">
                            <svg class="w-4 h-4 text-[#0066FF] dark:text-[#3B82F6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Import Data Excel</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center gap-2 px-3 py-2 text-xs font-semibold text-[#EF4444] hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="flex items-center gap-1.5 h-9 px-3.5 rounded-[10px] bg-[#0066FF] hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition active:scale-95 cursor-pointer"
                    >
                        <x-icon name="profile-circle" class="w-4 h-4 text-white" />
                        <span>Masuk</span>
                    </a>
                @endauth
            </div>

        </div>

    </nav>
</header>

<!-- ===== BOTTOM MOBILE NAVIGATION (hidden on lg+) ===== -->
<div class="lg:hidden fixed bottom-0 inset-x-0 z-[120] pointer-events-none select-none px-4 pb-4"
     style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 1rem));">
    <nav id="mobileBottomNav"
         class="pointer-events-auto max-w-[360px] sm:max-w-[400px] mx-auto bg-white/95 dark:bg-[#1F2123]/95 backdrop-blur-xl rounded-full p-1.5 flex items-center justify-between shadow-[0_8px_35px_rgba(0,0,0,0.12)] dark:shadow-[0_8px_35px_rgba(0,0,0,0.6)] border border-gray-200/80 dark:border-white/10 transition-all duration-300">

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

        <!-- Peta -->
        <a href="{{ route('map.index') }}"
           class="mobile-nav-item flex-1 h-11 flex items-center justify-center rounded-[22px] gap-1.5 transition-all duration-200
                  {{ $active === 'map' ? 'bg-gray-100 dark:bg-[#2D3034] text-[#0066FF] dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 active:scale-95' }}"
           title="Peta Potensi">
            <x-icon name="nav-map" class="h-5 w-5 sm:h-[22px] sm:w-[22px]" />
            @if($active === 'map')
                <span class="text-[11px] font-semibold hidden xs:inline">Peta</span>
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

        <!-- Login / Logout (Mobile) -->
        @auth
            <form action="{{ route('logout') }}" method="POST" class="flex-1 flex" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                @csrf
                <button
                    type="submit"
                    class="mobile-nav-item flex-1 h-11 flex items-center justify-center rounded-[22px] gap-1.5 transition-all duration-200 text-rose-500 hover:bg-red-50 dark:hover:bg-red-950/20 active:scale-95 cursor-pointer"
                    title="Keluar ({{ auth()->user()->name }})"
                >
                    <svg class="h-5 w-5 sm:h-[22px] sm:w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
               class="mobile-nav-item flex-1 h-11 flex items-center justify-center rounded-[22px] gap-1.5 transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-[#0066FF] dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 active:scale-95"
               title="Masuk">
                <x-icon name="nav-user" class="h-5 w-5 sm:h-[22px] sm:w-[22px]" />
                <span class="text-[11px] font-semibold hidden xs:inline">Masuk</span>
            </a>
        @endauth

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

    // Profile Dropdown Toggle (Desktop)
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        const arrow = document.getElementById('profileArrow');
        if (!dropdown) return;
        const isOpen = !dropdown.classList.contains('invisible');
        if (isOpen) {
            dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
            arrow?.classList.remove('rotate-180');
        } else {
            dropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.add('opacity-100', 'visible', 'scale-100');
            arrow?.classList.add('rotate-180');
        }
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#profileDropdownContainer')) {
            const dropdown = document.getElementById('profileDropdown');
            const arrow = document.getElementById('profileArrow');
            if (dropdown && !dropdown.classList.contains('invisible')) {
                dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                arrow?.classList.remove('rotate-180');
            }
        }
    });
</script>
