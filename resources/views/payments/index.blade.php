<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Data Tagihan — KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="payments" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-6 pb-28 lg:pb-8 flex flex-col gap-4 sm:gap-5">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
            <div>
                <h1 class="text-xl sm:text-[30px] font-bold tracking-tight text-gray-950 dark:text-white">Data Tagihan</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] mt-0.5">Rekap data tagihan per termin kontrak</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                    <a href="{{ route('payments.create') }}"
                       class="flex items-center gap-1.5 sm:gap-2 rounded-lg lg:rounded-[10px] bg-[#0066FF] hover:bg-blue-700 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-xs transition active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Tagihan</span>
                    </a>
                @endif
                <button id="btn-filter" type="button"
                    class="flex items-center gap-1.5 sm:gap-2 rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/10 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 dark:text-white shadow-xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('payments.index') }}"
                   class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-lg lg:rounded-[10px] border border-gray-200 dark:border-white/10 bg-white dark:bg-[#2D3034] hover:bg-gray-50 dark:hover:bg-white/10 shadow-xs transition cursor-pointer" title="Reset">
                    <svg class="w-4 h-4 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3.5 shrink-0">
            <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[90px] sm:h-[110px] shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="h-7 w-7 sm:h-9 sm:w-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Total Tagihan</p>
                    <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium">{{ $payments->total() }} record</p>
                </div>
            </div>

            <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[90px] sm:h-[110px] shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="h-7 w-7 sm:h-9 sm:w-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Total Nilai</p>
                    <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium truncate">{{ $totalTagihanFmt }}</p>
                </div>
            </div>

            <div class="col-span-2 sm:col-span-1 rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[90px] sm:h-[110px] shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="h-7 w-7 sm:h-9 sm:w-9 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Halaman ini</p>
                    <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium">{{ $payments->count() }} dari {{ $payments->total() }} record</p>
                </div>
            </div>
        </div>

        <!-- Card: Filter + Tabel -->
        <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-5 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-2.5 sm:gap-4 transition-colors">

            <!-- Filter Bar -->
            <form id="filter-form" method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap items-center gap-1.5 sm:gap-2.5 w-full">

                <!-- Search -->
                <div class="relative flex items-center flex-1 min-w-[130px] sm:flex-none sm:w-[200px] h-[34px] sm:h-[38px]">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 dark:text-[#9AA0A6] absolute left-2.5 sm:left-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no kontrak, mitra, invoice..."
                        class="w-full h-full pl-8 sm:pl-9 pr-3 py-1 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-lg lg:rounded-[10px] focus:outline-none focus:border-[#0066FF] text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition">
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="kelompok" id="input-kelompok" value="{{ request('kelompok') }}">
                <input type="hidden" name="status_mitra" id="input-status-mitra" value="{{ request('status_mitra') }}">

                <!-- Filter Kelompok -->
                <div class="relative custom-filter-container">
                    <button type="button" class="filter-dropdown-btn inline-flex items-center h-[34px] sm:h-[38px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3 py-1 transition cursor-pointer">
                        <span id="label-kelompok" class="{{ request('kelompok') ? 'text-black dark:text-white font-semibold' : 'text-gray-400 dark:text-[#9AA0A6] font-normal' }} text-xs select-none">
                            {{ request('kelompok') ?: 'Kelompok' }}
                        </span>
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] ml-1 pointer-events-none transition-transform duration-200 filter-dropdown-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="filter-dropdown-menu opacity-0 invisible scale-95 pointer-events-none transition-all duration-200 origin-top-left absolute left-0 top-full mt-1 z-[100] min-w-[160px] max-h-[220px] overflow-y-auto rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectFilter('kelompok', '')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-xs font-semibold {{ !request('kelompok') ? 'bg-blue-50 dark:bg-blue-900/30 text-black dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10' }} rounded-lg transition text-left cursor-pointer">
                            <span>Semua Kelompok</span>
                        </button>
                        @foreach($kelompokOptions as $opt)
                            <button type="button" onclick="selectFilter('kelompok', '{{ $opt }}')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-xs font-semibold {{ request('kelompok') === $opt ? 'bg-blue-50 dark:bg-blue-900/30 text-black dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10' }} rounded-lg transition text-left cursor-pointer">
                                <span>{{ $opt }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Filter Status Mitra -->
                <div class="relative custom-filter-container">
                    <button type="button" class="filter-dropdown-btn inline-flex items-center h-[34px] sm:h-[38px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3 py-1 transition cursor-pointer">
                        <span id="label-status-mitra" class="{{ request('status_mitra') ? 'text-black dark:text-white font-semibold' : 'text-gray-400 dark:text-[#9AA0A6] font-normal' }} text-xs select-none">
                            {{ request('status_mitra') ?: 'Status Mitra' }}
                        </span>
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] ml-1 pointer-events-none transition-transform duration-200 filter-dropdown-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="filter-dropdown-menu opacity-0 invisible scale-95 pointer-events-none transition-all duration-200 origin-top-left absolute left-0 top-full mt-1 z-[100] min-w-[160px] max-h-[220px] overflow-y-auto rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectFilter('status_mitra', '')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-xs font-semibold {{ !request('status_mitra') ? 'bg-blue-50 dark:bg-blue-900/30 text-black dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10' }} rounded-lg transition text-left cursor-pointer">
                            <span>Semua Status</span>
                        </button>
                        @foreach($statusMitraOptions as $opt)
                            <button type="button" onclick="selectFilter('status_mitra', '{{ $opt }}')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-xs font-semibold {{ request('status_mitra') === $opt ? 'bg-blue-50 dark:bg-blue-900/30 text-black dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10' }} rounded-lg transition text-left cursor-pointer">
                                <span>{{ $opt }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

            </form>

            <!-- MOBILE CARD VIEW -->
            <div class="sm:hidden flex flex-col gap-3">
                @forelse($payments as $row)
                    <div class="rounded-xl border border-gray-200/90 dark:border-white/10 bg-white dark:bg-[#1F2123] p-3.5 flex flex-col gap-2.5">
                        <div class="flex items-start justify-between gap-2 border-b border-gray-100 dark:border-white/10 pb-2">
                            <div>
                                <span class="text-[10px] font-semibold text-gray-400 dark:text-[#9AA0A6] uppercase tracking-wider">No Kontrak</span>
                                <p class="text-xs font-bold text-gray-900 dark:text-white leading-snug">{{ $row->nomor_kontrak }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800 whitespace-nowrap">
                                Termin {{ $row->termin_ke }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $row->contract?->tenant?->nama_mitra ?? '-' }}</h3>
                            <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mt-0.5">{{ $row->contract?->kelompok ?? '-' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 bg-gray-50/90 dark:bg-[#2D3034] rounded-lg p-2.5 text-xs">
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Virtual Account</span>
                                <span class="font-semibold text-gray-800 dark:text-white text-[11px]">{{ $row->virtual_account ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Tanggal Termin</span>
                                <span class="font-semibold text-gray-800 dark:text-white text-[11px]">{{ $row->tanggal_termin?->format('d/m/Y') ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Total Bayar</span>
                                <span class="font-bold text-gray-900 dark:text-white text-[11px]">{{ $row->total_bayar_formatted }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Invoice ID</span>
                                <span class="font-medium text-gray-800 dark:text-white text-[11px] truncate block">{{ $row->invoice_id ?? '-' }}</span>
                            </div>
                        </div>

                        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                            <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100 dark:border-white/10">
                                <a href="{{ route('payments.edit', $row->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('payments.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tagihan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/60 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-gray-400 dark:text-[#9AA0A6] bg-gray-50 dark:bg-[#2D3034] rounded-xl">
                        Tidak ada data tagihan.
                    </div>
                @endforelse
            </div>

            <!-- DESKTOP TABLE VIEW -->
            <div class="hidden sm:block border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden bg-white dark:bg-[#1F2123]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FA] dark:bg-[#282A2C] border-b border-gray-200 dark:border-white/10">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">No Kontrak</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Nama Mitra</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Kelompok</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Termin Ke</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Virtual Account</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Tanggal Termin</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Total Bayar</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Invoice ID</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Transaction ID</th>
                                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] text-right whitespace-nowrap">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10 text-[13px] text-gray-800 dark:text-gray-200">
                            @forelse($payments as $row)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-white/5 transition-colors">
                                    <td class="py-3.5 px-4 font-normal text-gray-900 dark:text-white whitespace-nowrap">{{ $row->nomor_kontrak }}</td>
                                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $row->contract?->tenant?->nama_mitra ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $row->contract?->kelompok ?? '-' }}</td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800">
                                            Termin {{ $row->termin_ke }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap font-mono text-xs">{{ $row->virtual_account ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $row->tanggal_termin?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $row->total_bayar_formatted }}</td>
                                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400 whitespace-nowrap font-mono text-xs">{{ $row->invoice_id ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400 whitespace-nowrap font-mono text-xs">{{ $row->transaction_id ?? '-' }}</td>
                                    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5 justify-end">
                                                <a href="{{ route('payments.edit', $row->id) }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] hover:bg-blue-100 dark:hover:bg-blue-900/50 transition" title="Edit Tagihan">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit</span>
                                                </a>
                                                <form action="{{ route('payments.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tagihan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/60 transition cursor-pointer" title="Hapus Tagihan">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()) ? 10 : 9 }}" class="text-center py-8 text-gray-400 dark:text-[#9AA0A6]">
                                        Tidak ada data tagihan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <x-pagination :paginator="$payments" />

        </div>
    </main>

    <x-toast />

    <script>
    (function () {
        const filterLabelMap = {
            kelompok:     { id: 'label-kelompok',     default: 'Kelompok',     inputId: 'input-kelompok' },
            status_mitra: { id: 'label-status-mitra', default: 'Status Mitra', inputId: 'input-status-mitra' },
        };

        function openMenu(el) { el.classList.remove('opacity-0','invisible','scale-95','pointer-events-none'); el.classList.add('opacity-100','visible','scale-100','pointer-events-auto'); }
        function closeMenu(el) { el.classList.add('opacity-0','invisible','scale-95','pointer-events-none'); el.classList.remove('opacity-100','visible','scale-100','pointer-events-auto'); }
        function isOpen(el) { return el && !el.classList.contains('invisible'); }

        window.selectFilter = function (name, value) {
            const cfg = filterLabelMap[name];
            if (!cfg) return;
            const input = document.getElementById(cfg.inputId);
            const lbl   = document.getElementById(cfg.id);
            if (input) input.value = value;
            if (lbl) {
                lbl.textContent = value || cfg.default;
                lbl.className = value
                    ? 'text-black dark:text-white font-semibold text-xs select-none'
                    : 'text-gray-400 dark:text-[#9AA0A6] font-normal text-xs select-none';
            }
            document.querySelectorAll('.filter-dropdown-menu').forEach(closeMenu);
            document.querySelectorAll('.filter-dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
        };

        document.getElementById('btn-filter')?.addEventListener('click', () => {
            document.getElementById('filter-form').submit();
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.filter-dropdown-btn');
            const allMenus = document.querySelectorAll('.filter-dropdown-menu');
            const allArrows = document.querySelectorAll('.filter-dropdown-arrow');

            if (btn) {
                e.stopPropagation();
                const container = btn.closest('.custom-filter-container');
                const menu = container?.querySelector('.filter-dropdown-menu');
                const arrow = btn.querySelector('.filter-dropdown-arrow');
                const wasOpen = isOpen(menu);

                allMenus.forEach(closeMenu);
                allArrows.forEach(a => a.classList.remove('rotate-180'));

                if (!wasOpen && menu) {
                    openMenu(menu);
                    if (arrow) arrow.classList.add('rotate-180');
                }
            } else if (!e.target.closest('.filter-dropdown-menu')) {
                allMenus.forEach(closeMenu);
                allArrows.forEach(a => a.classList.remove('rotate-180'));
            }
        });
    })();
    </script>
</body>
</html>
