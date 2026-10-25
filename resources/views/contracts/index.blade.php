<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Daftar Kontrak — KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="contracts" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-6 pb-28 lg:pb-8 flex flex-col gap-4 sm:gap-5">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
            <h1 class="text-xl sm:text-[30px] font-bold tracking-tight text-gray-950 dark:text-white">
                Daftar Kontrak
            </h1>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                    <a href="{{ route('contracts.create') }}"
                       class="flex items-center gap-1.5 sm:gap-2 rounded-lg lg:rounded-[10px] bg-[#0066FF] hover:bg-blue-700 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-xs transition active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Kontrak</span>
                    </a>
                @endif
                <button id="btn-filter" type="button"
                    class="flex items-center gap-1.5 sm:gap-2 rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/10 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 dark:text-white shadow-xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('contracts.index') }}"
                   class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-lg lg:rounded-[10px] border border-gray-200 dark:border-white/10 bg-white dark:bg-[#2D3034] hover:bg-gray-50 dark:hover:bg-white/10 shadow-xs transition cursor-pointer" title="Reset">
                    <svg class="w-4 h-4 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </div>

        <!-- Card: Filter + Tabel -->
        <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-5 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-2.5 sm:gap-4 transition-colors">

            <!-- Filter Bar -->
            <form id="filter-form" method="GET" action="{{ route('contracts.index') }}" class="flex flex-wrap items-center justify-between gap-1.5 sm:gap-2.5 w-full">

                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2.5 w-full sm:w-auto">

                    <!-- Search -->
                    <div class="relative flex items-center flex-1 min-w-[130px] sm:flex-none sm:w-[185px] h-[34px] sm:h-[38px]">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 dark:text-[#9AA0A6] absolute left-2.5 sm:left-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search kontrak, mitra..."
                            class="w-full h-full pl-8 sm:pl-9 pr-3 py-1 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-lg lg:rounded-[10px] focus:outline-none focus:border-[#0066FF] text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition">
                    </div>

                    <!-- Hidden inputs -->
                    <input type="hidden" name="kelompok" id="input-kelompok" value="{{ request('kelompok') }}">
                    <input type="hidden" name="status_mitra" id="input-status-mitra" value="{{ request('status_mitra') }}">
                    <input type="hidden" name="status_kontrak" id="input-status-kontrak" value="{{ request('status_kontrak') }}">

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

                    <!-- Filter Status Kontrak -->
                    @php
                        $statusKontrakLabels = ['' => 'Semua Status Kontrak', 'aktif' => 'Kontrak Aktif', 'backlog' => 'Backlog', 'tanpa_tanggal' => 'Belum Ada Tanggal Akhir'];
                        $currentSkLabel = $statusKontrakLabels[request('status_kontrak', '')] ?? 'Semua Status Kontrak';
                    @endphp
                    <div class="relative custom-filter-container">
                        <button type="button" class="filter-dropdown-btn inline-flex items-center gap-1 h-[34px] sm:h-[38px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3 py-1 transition cursor-pointer">
                            <span class="text-gray-400 dark:text-[#9AA0A6] font-normal text-xs select-none">Status:</span>
                            <span id="label-status-kontrak" class="{{ request('status_kontrak') ? 'text-black dark:text-white font-semibold' : 'text-gray-800 dark:text-white font-semibold' }} text-xs select-none">{{ $currentSkLabel }}</span>
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] ml-0.5 pointer-events-none transition-transform duration-200 filter-dropdown-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="filter-dropdown-menu opacity-0 invisible scale-95 pointer-events-none transition-all duration-200 origin-top-left absolute left-0 top-full mt-1 z-[100] min-w-[200px] max-h-[220px] overflow-y-auto rounded-lg lg:rounded-[10px] bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1.5 flex flex-col gap-0.5">
                            @foreach($statusKontrakLabels as $val => $lbl)
                                <button type="button" onclick="selectFilter('status_kontrak', '{{ $val }}')" class="flex items-center justify-between w-full px-2.5 py-1.5 text-xs font-semibold {{ request('status_kontrak', '') === (string)$val ? 'bg-blue-50 dark:bg-blue-900/30 text-black dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10' }} rounded-lg transition text-left cursor-pointer">
                                    <span>{{ $lbl }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

            </form>

            <!-- MOBILE CARD VIEW -->
            <div class="sm:hidden flex flex-col gap-3">
                @forelse($contracts as $item)
                    <div class="rounded-xl border border-gray-200/90 dark:border-white/10 bg-white dark:bg-[#1F2123] p-3.5 flex flex-col gap-2.5">
                        <div class="flex items-start justify-between gap-2 border-b border-gray-100 dark:border-white/10 pb-2">
                            <div>
                                <span class="text-[10px] font-semibold text-gray-400 dark:text-[#9AA0A6] uppercase tracking-wider">No Kontrak</span>
                                <p class="text-xs font-bold text-gray-900 dark:text-white leading-snug">{{ $item->nomor_kontrak }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ strtolower($item->tenant?->status_mitra ?? '') === 'swasta' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800' }}">
                                    {{ $item->tenant?->status_mitra ?? 'Swasta' }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $item->status_kontrak_badge }}">
                                    {{ $item->status_kontrak }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->tenant?->nama_mitra ?? '-' }}</h3>
                            <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mt-0.5">{{ $item->asset?->blok_aset ?? '-' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 bg-gray-50/90 dark:bg-[#2D3034] rounded-lg p-2.5 text-xs">
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Kelompok</span>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $item->kelompok ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Jenis Aset</span>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $item->asset?->jenis_aset ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Masa Kontrak</span>
                                <span class="font-medium text-gray-800 dark:text-white text-[11px]">
                                    {{ $item->masa_awal?->format('d/m/y') ?? '-' }} — {{ $item->masa_akhir?->format('d/m/y') ?? '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-[#9AA0A6] block text-[10px]">Sisa</span>
                                <span class="font-semibold text-gray-800 dark:text-white text-[11px]">{{ $item->sisa_hari }}</span>
                            </div>
                        </div>

                        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                            <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100 dark:border-white/10">
                                <a href="{{ route('contracts.edit', $item->nomor_kontrak) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('contracts.destroy', $item->nomor_kontrak) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini beserta seluruh data tagihan terkait?')">
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
                        Tidak ada data kontrak.
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
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Blok Aset</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Jenis Aset</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Masa Awal</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Masa Akhir</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Sisa</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Status Mitra</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] whitespace-nowrap">Status Kontrak</th>
                                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-[#9AA0A6] text-right whitespace-nowrap">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10 text-[13px] text-gray-800 dark:text-gray-200">
                            @forelse($contracts as $item)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-white/5 transition-colors">
                                    <td class="py-3.5 px-4 font-normal text-gray-900 dark:text-white whitespace-nowrap">{{ $item->nomor_kontrak }}</td>
                                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $item->tenant?->nama_mitra ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $item->kelompok ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 max-w-[240px] leading-snug">{{ $item->asset?->blok_aset ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $item->asset?->jenis_aset ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $item->masa_awal?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $item->masa_akhir?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400 whitespace-nowrap text-xs">{{ $item->sisa_hari }}</td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                            {{ strtolower($item->tenant?->status_mitra ?? '') === 'swasta' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800' }}">
                                            {{ $item->tenant?->status_mitra ?? 'Swasta' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $item->status_kontrak_badge }}">
                                            {{ $item->status_kontrak }}
                                        </span>
                                    </td>
                                    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5 justify-end">
                                                <a href="{{ route('contracts.edit', $item->nomor_kontrak) }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#0066FF] dark:text-[#3B82F6] hover:bg-blue-100 dark:hover:bg-blue-900/50 transition" title="Edit Kontrak">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit</span>
                                                </a>
                                                <form action="{{ route('contracts.destroy', $item->nomor_kontrak) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini beserta data tagihan terkait?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/60 transition cursor-pointer" title="Hapus Kontrak">
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
                                    <td colspan="{{ auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()) ? 11 : 10 }}" class="text-center py-8 text-gray-400 dark:text-[#9AA0A6]">
                                        Tidak ada data kontrak yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <x-pagination :paginator="$contracts" />

        </div>
    </main>

    <x-toast />

    <script>
    (function () {
        const filterLabelMap = {
            kelompok:       { id: 'label-kelompok',        default: 'Kelompok',              inputId: 'input-kelompok' },
            status_mitra:   { id: 'label-status-mitra',    default: 'Status Mitra',           inputId: 'input-status-mitra' },
            status_kontrak: { id: 'label-status-kontrak',  default: 'Semua Status Kontrak',   inputId: 'input-status-kontrak',
                lookup: @json($statusKontrakLabels) },
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
                lbl.textContent = value ? (cfg.lookup ? (cfg.lookup[value] ?? value) : value) : cfg.default;
                lbl.className = value
                    ? 'text-black dark:text-white font-semibold text-xs select-none'
                    : (name === 'status_kontrak' ? 'text-gray-800 dark:text-white font-semibold text-xs select-none' : 'text-gray-400 dark:text-[#9AA0A6] font-normal text-xs select-none');
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
