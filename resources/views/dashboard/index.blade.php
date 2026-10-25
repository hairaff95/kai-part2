<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Dashboard — KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="dashboard" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-5 lg:pt-4 pb-28 lg:pb-5 flex flex-col lg:min-h-0">

        <!-- Page Header -->
        <div class="mb-3 sm:mb-4 shrink-0">
            <h1 class="text-xl sm:text-[30px] lg:text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Dashboard
            </h1>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 lg:gap-3.5 lg:items-stretch">

            <!-- Kolom Kiri -->
            <div class="lg:col-span-8 flex flex-col gap-3.5 sm:gap-4 lg:gap-3.5">

                <!-- 4 Kartu Statistik -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3.5 shrink-0">

                    <!-- Kartu 1: Total Kontrak -->
                    <a href="{{ route('contracts.index') }}"
                       class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 lg:p-3.5 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[105px] sm:h-[130px] lg:h-[110px] transition hover:shadow-md dark:hover:border-white/10 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <div class="flex items-start justify-between">
                            <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-tight">Total Kontrak</p>
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium">{{ $totalKontrak }} kontrak</p>
                        </div>
                    </a>

                    <!-- Kartu 2: Kontrak Aktif -->
                    <a href="{{ route('contracts.index', ['status_kontrak' => 'aktif']) }}"
                       class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 lg:p-3.5 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[105px] sm:h-[130px] lg:h-[110px] transition hover:shadow-md dark:hover:border-white/10 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <div class="flex items-start justify-between">
                            <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-tight">Kontrak Aktif</p>
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium">{{ $kontrakAktif }} kontrak</p>
                        </div>
                    </a>

                    <!-- Kartu 3: Backlog -->
                    <a href="{{ route('contracts.index', ['status_kontrak' => 'backlog']) }}"
                       class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 lg:p-3.5 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[105px] sm:h-[130px] lg:h-[110px] transition hover:shadow-md dark:hover:border-white/10 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <div class="flex items-start justify-between">
                            <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-tight">Backlog</p>
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium">{{ $backlog }} kontrak</p>
                        </div>
                    </a>

                    <!-- Kartu 4: Total Tagihan -->
                    <a href="{{ route('payments.index') }}"
                       class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 lg:p-3.5 border border-gray-100/90 dark:border-white/5 flex flex-col justify-between h-[105px] sm:h-[130px] lg:h-[110px] transition hover:shadow-md dark:hover:border-white/10 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <div class="flex items-start justify-between">
                            <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-tight">Total Tagihan</p>
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium">{{ $totalTagihanFormatted }}</p>
                        </div>
                    </a>

                </div>

                <!-- Chart Tagihan per Bulan -->
                <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-5 lg:p-4.5 border border-gray-100/90 dark:border-white/5 flex flex-col gap-2.5 sm:gap-3 flex-1 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <h2 class="text-xs sm:text-base font-semibold text-gray-900 dark:text-white">
                        Tagihan per Bulan ({{ now()->year }})
                    </h2>

                    <div class="relative h-[135px] sm:h-[155px] lg:h-[130px] w-full mb-1">
                        <!-- Y-axis grid lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-4">
                            @foreach($yGridLabels as $yLabel)
                                <div class="flex items-center gap-2">
                                    <span class="w-7 sm:w-8 text-[9px] sm:text-[10px] text-gray-400 dark:text-[#787E87] text-right shrink-0">{{ $yLabel }}</span>
                                    <div class="h-px bg-gray-100 dark:bg-white/10 flex-1"></div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Chart SVG -->
                        <div class="absolute inset-0 pl-9 sm:pl-11 pb-4">
                            <svg viewBox="0 0 1000 200" preserveAspectRatio="none" class="w-full h-full overflow-visible">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#1570EF" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="#1570EF" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>
                                <path d="{{ $lightWavePath }}" fill="none" stroke="#84ADFF" stroke-width="1.8" stroke-linecap="round" />
                                <path d="{{ $gradientFillPath }}" fill="url(#chartGradient)" />
                                <path d="{{ $primaryWavePath }}" fill="none" stroke="#1570EF" stroke-width="2.3" stroke-linecap="round" />
                            </svg>

                            <!-- X-axis labels -->
                            <div class="absolute bottom-0 inset-x-0 flex justify-between text-[9px] sm:text-[10px] text-gray-400 dark:text-[#787E87] font-medium">
                                @foreach($monthlyRaw as $m)
                                    <span>{{ $m['key'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Jatuh Tempo Terdekat -->
                    <div class="pt-2.5 border-t border-gray-100/90 dark:border-white/10">
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white mb-1.5">Jatuh Tempo 90 Hari ke Depan</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[10px] sm:text-xs min-w-full">
                                <thead>
                                    <tr class="text-gray-400 dark:text-[#787E87] border-b border-gray-100 dark:border-white/10 whitespace-nowrap">
                                        <th class="pb-1.5 pr-2 font-normal">Kelompok</th>
                                        <th class="pb-1.5 px-2 font-normal">Nama Mitra</th>
                                        <th class="pb-1.5 px-2 font-normal">Jatuh Tempo</th>
                                        <th class="pb-1.5 pl-2 font-normal text-right">Sisa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                    @forelse($upcomingContracts as $uc)
                                        <tr class="whitespace-nowrap">
                                            <td class="py-1.5 pr-2 font-semibold text-gray-900 dark:text-white">{{ $uc['kelompok'] }}</td>
                                            <td class="py-1.5 px-2 text-gray-700 dark:text-gray-300 max-w-[150px] truncate">{{ $uc['nama'] }}</td>
                                            <td class="py-1.5 px-2 text-gray-600 dark:text-gray-400">{{ $uc['jatuh_tempo'] }}</td>
                                            <td class="py-1.5 pl-2 text-right">
                                                <span class="inline-block rounded-md bg-[#FFF4E5] dark:bg-amber-900/40 px-2 py-0.5 text-[9px] sm:text-[10px] font-semibold text-amber-600 dark:text-amber-400 whitespace-nowrap">{{ $uc['sisa'] }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="py-3 text-center text-gray-400 text-xs">Tidak ada kontrak yang akan jatuh tempo dalam 90 hari.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Distribusi Kelompok -->
            <div class="lg:col-span-4 flex flex-col gap-3.5 sm:gap-4 lg:gap-3.5">

                <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-5 lg:p-4.5 border border-gray-100/90 dark:border-white/5 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex flex-col gap-3">
                    <h2 class="text-xs sm:text-base font-semibold text-gray-950 dark:text-white">Distribusi Kelompok</h2>

                    @if(count($kelompokData) > 0)
                        <!-- Bar Chart -->
                        <div class="flex items-stretch gap-0.5 h-[40px] sm:h-[50px] rounded-xl overflow-hidden">
                            @foreach($kelompokData as $k)
                                <div class="h-full rounded-sm transition-all" style="width: {{ $k['percentage'] }}%; background-color: {{ $k['color'] }};" title="{{ $k['name'] }}: {{ $k['percentage'] }}%"></div>
                            @endforeach
                        </div>

                        <!-- Legenda -->
                        <div class="space-y-2 sm:space-y-2.5 pt-1">
                            @foreach($kelompokData as $k)
                                <div class="flex items-center justify-between text-[11px] sm:text-sm">
                                    <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                        <span class="h-2 w-2 rounded-full shrink-0" style="background-color: {{ $k['color'] }};"></span>
                                        {{ $k['name'] }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $k['total'] }} ({{ $k['percentage'] }}%)</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400 dark:text-[#9AA0A6]">
                            <svg class="w-10 h-10 mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <p class="text-xs">Belum ada data kontrak</p>
                        </div>
                    @endif
                </div>

                <!-- Status Kontrak Summary -->
                <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#659DF8] via-[#2F7EF8] to-[#0062F5] p-4 sm:p-5 lg:p-4.5 text-white shadow-[0_4px_20px_rgba(21,112,239,0.2)] flex flex-col gap-3 flex-1">
                    <h2 class="text-xs sm:text-base font-semibold text-white">Status Kontrak</h2>

                    <div class="grid grid-cols-1 gap-2.5">
                        <!-- Aktif -->
                        <div class="flex items-center justify-between bg-white/15 rounded-xl px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-300 shrink-0"></span>
                                <span class="text-xs sm:text-sm font-semibold text-white">Kontrak Aktif</span>
                            </div>
                            <span class="text-sm sm:text-base font-bold text-white">{{ $kontrakAktif }}</span>
                        </div>

                        <!-- Backlog -->
                        <div class="flex items-center justify-between bg-white/15 rounded-xl px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-red-300 shrink-0"></span>
                                <span class="text-xs sm:text-sm font-semibold text-white">Backlog</span>
                            </div>
                            <span class="text-sm sm:text-base font-bold text-white">{{ $backlog }}</span>
                        </div>

                        <!-- Total -->
                        <div class="flex items-center justify-between bg-white/10 rounded-xl px-3 py-2.5 border border-white/20">
                            <span class="text-xs sm:text-sm font-semibold text-white/80">Total</span>
                            <span class="text-sm sm:text-base font-bold text-white">{{ $totalKontrak }}</span>
                        </div>
                    </div>

                    <a href="{{ route('contracts.index') }}"
                       class="mt-auto flex items-center justify-center gap-1.5 bg-white/20 hover:bg-white/30 rounded-xl py-2.5 text-xs font-semibold text-white transition active:scale-95">
                        <span>Lihat Semua Kontrak</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </main>

    <x-toast />
    <x-temp-password-guard />
</body>
</html>
