<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan — Super Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Auto Theme Script (WIB 17:00 - 07:00 Auto Dark Mode) -->
    <x-theme-script />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 selection:bg-blue-100 selection:text-blue-600 flex flex-col justify-between transition-colors duration-200">

    {{-- Navbar --}}
    <x-navbar active="pengaturan" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-5 sm:pt-8 pb-28 lg:pb-12">

        {{-- Main 2-Column Layout (Sidebar Kiri & Konten Kanan) persis Figma --}}
        <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-5 lg:gap-14 items-start">

            {{-- ================= SIDEBAR KIRI ================= --}}
            <div class="space-y-4 lg:space-y-6">
                <h1 class="text-[24px] sm:text-[30px] lg:text-[34px] font-bold text-gray-950 dark:text-white tracking-tight">
                    Pengaturan
                </h1>

                {{-- Desktop: vertical nav, Mobile: horizontal tab pills --}}
                <nav class="flex flex-row lg:flex-col gap-2 lg:gap-3 overflow-x-auto pb-1 lg:pb-0">
                    {{-- 1. Tab Profil Saya --}}
                    <button
                        type="button"
                        onclick="switchSuperTab('profil')"
                        id="tab-btn-profil"
                        class="shrink-0 text-left text-sm font-semibold transition cursor-pointer text-[#0066FF] dark:text-[#3B82F6] px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-blue-50 dark:bg-blue-900/30 lg:bg-transparent lg:dark:bg-transparent flex items-center gap-2"
                    >
                        Profil Saya
                    </button>

                    {{-- 2. Tab Manajemen Admin --}}
                    <button
                        type="button"
                        onclick="switchSuperTab('manajemen-admin')"
                        id="tab-btn-manajemen"
                        class="shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent flex items-center gap-2"
                    >
                        Manajemen Admin
                    </button>

                    {{-- 3. Tab Persetujuan Reset Sandi --}}
                    <button
                        type="button"
                        onclick="switchSuperTab('persetujuan-sandi')"
                        id="tab-btn-persetujuan"
                        class="shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent flex items-center gap-2"
                    >
                        <span>Persetujuan Reset Sandi</span>
                        <span id="badge-pending-count" class="px-2 py-0.5 text-[11px] font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 rounded-full {{ ($pendingCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $pendingCount ?? 0 }}</span>
                    </button>

                    {{-- 4. Tab Import Data Excel --}}
                    <button
                        type="button"
                        onclick="switchSuperTab('import-excel')"
                        id="tab-btn-import-excel"
                        class="shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent flex items-center gap-2"
                    >
                        Import Data Excel
                    </button>
                </nav>
            </div>



            {{-- ================= KONTEN KANAN ================= --}}
            <div class="w-full">

                {{-- ------------------- TAB 1: PROFIL SAYA ------------------- --}}
                <div id="panel-profil" class="space-y-6">

                    {{-- Card 1: Avatar & Nama --}}
                    <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-8 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 transition-colors">
                        <div class="flex items-center gap-5 sm:gap-6">
                            {{-- Avatar circle --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-[#D9D9D9] dark:bg-[#34383D] flex items-center justify-center text-gray-400 dark:text-gray-300 shrink-0 select-none overflow-hidden">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>

                            <div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-snug" id="display-fullname">
                                    @auth {{ auth()->user()->name }} @else Haidar Rafi @endauth
                                </h2>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-medium mt-1">
                                    @auth
                                        @if(auth()->user()->isSuperAdmin()) Super Admin KAI @else Admin KAI @endif
                                    @else
                                        Super Admin KAI
                                    @endauth
                                </p>
                            </div>
                        </div>

                        <div>
                            <button
                                type="button"
                                onclick="openEditProfileModal()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[8px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-medium text-white shadow-xs transition cursor-pointer"
                            >
                                <x-icon name="edit-detail-peta" class="w-4 h-4" />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>

                    {{-- Card 2: Informasi Profil --}}
                    <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-8 shadow-xs space-y-6 transition-colors">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-4">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                                Informasi Profil
                            </h3>
                            <button
                                type="button"
                                onclick="openEditProfileModal()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[8px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-medium text-white shadow-xs transition cursor-pointer"
                            >
                                <x-icon name="edit-detail-peta" class="w-4 h-4" />
                                <span>Edit</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 pt-2">
                            {{-- Nama Awal --}}
                            <div>
                                <span class="block text-xs font-medium text-gray-400 dark:text-[#9AA0A6] mb-1.5">Nama Lengkap</span>
                                <span class="block text-sm sm:text-[15px] font-semibold text-gray-900 dark:text-white" id="display-first-name">
                                    @auth {{ auth()->user()->name }} @else Haidar Rafi @endauth
                                </span>
                            </div>

                            {{-- Role Akun --}}
                            <div>
                                <span class="block text-xs font-medium text-gray-400 dark:text-[#9AA0A6] mb-1.5">Role Akun</span>
                                <span class="block text-sm sm:text-[15px] font-semibold text-gray-900 dark:text-white" id="display-role">
                                    @auth {{ auth()->user()->role ?? 'SuperAdmin' }} @else SuperAdmin @endauth
                                </span>
                            </div>

                            {{-- Alamat Email --}}
                            <div class="sm:col-span-2">
                                <span class="block text-xs font-medium text-gray-400 dark:text-[#9AA0A6] mb-1.5">Alamat Email</span>
                                <span class="block text-sm sm:text-[15px] font-semibold text-gray-900 dark:text-white" id="display-email">
                                    @auth {{ auth()->user()->email }} @else admin.kai@daop4.com @endauth
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ------------------- TAB 2: MANAJEMEN ADMIN (TABLE) ------------------- --}}
                <div id="panel-manajemen-admin" class="hidden space-y-6">
                    <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-8 shadow-xs space-y-6 transition-colors">
                        
                        {{-- Header Controls: Search, Role Filter, + Tambah Admin Button --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4">
                            <div class="flex items-center gap-2 w-full sm:w-auto flex-1 max-w-md">
                                {{-- Search Input --}}
                                <div class="relative flex items-center flex-1 sm:w-[185px] sm:flex-none h-[36px] sm:h-[38px]">
                                    <x-icon name="search" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 dark:text-[#9AA0A6] absolute left-2.5 sm:left-3 pointer-events-none" />
                                    <input
                                        type="text"
                                        placeholder="Search"
                                        id="search-admin-input"
                                        onkeyup="filterAdminTable()"
                                        class="w-full h-full pl-8 sm:pl-9 pr-3 py-1 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-lg lg:rounded-[10px] focus:outline-none focus:border-[#0066FF] text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 placeholder:text-xs sm:placeholder:text-sm transition"
                                    >
                                </div>

                                {{-- Role Filter Dropdown (Konsisten Desain Jatuh Tempo) --}}
                                <div class="relative custom-filter-container shrink-0">
                                    <button type="button" class="filter-dropdown-btn inline-flex items-center h-[36px] sm:h-[38px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3 py-1 transition cursor-pointer">
                                        <span id="label-role" class="text-gray-400 dark:text-[#9AA0A6] font-normal text-xs select-none">Role</span>
                                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] ml-1 pointer-events-none transition-transform duration-200 filter-dropdown-arrow" />
                                    </button>
                                    <div class="filter-dropdown-menu opacity-0 invisible scale-95 pointer-events-none transition-all duration-200 origin-top-left absolute left-0 top-full mt-1 z-[100] min-w-[140px] rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1.5 flex flex-col gap-0.5">
                                        <button type="button" onclick="filterRoleSettings('', 'Role')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-[11px] sm:text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-[10px] transition text-left cursor-pointer">
                                            <span>Semua Role</span>
                                        </button>
                                        <button type="button" onclick="filterRoleSettings('Admin', 'Admin')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-[10px] transition text-left cursor-pointer">
                                            <span>Admin</span>
                                        </button>
                                        <button type="button" onclick="filterRoleSettings('SuperAdmin', 'SuperAdmin')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-[10px] transition text-left cursor-pointer">
                                            <span>SuperAdmin</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- + Tambah Admin Button --}}
                            <div>
                                <button
                                    type="button"
                                    onclick="openAddAdminModal()"
                                    class="inline-flex items-center gap-1.5 sm:gap-2 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-lg sm:rounded-[10px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-semibold text-white shadow-xs transition active:scale-95 cursor-pointer shrink-0"
                                >
                                    <x-icon name="plus-icon" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-white fill-white" />
                                    <span>Tambah Admin</span>
                                </button>
                            </div>
                        </div>

                        {{-- MOBILE CARDS VIEW (block sm:hidden) --}}
                        <div class="space-y-3.5 sm:hidden" id="admin-cards-container">
                            @forelse($admins as $admin)
                            <div class="admin-card rounded-xl sm:rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1F2123] p-4 space-y-3 shadow-2xs transition-all" data-timestamp="{{ $admin->created_at->toISOString() }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-[#34383D] flex items-center justify-center shrink-0 text-[#0066FF] dark:text-[#3B82F6]">
                                            <x-icon name="profile-circle" class="w-5 h-5" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight truncate name-col">{{ $admin->name }}</h3>
                                            <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mt-0.5 truncate email-col">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="admin-action-btn flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-[#34383D] hover:bg-gray-200 dark:hover:bg-white/15 text-gray-600 dark:text-white transition cursor-pointer shrink-0"
                                        title="Aksi"
                                        data-id="{{ $admin->id }}"
                                        data-name="{{ $admin->name }}"
                                        data-email="{{ $admin->email }}"
                                        data-can-delete="{{ $admin->role !== 'superadmin' && $admin->id !== auth()->id() ? 'true' : 'false' }}"
                                    >
                                        <x-icon name="dots-vertical" class="w-4 h-4" />
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-2 bg-gray-50/90 dark:bg-[#2D3034] rounded-lg p-2.5 text-xs">
                                    <div class="role-col">
                                        <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px] mb-0.5">Role</span>
                                        @if($admin->role === 'superadmin')
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#0066FF] dark:text-[#3B82F6] bg-blue-50 dark:bg-blue-900/30 border border-blue-200/80 dark:border-blue-800">SuperAdmin</span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/80 dark:border-emerald-800">Admin</span>
                                        @endif
                                    </div>
                                    <div class="date-col">
                                        <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px] mb-0.5">Dibuat</span>
                                        <span class="font-medium text-gray-800 dark:text-white text-[11px]">{{ $admin->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="py-6 text-center text-sm text-gray-400 dark:text-[#9AA0A6]">Belum ada data admin.</div>
                            @endforelse
                        </div>

                        {{-- DESKTOP ADMIN TABLE (hidden sm:block - Konsisten dengan Desain Desktop) --}}
                        <div class="hidden sm:block border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden bg-white dark:bg-[#1F2123]">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse" id="admin-table">
                                    <thead>
                                        <tr class="bg-[#F8F9FA] dark:bg-[#282A2C] border-b border-gray-200 dark:border-white/10">
                                            <th scope="col" class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Email</th>
                                            <th scope="col" class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Nama Akun</th>
                                            <th scope="col" class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Role</th>
                                            <th scope="col" class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">
                                                <button
                                                    type="button"
                                                    id="btn-sort-terakhir-aktif"
                                                    onclick="toggleSortTerakhirAktif()"
                                                    class="inline-flex items-center gap-1.5 cursor-pointer select-none text-gray-400 dark:text-[#9AA0A6] hover:text-gray-900 dark:hover:text-white transition focus:outline-none"
                                                    title="Klik untuk mengurutkan Terakhir Aktif"
                                                >
                                                    <span>Terakhir Aktif</span>
                                                    <span id="sort-arrow-icon" class="inline-flex items-center text-gray-400 dark:text-[#9AA0A6]">
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            {{-- Arrow Up (Ascending / Terlama) --}}
                                                            <path id="sort-arrow-up" d="M9.15 5.15834L6.05837 2.06665C6.00003 2.00832 5.92502 1.95833 5.84169 1.925C5.83336 1.925 5.825 1.92499 5.81667 1.91666C5.75 1.89166 5.67499 1.875 5.59999 1.875C5.43333 1.875 5.27502 1.94164 5.15835 2.05831L2.05833 5.15834C1.81666 5.4 1.81666 5.8 2.05833 6.04167C2.29999 6.28333 2.70004 6.28333 2.94171 6.04167L4.98333 3.99999V17.5C4.98333 17.8417 5.26667 18.125 5.60833 18.125C5.95 18.125 6.23333 17.8417 6.23333 17.5V4.00833L8.26667 6.04167C8.39167 6.16667 8.55003 6.22498 8.70836 6.22498C8.86669 6.22498 9.025 6.16667 9.15 6.04167C9.39167 5.8 9.39167 5.40834 9.15 5.15834Z" fill="currentColor" class="text-gray-400 dark:text-[#9AA0A6] opacity-40 transition-all duration-200" />
                                                            {{-- Arrow Down (Descending / Terbaru) --}}
                                                            <path id="sort-arrow-down" d="M17.9416 13.9583C17.7 13.7167 17.2999 13.7167 17.0582 13.9583L15.0166 16V2.5C15.0166 2.15833 14.7333 1.875 14.3916 1.875C14.05 1.875 13.7666 2.15833 13.7666 2.5V15.9917L11.7333 13.9583C11.4916 13.7167 11.0916 13.7167 10.85 13.9583C10.6083 14.2 10.6083 14.6 10.85 14.8417L13.9416 17.9333C13.9999 17.9917 14.0749 18.0417 14.1583 18.075C14.1666 18.075 14.175 18.075 14.1833 18.0833C14.25 18.1083 14.325 18.125 14.4 18.125C14.5666 18.125 14.7249 18.0584 14.8416 17.9417L17.9416 14.8417C18.1833 14.5917 18.1833 14.2 17.9416 13.9583Z" fill="currentColor" class="text-gray-400 dark:text-[#9AA0A6] opacity-40 transition-all duration-200" />
                                                        </svg>
                                                    </span>
                                                </button>
                                            </th>
                                            <th scope="col" class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-white/10 text-[13px] text-gray-800 dark:text-gray-200" id="admin-tbody">
                                        @forelse($admins as $admin)
                                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/5 transition-colors admin-row" data-timestamp="{{ $admin->created_at->toISOString() }}">
                                            <td class="py-3.5 px-4 font-normal text-gray-900 dark:text-white whitespace-nowrap email-col">{{ $admin->email }}</td>
                                            <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white whitespace-nowrap name-col">{{ $admin->name }}</td>
                                            <td class="py-3.5 px-4 whitespace-nowrap role-col">
                                                @if($admin->role === 'superadmin')
                                                    <span class="inline-block px-3 py-0.5 rounded-full text-xs font-medium text-[#0066FF] dark:text-[#3B82F6] bg-blue-50 dark:bg-blue-900/30 border border-blue-200/80 dark:border-blue-800">SuperAdmin</span>
                                                @else
                                                    <span class="inline-block px-3 py-0.5 rounded-full text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/80 dark:border-emerald-800">Admin</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap font-normal date-col">{{ $admin->created_at->format('d/m/Y \a\t H.i') }}</td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                                <button
                                                    type="button"
                                                    class="admin-action-btn flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-[#34383D] hover:bg-gray-200 dark:hover:bg-white/15 text-gray-600 dark:text-white transition cursor-pointer mx-auto"
                                                    title="Aksi"
                                                    data-id="{{ $admin->id }}"
                                                    data-name="{{ $admin->name }}"
                                                    data-email="{{ $admin->email }}"
                                                    data-can-delete="{{ $admin->role !== 'superadmin' && $admin->id !== auth()->id() ? 'true' : 'false' }}"
                                                >
                                                    <x-icon name="dots-vertical" class="w-4 h-4" />
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-sm text-gray-400 dark:text-[#9AA0A6]">Belum ada data admin.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                </div>
            </div>


            {{-- ------------------- TAB 2: PERSETUJUAN RESET SANDI (CARDS) ------------------- --}}
            <div id="panel-persetujuan-sandi" class="hidden space-y-6">
                <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-8 shadow-xs space-y-6 transition-colors">
                    
                    {{-- Header Controls: Search & Waktu Pengajuan Filter (Konsisten Desain Jatuh Tempo) --}}
                    <div class="flex items-center gap-2 w-full sm:w-auto max-w-md">
                        {{-- Search Input --}}
                        <div class="relative flex items-center flex-1 sm:w-[185px] sm:flex-none h-[36px] sm:h-[38px]">
                            <x-icon name="search" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 dark:text-[#9AA0A6] absolute left-2.5 sm:left-3 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search"
                                id="search-approval-input"
                                onkeyup="filterApprovalCards()"
                                class="w-full h-full pl-8 sm:pl-9 pr-3 py-1 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-lg lg:rounded-[10px] focus:outline-none focus:border-[#0066FF] text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 placeholder:text-xs sm:placeholder:text-sm transition"
                            >
                        </div>

                        {{-- Filter Waktu Pengajuan Dropdown --}}
                        <div class="relative custom-filter-container shrink-0">
                            <button type="button" class="filter-dropdown-btn inline-flex items-center h-[36px] sm:h-[38px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3 py-1 transition cursor-pointer">
                                <span id="label-waktu" class="text-gray-400 dark:text-[#9AA0A6] font-normal text-xs select-none">Waktu Pengajuan</span>
                                <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] ml-1 pointer-events-none transition-transform duration-200 filter-dropdown-arrow" />
                            </button>
                            <div class="filter-dropdown-menu opacity-0 invisible scale-95 pointer-events-none transition-all duration-200 origin-top-left absolute left-0 top-full mt-1 z-[100] min-w-[150px] rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1.5 flex flex-col gap-0.5">
                                <button type="button" onclick="filterWaktuApproval('', 'Waktu Pengajuan')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-[11px] sm:text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-[10px] transition text-left cursor-pointer">
                                    <span>Semua Waktu</span>
                                </button>
                                <button type="button" onclick="filterWaktuApproval('terbaru', 'Terbaru')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-[10px] transition text-left cursor-pointer">
                                    <span>Terbaru</span>
                                </button>
                                <button type="button" onclick="filterWaktuApproval('terlama', 'Terlama')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-[10px] transition text-left cursor-pointer">
                                    <span>Terlama</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Cards List of Reset Password Requests --}}
                    <div class="space-y-4" id="requests-list-container">
                        @forelse($requests as $req)
                            <div class="approval-card-item rounded-xl sm:rounded-2xl border border-gray-200/90 dark:border-white/10 bg-white dark:bg-[#1F2123] p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 transition hover:border-gray-300 dark:hover:border-white/20 shadow-2xs" id="req-card-{{ $req->id }}" data-timestamp="{{ $req->created_at->toISOString() }}">
                                {{-- Profile & Info Header --}}
                                <div class="flex items-center gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 dark:bg-[#34383D] flex items-center justify-center shrink-0 text-[#0066FF] dark:text-[#3B82F6]">
                                        <x-icon name="profile-circle" class="w-5 h-5 sm:w-6 sm:h-6" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                                            <h4 class="text-sm font-bold sm:font-semibold text-gray-900 dark:text-white leading-tight truncate">
                                                {{ $req->user->name ?? 'Admin #' . $req->user_id }}
                                            </h4>
                                            <span class="sm:hidden text-[10px] font-medium text-gray-500 dark:text-[#9AA0A6] bg-gray-100/80 dark:bg-[#2D3034] px-2 py-0.5 rounded-full">
                                                {{ $req->created_at->format('d/m/Y \at H.i A') }}
                                            </span>
                                            @if($req->request_count > 1)
                                                <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-full">
                                                    Request ke-{{ $req->request_count }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mt-0.5 truncate">
                                            {{ $req->user->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Actions & Timestamp on Desktop --}}
                                <div class="flex items-center gap-2 sm:gap-6 justify-between sm:justify-end pt-1 sm:pt-0 border-t border-gray-100 dark:border-white/10 sm:border-t-0">
                                    <span class="hidden sm:inline-block text-xs text-gray-500 dark:text-[#9AA0A6]">
                                        {{ $req->created_at->format('d/m/Y \at H.i A') }}
                                    </span>

                                    @if($req->status === 'pending')
                                        <div class="flex items-center gap-2 w-full sm:w-auto">
                                            <form action="{{ route('reset-requests.reject', $req->id) }}" method="POST" class="inline flex-1 sm:flex-none">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="w-full sm:w-auto min-h-[38px] sm:min-h-[34px] inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl sm:rounded-[8px] border border-red-200 dark:border-red-900/40 bg-red-50/80 dark:bg-red-950/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-xs font-semibold text-red-600 dark:text-red-400 transition cursor-pointer"
                                                >
                                                    <span class="text-sm leading-none font-bold">✕</span>
                                                    <span>Tolak</span>
                                                </button>
                                            </form>

                                            <form action="{{ route('reset-requests.approve', $req->id) }}" method="POST" class="inline flex-1 sm:flex-none">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="w-full sm:w-auto min-h-[38px] sm:min-h-[34px] inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl sm:rounded-[8px] bg-[#0066FF] hover:bg-blue-700 text-xs font-semibold text-white shadow-xs transition cursor-pointer"
                                                >
                                                    <span class="text-sm leading-none font-bold">✓</span>
                                                    <span>Setuju</span>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($req->status === 'approved')
                                        <span class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($req->status === 'rejected')
                                        <span class="px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 text-xs font-semibold">
                                            ✕ Ditolak
                                        </span>
                                    @elseif($req->status === 'auto_reset')
                                        <span class="px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 text-xs font-semibold">
                                            ⚡ Password Sementara Dikirim
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-semibold">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400 dark:text-[#9AA0A6]">
                                <x-icon name="icon-mail" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                                <p class="text-sm font-medium">Belum ada permintaan reset kata sandi</p>
                            </div>
                        @endforelse

                        @if($requests->hasPages())
                            <div class="pt-4">
                                {{ $requests->links() }}
                            </div>
                        @endif
                    </div>

                </div>

            </div>


            {{-- ------------------- TAB 4: IMPORT DATA EXCEL ------------------- --}}
            <div id="panel-import-excel" class="hidden space-y-6">

                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 p-4 rounded-2xl text-xs sm:text-sm shadow-xs">
                        <x-icon name="toast-sukses" class="w-5 h-5 shrink-0" />
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center gap-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs sm:text-sm shadow-xs">
                        <x-icon name="toast-gagal" class="w-5 h-5 shrink-0" />
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 p-4 rounded-2xl text-xs sm:text-sm shadow-xs">
                        <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Upload File Data Card (Sesuai Gambar Mockup) --}}
                <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-10 shadow-xs space-y-8 transition-colors">
                    
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-950 dark:text-white tracking-tight">
                        Upload File Data
                    </h2>

                        <form id="super-excel-import-form" method="POST" action="{{ route('settings.import-excel') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            {{-- Drag & Drop Upload Zone with icon-upload-data.svg --}}
                            <div
                                id="super-dropzone-area"
                                onclick="document.getElementById('super-excel-file-input').click()"
                                class="relative border-2 border-dashed border-gray-200 dark:border-white/15 hover:border-[#0066FF] dark:hover:border-[#3B82F6] bg-transparent hover:bg-blue-50/20 dark:hover:bg-blue-900/10 rounded-2xl p-8 sm:p-12 text-center transition cursor-pointer group flex flex-col items-center justify-center"
                            >
                                <input
                                    type="file"
                                    name="excel_file"
                                    id="super-excel-file-input"
                                    accept=".csv, .xlsx, .xls, .txt"
                                    class="hidden"
                                    onchange="handleSuperFileSelected(this)"
                                    required
                                >

                                <div class="flex flex-col items-center justify-center pointer-events-none">
                                    <x-icon name="icon-upload-data" class="h-32 sm:h-36 w-auto mx-auto mb-4 group-hover:scale-105 transition-transform duration-200" />
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                        Pilih file atau drag & drop ke area ini
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-[#9AA0A6]">
                                        file mendukung format .csv, .xlsx, .xls
                                    </p>
                                </div>
                            </div>

                            {{-- Selected File Box (Matching Gambar 1 & Gambar 2) --}}
                            <div id="super-selected-file-container" class="hidden rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#282A2C] p-4 sm:p-5 transition-all">
                                <div class="flex items-center gap-4">
                                    {{-- Icon Box (Circular Import or Green Excel Icon) --}}
                                    <div id="super-icon-wrapper" class="shrink-0 flex items-center justify-center">
                                        <x-icon id="super-preview-excel-icon" name="excel-icon" class="w-9 h-9 object-contain" />
                                    </div>

                                    {{-- Info & Progress Bar --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-0.5">
                                            <span id="super-selected-file-name" class="font-semibold text-sm text-gray-900 dark:text-white truncate">pk.xlsx</span>
                                            <span id="super-upload-percentage" class="text-xs font-semibold text-gray-500 dark:text-gray-400">100%</span>
                                        </div>

                                        <span id="super-selected-file-size" class="text-xs text-gray-400 dark:text-gray-500 block mb-2">10 MB</span>

                                        {{-- Progress Bar --}}
                                        <div id="super-progress-wrapper" class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                            <div id="super-import-progress-bar" class="bg-[#0066FF] h-full rounded-full transition-all duration-300" style="width: 100%;"></div>
                                        </div>
                                    </div>

                                    {{-- Cancel File Selection Button --}}
                                    <button
                                        type="button"
                                        onclick="clearSuperSelectedFile(event)"
                                        class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 cursor-pointer shrink-0"
                                        title="Hapus file"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Action Buttons (Batal & Import) --}}
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    onclick="clearSuperSelectedFile(event)"
                                    class="px-7 py-2.5 rounded-xl bg-[#E00000] hover:bg-red-700 text-sm font-semibold text-white transition shadow-sm hover:shadow active:scale-98 cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    id="btn-super-submit-import"
                                    class="px-7 py-2.5 rounded-xl bg-[#0066FF] hover:bg-blue-700 text-sm font-semibold text-white transition shadow-sm hover:shadow active:scale-98 cursor-pointer"
                                >
                                    Import
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </main>

    {{-- ================= GLOBAL DROPDOWN MENU AKSI ADMIN (DI LUAR TABEL AGAR TIDAK TERPOTONG) ================= --}}
    <div id="admin-global-action-dropdown" class="opacity-0 invisible scale-95 pointer-events-none transition-all duration-200 origin-top-right fixed z-[9999] w-max min-w-[200px] sm:min-w-[220px] rounded-xl sm:rounded-2xl bg-white dark:bg-[#1F2123] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.7)] p-1.5 sm:p-2 flex flex-col gap-0.5 sm:gap-1 text-left">
        <button type="button" id="admin-dd-nonaktif-btn" onclick="executeAdminAction('nonaktif')" class="flex items-center gap-2 sm:gap-2.5 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-xl transition cursor-pointer w-full text-left whitespace-nowrap">
            <x-icon name="icon-nonaktif-profile" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500 dark:text-gray-300 shrink-0" />
            <span class="whitespace-nowrap">Non Aktif Profil</span>
        </button>
        <button type="button" id="admin-dd-reset-btn" onclick="executeAdminAction('reset')" class="flex items-center gap-2 sm:gap-2.5 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-xl transition cursor-pointer w-full text-left whitespace-nowrap">
            <x-icon name="icon-key" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500 dark:text-gray-300 shrink-0" />
            <span class="whitespace-nowrap">Reset Sandi Sementara</span>
        </button>
        <button type="button" id="admin-dd-delete-btn" onclick="executeAdminAction('delete')" class="flex items-center gap-2 sm:gap-2.5 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-semibold text-[#EF4444] dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition cursor-pointer w-full text-left whitespace-nowrap">
            <x-icon name="icon-trash-admin" class="w-4 h-4 sm:w-5 sm:h-5 text-[#EF4444] dark:text-red-400 shrink-0" />
            <span class="whitespace-nowrap">Hapus Admin</span>
        </button>
    </div>

    {{-- ================= MODAL TAMBAH ADMIN ================= --}}
    <div id="modal-tambah-admin" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-3 sm:p-4">
        <div class="w-full max-w-[340px] sm:max-w-md rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-8 shadow-2xl border border-gray-100 dark:border-white/10 space-y-3.5 sm:space-y-5 animate-in fade-in zoom-in-95 duration-200 transition-colors max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-2.5 sm:pb-3">
                <h3 class="text-sm sm:text-lg font-bold text-gray-900 dark:text-white">Tambah Akun Admin Baru</h3>
                <button type="button" onclick="closeAddAdminModal()" class="p-1 rounded-lg text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer">
                    <x-icon name="close" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
            </div>

            <form id="form-tambah-admin" onsubmit="saveNewAdmin(event)" class="space-y-2.5 sm:space-y-4">

                <div id="form-admin-error" class="hidden rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-3 py-2 sm:px-3.5 sm:py-2.5 text-[11px] sm:text-xs text-red-700 dark:text-red-400"></div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Nama Lengkap</label>
                    <input
                        type="text"
                        id="new-admin-name"
                        placeholder="Contoh: Haidar Rafi"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Username</label>
                    <input
                        type="text"
                        id="new-admin-username"
                        placeholder="Contoh: haidar.rafi"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Alamat Email Dinas</label>
                    <input
                        type="email"
                        id="new-admin-email"
                        placeholder="nama@daop4.com"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Kata Sandi</label>
                    <input
                        type="password"
                        id="new-admin-password"
                        placeholder="Minimal 8 karakter"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Konfirmasi Kata Sandi</label>
                    <input
                        type="password"
                        id="new-admin-password-confirmation"
                        placeholder="Ulangi kata sandi"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div class="flex items-center justify-end gap-2 sm:gap-3 pt-2 sm:pt-3">
                    <button
                        type="button"
                        onclick="closeAddAdminModal()"
                        class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-[8px] border border-gray-200 dark:border-white/10 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/10 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-[8px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-medium text-white transition shadow-xs cursor-pointer"
                    >
                        Simpan Admin
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Modal Edit Profil Super Admin --}}
    <div id="modal-edit-profile" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-3 sm:p-4">
        <div class="w-full max-w-[340px] sm:max-w-md rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] border border-gray-100 dark:border-white/10 p-4 sm:p-8 shadow-2xl space-y-4 sm:space-y-6 transition-colors max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-2.5 sm:pb-4">
                <h3 class="text-sm sm:text-lg font-bold text-gray-900 dark:text-white">
                    Edit Profil Saya
                </h3>
                <button type="button" onclick="closeEditProfileModal()" class="p-1 rounded-lg text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer">
                    <x-icon name="close" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
            </div>

            <form id="form-edit-profile" onsubmit="saveProfileChanges(event)" class="space-y-3 sm:space-y-4">
                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Nama Lengkap</label>
                    <input
                        type="text"
                        id="input-fullname"
                        value="@auth {{ auth()->user()->name }} @else Haidar Rafi @endauth"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Alamat Email</label>
                    <input
                        type="email"
                        id="input-email"
                        value="@auth {{ auth()->user()->email }} @else admin.kai@daop4.com @endauth"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div class="flex items-center justify-end gap-2 sm:gap-3 pt-2 sm:pt-3">
                    <button
                        type="button"
                        onclick="closeEditProfileModal()"
                        class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-[8px] border border-gray-200 dark:border-white/10 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/10 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-[8px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-medium text-white transition shadow-xs cursor-pointer"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        // Tab switching SuperAdmin (4 Tabs: profil, manajemen-admin, persetujuan-sandi, import-excel)
        function switchSuperTab(tabName) {
            const btnProfil = document.getElementById('tab-btn-profil');
            const btnManajemen = document.getElementById('tab-btn-manajemen');
            const btnPersetujuan = document.getElementById('tab-btn-persetujuan');
            const btnImport = document.getElementById('tab-btn-import-excel');

            const panelProfil = document.getElementById('panel-profil');
            const panelManajemen = document.getElementById('panel-manajemen-admin');
            const panelPersetujuan = document.getElementById('panel-persetujuan-sandi');
            const panelImport = document.getElementById('panel-import-excel');

            const activeClass = "shrink-0 text-left text-sm font-semibold transition cursor-pointer text-[#0066FF] dark:text-[#3B82F6] px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-blue-50 dark:bg-blue-900/30 lg:bg-transparent lg:dark:bg-transparent flex items-center gap-2";
            const inactiveClass = "shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent flex items-center gap-2";

            // Sembunyikan semua panel
            if (panelProfil) panelProfil.classList.add('hidden');
            if (panelManajemen) panelManajemen.classList.add('hidden');
            if (panelPersetujuan) panelPersetujuan.classList.add('hidden');
            if (panelImport) panelImport.classList.add('hidden');

            // Reset tab button styles
            if (btnProfil) btnProfil.className = inactiveClass;
            if (btnManajemen) btnManajemen.className = inactiveClass;
            if (btnPersetujuan) btnPersetujuan.className = inactiveClass;
            if (btnImport) btnImport.className = inactiveClass;

            if (tabName === 'profil') {
                if (btnProfil) btnProfil.className = activeClass;
                if (panelProfil) panelProfil.classList.remove('hidden');
            } else if (tabName === 'manajemen-admin') {
                if (btnManajemen) btnManajemen.className = activeClass;
                if (panelManajemen) panelManajemen.classList.remove('hidden');
            } else if (tabName === 'persetujuan-sandi') {
                if (btnPersetujuan) btnPersetujuan.className = activeClass;
                if (panelPersetujuan) panelPersetujuan.classList.remove('hidden');
            } else if (tabName === 'import-excel') {
                if (btnImport) btnImport.className = activeClass;
                if (panelImport) panelImport.classList.remove('hidden');
            }
        }

        // Inisialisasi tab aktif saat halaman pertama kali dibuka
        document.addEventListener('DOMContentLoaded', () => {
            @php
                $tabToOpen = request('tab', $activeTab ?? 'profil');
                if ($tabToOpen === 'profil-saya') $tabToOpen = 'profil';
                if ($tabToOpen === 'manajemen') $tabToOpen = 'manajemen-admin';
                if ($tabToOpen === 'persetujuan') $tabToOpen = 'persetujuan-sandi';
                if ($tabToOpen === 'import') $tabToOpen = 'import-excel';
            @endphp
            switchSuperTab('{{ $tabToOpen }}');
        });

        // Modal Edit Profil
        function openEditProfileModal() {
            document.getElementById('modal-edit-profile')?.classList.remove('hidden');
        }
        function closeEditProfileModal() {
            document.getElementById('modal-edit-profile')?.classList.add('hidden');
        }
        function saveProfileChanges(e) {
            e.preventDefault();
            const fullname = document.getElementById('input-fullname')?.value.trim() || 'Haidar Rafi';
            const email = document.getElementById('input-email')?.value.trim() || 'admin.kai@daop4.com';

            if (!fullname || !email) {
                if (window.showToast) window.showToast('Gagal update: Nama dan email tidak boleh kosong!', 'error');
                return;
            }

            const displayFullname = document.getElementById('display-fullname');
            const displayFirst = document.getElementById('display-first-name');
            const displayEmail = document.getElementById('display-email');

            if (displayFullname) displayFullname.textContent = fullname;
            if (displayFirst) displayFirst.textContent = fullname;
            if (displayEmail) displayEmail.textContent = email;

            closeEditProfileModal();
            if (window.showToast) window.showToast('Sukses update data terbaru!', 'success');
        }

        function handleSuperFileSelected(input) {
            const container = document.getElementById('super-selected-file-container');
            const nameEl = document.getElementById('super-selected-file-name');
            const sizeEl = document.getElementById('super-selected-file-size');
            const percentEl = document.getElementById('super-upload-percentage');
            const barEl = document.getElementById('super-import-progress-bar');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const ext = file.name.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                    if (window.showToast) window.showToast('Gagal: Format file harus .xlsx, .xls, atau .csv!', 'error');
                    clearSuperSelectedFile();
                    return;
                }

                nameEl.textContent = file.name;
                const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
                sizeEl.textContent = (sizeMb >= 1) ? `${sizeMb} MB` : `${(file.size / 1024).toFixed(1)} KB`;
                
                // Show container with animation
                container.classList.remove('hidden');
                
                // Progress simulation to 100%
                percentEl.textContent = '100%';
                barEl.style.width = '100%';
                if (window.showToast) window.showToast('File siap diimpor: ' + file.name, 'info');
            } else {
                container.classList.add('hidden');
            }
        }

        function clearSuperSelectedFile(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const input = document.getElementById('super-excel-file-input');
            const container = document.getElementById('super-selected-file-container');
            if (input) input.value = '';
            if (container) container.classList.add('hidden');
        }

        // Support Drag & Drop visuals
        const superDropzone = document.getElementById('super-dropzone-area');
        if (superDropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                superDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    superDropzone.classList.add('border-[#0066FF]', 'bg-blue-50/20');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                superDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    superDropzone.classList.remove('border-[#0066FF]', 'bg-blue-50/20');
                });
            });

            superDropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    const input = document.getElementById('super-excel-file-input');
                    if (input) {
                        input.files = files;
                        handleSuperFileSelected(input);
                    }
                }
            });
        }

        // Form Submit Handler for Excel Import
        const superImportForm = document.getElementById('super-excel-import-form');
        if (superImportForm) {
            superImportForm.addEventListener('submit', (e) => {
                const input = document.getElementById('super-excel-file-input');
                if (!input || !input.files || input.files.length === 0) {
                    e.preventDefault();
                    if (window.showToast) window.showToast('Silakan pilih file Excel terlebih dahulu!', 'warning');
                    return;
                }
                const btn = document.getElementById('btn-super-submit-import');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                    btn.textContent = 'Mengimpor...';
                }
                if (window.showToast) window.showToast('Sedang memproses upload dan impor data Excel...', 'info', 6000);
            });
        }

        // ================= GLOBAL ACTION DROPDOWN FOR ADMIN MANAGEMENT =================
        let currentActiveAdmin = null;
        const adminDropdown = document.getElementById('admin-global-action-dropdown');

        function openSmoothDropdown(el) {
            if (!el) return;
            el.classList.remove('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
            el.classList.add('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
        }
        function closeSmoothDropdown(el) {
            if (!el) return;
            el.classList.add('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
            el.classList.remove('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
        }
        function isSmoothDropdownOpen(el) {
            return el && !el.classList.contains('invisible');
        }

        function closeAdminGlobalDropdown() {
            if (adminDropdown) {
                closeSmoothDropdown(adminDropdown);
                adminDropdown.dataset.targetEmail = '';
            }
            currentActiveAdmin = null;
        }

        // Delegasi Klik Tombol Aksi Admin
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.admin-action-btn');
            if (btn) {
                e.stopPropagation();
                const name = btn.dataset.name;
                const email = btn.dataset.email;
                const canDelete = btn.dataset.canDelete === 'true';
                const adminId = btn.dataset.id;
                const row = btn.closest('tr');

                // Jika tombol yang sama diklik lagi saat dropdown terbuka, tutup
                if (isSmoothDropdownOpen(adminDropdown) && adminDropdown.dataset.targetEmail === email) {
                    closeAdminGlobalDropdown();
                    return;
                }

                currentActiveAdmin = { name, email, canDelete, adminId, btn, row };
                adminDropdown.dataset.targetEmail = email;

                // Tampilkan / sembunyikan tombol Hapus untuk role SuperAdmin
                const deleteBtn = document.getElementById('admin-dd-delete-btn');
                if (deleteBtn) {
                    deleteBtn.style.display = canDelete ? 'flex' : 'none';
                }

                // Hitung posisi fixed dropdown
                const rect = btn.getBoundingClientRect();
                const dropWidth = adminDropdown.offsetWidth || 220;
                let left = rect.right - dropWidth;
                let top = rect.bottom + 6;

                // Cek agar tidak offscreen di kanan/kiri
                if (left < 10) left = 10;
                if (left + dropWidth > window.innerWidth) left = window.innerWidth - dropWidth - 10;

                // Cek jika menabrak bagian bawah viewport
                if (top + 160 > window.innerHeight) {
                    top = rect.top - 145;
                }

                adminDropdown.style.top = top + 'px';
                adminDropdown.style.left = left + 'px';
                openSmoothDropdown(adminDropdown);
                return;
            }

            // Klik di luar dropdown akan menutup dropdown
            if (!e.target.closest('#admin-global-action-dropdown')) {
                closeAdminGlobalDropdown();
            }
        });

        // Tutup dropdown saat scroll atau resize window
        window.addEventListener('scroll', closeAdminGlobalDropdown, true);
        window.addEventListener('resize', closeAdminGlobalDropdown);

        function executeAdminAction(type) {
            if (!currentActiveAdmin) return;
            const { name, email, row, adminId } = currentActiveAdmin;
            closeAdminGlobalDropdown();

            if (type === 'nonaktif') {
                actionNonaktif(name);
            } else if (type === 'reset') {
                actionResetSandi(name, email);
            } else if (type === 'delete') {
                actionHapusAdmin(row, name, email, adminId);
            }
        }

        function actionNonaktif(name) {
            if (window.showToast) window.showToast(`Status admin ${name} berhasil diperbarui!`, 'success');
        }

        function actionResetSandi(name, email) {
            if (window.showToast) window.showToast(`Permintaan reset kata sandi untuk ${name} telah diajukan!`, 'info');
        }

        function actionHapusAdmin(row, name, email, adminId) {
            window.showToastConfirm({
                message: `Apakah Anda yakin ingin menghapus akun admin ${name}?`,
                onConfirm: () => {
                    if (!adminId) {
                        if (currentActiveAdmin?.email) {
                            document.querySelectorAll('.admin-row, .admin-card').forEach(el => {
                                if (el.querySelector('.email-col')?.textContent.trim() === currentActiveAdmin.email) {
                                    el.remove();
                                }
                            });
                        } else if (row) {
                            row.remove();
                        }
                        if (window.showToast) window.showToast('Sukses menghapus data admin!', 'success');
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    fetch(`/pengaturan/admins/${adminId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(async res => {
                        if (res.ok) {
                            document.querySelectorAll('.admin-row, .admin-card').forEach(el => {
                                const emailEl = el.querySelector('.email-col');
                                if (emailEl?.textContent.trim() === email) {
                                    el.remove();
                                }
                            });
                            if (window.showToast) window.showToast('Sukses menghapus data admin!', 'success');
                        } else {
                            const data = await res.json().catch(() => ({}));
                            if (window.showToast) window.showToast(data.message || 'Gagal menghapus admin.', 'error');
                        }
                    })
                    .catch(() => {
                        if (window.showToast) window.showToast('Gagal terhubung ke server.', 'error');
                    });
                }
            });
        }

        // ================= SORTING "TERAKHIR AKTIF" =================
        let sortDirection = ''; // '' -> 'desc' -> 'asc' -> 'desc'

        function toggleSortTerakhirAktif() {
            const tbody = document.getElementById('admin-tbody');
            const mobileContainer = document.getElementById('admin-cards-container');

            const arrowUp = document.getElementById('sort-arrow-up');
            const arrowDown = document.getElementById('sort-arrow-down');

            if (sortDirection === 'desc') {
                sortDirection = 'asc';
            } else {
                sortDirection = 'desc';
            }

            // 1. Sort Desktop Rows
            if (tbody) {
                const rows = Array.from(tbody.querySelectorAll('.admin-row'));
                rows.sort((a, b) => {
                    const timeA = new Date(a.dataset.timestamp || 0).getTime();
                    const timeB = new Date(b.dataset.timestamp || 0).getTime();
                    return sortDirection === 'desc' ? timeB - timeA : timeA - timeB;
                });
                rows.forEach(r => tbody.appendChild(r));
            }

            // 2. Sort Mobile Cards
            if (mobileContainer) {
                const cards = Array.from(mobileContainer.querySelectorAll('.admin-card'));
                cards.sort((a, b) => {
                    const timeA = new Date(a.dataset.timestamp || 0).getTime();
                    const timeB = new Date(b.dataset.timestamp || 0).getTime();
                    return sortDirection === 'desc' ? timeB - timeA : timeA - timeB;
                });
                cards.forEach(c => mobileContainer.appendChild(c));
            }

            // Update status visual panah (satu terang/aktif, satu redup)
            if (sortDirection === 'desc') {
                if (arrowDown) {
                    arrowDown.setAttribute('class', 'text-[#0066FF] dark:text-[#3B82F6] opacity-100 transition-all duration-200');
                    arrowDown.setAttribute('fill', 'currentColor');
                }
                if (arrowUp) {
                    arrowUp.setAttribute('class', 'text-gray-300 dark:text-gray-600 opacity-30 transition-all duration-200');
                    arrowUp.setAttribute('fill', 'currentColor');
                }
            } else {
                if (arrowUp) {
                    arrowUp.setAttribute('class', 'text-[#0066FF] dark:text-[#3B82F6] opacity-100 transition-all duration-200');
                    arrowUp.setAttribute('fill', 'currentColor');
                }
                if (arrowDown) {
                    arrowDown.setAttribute('class', 'text-gray-300 dark:text-gray-600 opacity-30 transition-all duration-200');
                    arrowDown.setAttribute('fill', 'currentColor');
                }
            }
        }

        // ================= MODAL TAMBAH ADMIN =================
        function openAddAdminModal() {
            const modal = document.getElementById('modal-tambah-admin');
            if (modal) modal.classList.remove('hidden');
        }

        function closeAddAdminModal() {
            const modal = document.getElementById('modal-tambah-admin');
            if (modal) modal.classList.add('hidden');
            const form = document.getElementById('form-tambah-admin');
            if (form) form.reset();
        }

        function saveNewAdmin(e) {
            e.preventDefault();

            const name     = document.getElementById('new-admin-name').value;
            const username = document.getElementById('new-admin-username').value;
            const email    = document.getElementById('new-admin-email').value;
            const password = document.getElementById('new-admin-password').value;
            const passwordConfirmation = document.getElementById('new-admin-password-confirmation').value;
            const errorBox  = document.getElementById('form-admin-error');
            const submitBtn = e.target.querySelector('[type="submit"]');

            errorBox.classList.add('hidden');
            errorBox.textContent = '';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const formData  = new FormData();
            formData.append('_token', csrfToken);
            formData.append('name', name);
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('password_confirmation', passwordConfirmation);

            fetch('{{ route("admins.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData,
            })
            .then(async res => {
                if (res.ok) {
                    closeAddAdminModal();
                    if (window.showToast) window.showToast(`Admin ${name} berhasil ditambahkan!`, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                    return;
                }
                const data = await res.json().catch(() => ({}));
                if (data.errors) {
                    const messages = Object.values(data.errors).flat().join(' ');
                    errorBox.textContent = messages;
                    errorBox.classList.remove('hidden');
                } else if (res.status === 401 || res.status === 403) {
                    errorBox.textContent = 'Sesi habis. Silakan refresh halaman dan login ulang.';
                    errorBox.classList.remove('hidden');
                } else {
                    errorBox.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                    errorBox.classList.remove('hidden');
                }
            })
            .catch(() => {
                errorBox.textContent = 'Gagal terhubung ke server.';
                errorBox.classList.remove('hidden');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Admin';
            });
        }

        // Persetujuan Cards Actions
        function approveApprovalCard(id, email) {
            const card = document.getElementById(`req-card-${id}`);
            if (card) {
                card.remove();
            }
            decrementBadge();
            if (window.showToast) window.showToast('Sukses menyetujui permintaan! Kode OTP telah dikirim ke email.', 'success');
        }

        function rejectApprovalCard(id) {
            window.showToastConfirm({
                message: 'Apakah Anda yakin ingin menolak permintaan reset kata sandi ini?',
                onConfirm: () => {
                    const card = document.getElementById(`req-card-${id}`);
                    if (card) {
                        card.remove();
                    }
                    decrementBadge();
                    if (window.showToast) window.showToast('Sukses menolak permintaan reset kata sandi.', 'info');
                }
            });
        }

        function decrementBadge() {
            const badge = document.getElementById('badge-pending-count');
            if (badge) {
                const count = Math.max(0, parseInt(badge.textContent) - 1);
                badge.textContent = count;
                if (count === 0) badge.classList.add('hidden');
            }
        }

        // ================= CUSTOM FILTER DROPDOWNS LOGIC (JATUH TEMPO STYLE) =================
        let currentRoleFilter = '';
        let currentWaktuFilter = '';

        document.addEventListener('click', (e) => {
            const filterBtn = e.target.closest('.filter-dropdown-btn');
            const allFilterMenus = document.querySelectorAll('.filter-dropdown-menu');
            const allFilterArrows = document.querySelectorAll('.filter-dropdown-arrow');

            if (filterBtn) {
                e.stopPropagation();
                const container = filterBtn.closest('.custom-filter-container');
                const menu = container ? container.querySelector('.filter-dropdown-menu') : null;
                const arrow = filterBtn.querySelector('.filter-dropdown-arrow');
                const wasOpen = isSmoothDropdownOpen(menu);

                allFilterMenus.forEach(closeSmoothDropdown);
                allFilterArrows.forEach(a => a.classList.remove('rotate-180'));
                closeAdminGlobalDropdown();

                if (!wasOpen && menu) {
                    openSmoothDropdown(menu);
                    if (arrow) arrow.classList.add('rotate-180');
                }
            } else if (!e.target.closest('.filter-dropdown-menu')) {
                allFilterMenus.forEach(closeSmoothDropdown);
                allFilterArrows.forEach(a => a.classList.remove('rotate-180'));
            }
        });

        // Filter Role Manajemen Admin
        function filterRoleSettings(val, label) {
            currentRoleFilter = val;
            const lbl = document.getElementById('label-role');
            if (lbl) {
                lbl.textContent = val ? label : 'Role';
                lbl.className = val ? 'text-gray-800 dark:text-white font-semibold text-[11px] sm:text-xs select-none' : 'text-gray-400 dark:text-[#9AA0A6] font-normal text-[11px] sm:text-xs select-none';
            }
            document.querySelectorAll('.filter-dropdown-menu').forEach(closeSmoothDropdown);
            document.querySelectorAll('.filter-dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
            filterAdminTable();
        }

        function filterAdminTable() {
            const query = (document.getElementById('search-admin-input')?.value || '').toLowerCase();
            const role = currentRoleFilter.toLowerCase();
            const rows = document.querySelectorAll('.admin-row');
            const cards = document.querySelectorAll('.admin-card');

            // Filter Desktop Rows
            rows.forEach(r => {
                const email = (r.querySelector('.email-col')?.textContent || '').toLowerCase();
                const name = (r.querySelector('.name-col')?.textContent || '').toLowerCase();
                const rRole = (r.querySelector('.role-col')?.textContent || '').toLowerCase();

                const matchQuery = !query || email.includes(query) || name.includes(query);
                const matchRole = !role || rRole.includes(role);

                r.style.display = (matchQuery && matchRole) ? '' : 'none';
            });

            // Filter Mobile Cards
            cards.forEach(c => {
                const email = (c.querySelector('.email-col')?.textContent || '').toLowerCase();
                const name = (c.querySelector('.name-col')?.textContent || '').toLowerCase();
                const rRole = (c.querySelector('.role-col')?.textContent || '').toLowerCase();

                const matchQuery = !query || email.includes(query) || name.includes(query);
                const matchRole = !role || rRole.includes(role);

                c.style.display = (matchQuery && matchRole) ? '' : 'none';
            });
        }

        // Filter Persetujuan Reset Sandi
        function filterWaktuApproval(val, label) {
            currentWaktuFilter = val;
            const lbl = document.getElementById('label-waktu');
            if (lbl) {
                lbl.textContent = val ? label : 'Waktu Pengajuan';
                lbl.className = val ? 'text-gray-800 dark:text-white font-semibold text-[11px] sm:text-xs select-none' : 'text-gray-400 dark:text-[#9AA0A6] font-normal text-[11px] sm:text-xs select-none';
            }
            document.querySelectorAll('.filter-dropdown-menu').forEach(closeSmoothDropdown);
            document.querySelectorAll('.filter-dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
            filterApprovalCards();
        }

        function filterApprovalCards() {
            const query = (document.getElementById('search-approval-input')?.value || '').toLowerCase();
            const container = document.getElementById('requests-list-container');
            if (!container) return;

            const cards = Array.from(container.querySelectorAll('.approval-card-item'));

            if (currentWaktuFilter === 'terbaru') {
                cards.sort((a, b) => new Date(b.dataset.timestamp || 0).getTime() - new Date(a.dataset.timestamp || 0).getTime());
                cards.forEach(c => container.appendChild(c));
            } else if (currentWaktuFilter === 'terlama') {
                cards.sort((a, b) => new Date(a.dataset.timestamp || 0).getTime() - new Date(b.dataset.timestamp || 0).getTime());
                cards.forEach(c => container.appendChild(c));
            }

            cards.forEach(c => {
                const text = c.textContent.toLowerCase();
                c.style.display = (!query || text.includes(query)) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const urlTab = new URLSearchParams(window.location.search).get('tab') || "{{ $activeTab ?? 'profil' }}";
            switchSuperTab(urlTab === 'profil-saya' ? 'profil' : urlTab);
        });
    </script>

<x-temp-password-guard />
</body>
</html>
