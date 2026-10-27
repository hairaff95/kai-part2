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
    {{-- Chart.js untuk Grafik Dinamis Real-Time --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="dashboard" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-5 lg:pt-4 pb-28 lg:pb-8 flex flex-col">

        <!-- Page Header -->
        <div class="mb-3.5 sm:mb-4 shrink-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-[30px] lg:text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Dashboard
                    </h1>
                    @auth
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/40 text-[#0066FF] dark:text-[#3B82F6] border border-blue-200/60 dark:border-blue-800/40">
                            {{ auth()->user()->role_label }}
                        </span>
                    @endauth
                </div>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Monitoring kinerja kontrak, akrual crowl harian, dan profil potensi aset Daop 4.
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('map.index') }}"
                   class="h-9 px-3.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1F2123] hover:bg-gray-50 dark:hover:bg-[#2D3034] text-xs font-semibold text-gray-800 dark:text-white transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Peta Potensi Aset</span>
                </a>

                <form action="{{ route('dashboard.update_kinerja') }}" method="POST" id="updateKinerjaForm">
                    @csrf
                    <button type="submit" id="btnUpdateKinerja"
                            class="h-9 px-4 rounded-xl bg-[#0066FF] hover:bg-blue-600 text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                        <svg id="iconRefresh" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Update Kinerja</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Grid Layout Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 lg:gap-3.5 lg:items-stretch">

            <!-- Kolom Kiri -->
            <div class="lg:col-span-8 flex flex-col gap-3.5 sm:gap-4 lg:gap-3.5">

                <!-- 4 Kartu Statistik (Sesuai Desain Asli Branch Main) -->
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
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium" id="cardTotalKontrak">{{ $totalKontrak }} kontrak</p>
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
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium" id="cardKontrakAktif">{{ $kontrakAktif }} kontrak</p>
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
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium" id="cardBacklog">{{ $backlog }} kontrak</p>
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
                            <p class="text-[11px] sm:text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5 font-medium" id="cardTotalTagihan">{{ $totalTagihanFormatted }}</p>
                        </div>
                    </a>

                </div>

                <!-- Ringkasan Kinerja & Crowl Akuntansi Strip -->
                <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-4 border border-gray-100/90 dark:border-white/5 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#0066FF] shrink-0"></span>
                        <span class="text-gray-500 dark:text-gray-400">Kinerja KNA:</span>
                        <span class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm" id="stripKinerjaKna">{{ $totalNilaiKontrakFormatted }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 hidden sm:inline">(Diakui 100%)</span>
                    </div>
                    <div class="h-4 w-px bg-gray-200 dark:bg-white/10 hidden sm:block"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-gray-500 dark:text-gray-400">Crowl Akuntansi:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-xs sm:text-sm" id="stripCrowlTerakrual">{{ $totalCrowlTerakrualFormatted }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 hidden sm:inline">(Terakrual)</span>
                    </div>
                    <div class="h-4 w-px bg-gray-200 dark:bg-white/10 hidden sm:block"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                        <span class="text-gray-500 dark:text-gray-400">Akrual Harian:</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400 text-xs sm:text-sm" id="stripCrowlHarian">{{ $totalCrowlHarianFormatted }}</span>
                    </div>
                </div>

                <!-- Chart Tagihan per Bulan (SVG Wave Asli dari Main) -->
                <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-5 lg:p-4.5 border border-gray-100/90 dark:border-white/5 flex flex-col gap-2.5 sm:gap-3 flex-1 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xs sm:text-base font-semibold text-gray-900 dark:text-white">
                            Tagihan per Bulan ({{ now()->year }})
                        </h2>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500">Distribusi Pendapatan</span>
                    </div>

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

                    <!-- Tabel Jatuh Tempo Terdekat (90 Hari ke Depan) -->
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

            <!-- Kolom Kanan -->
            <div class="lg:col-span-4 flex flex-col gap-3.5 sm:gap-4 lg:gap-3.5">

                <!-- Distribusi Kelompok (Sesuai Desain Asli Main) -->
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

                <!-- Status Kontrak & Profil Aset Potensial Daop 4 (Gradien Biru Asli Main) -->
                <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#659DF8] via-[#2F7EF8] to-[#0062F5] p-4 sm:p-5 lg:p-4.5 text-white shadow-[0_4px_20px_rgba(21,112,239,0.2)] flex flex-col gap-3 flex-1">
                    <h2 class="text-xs sm:text-base font-semibold text-white">Status Kontrak</h2>

                    <div class="grid grid-cols-1 gap-2">
                        <!-- Aktif -->
                        <div class="flex items-center justify-between bg-white/15 rounded-xl px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-300 shrink-0"></span>
                                <span class="text-xs sm:text-sm font-semibold text-white">Kontrak Aktif</span>
                            </div>
                            <span class="text-sm sm:text-base font-bold text-white">{{ $kontrakAktif }}</span>
                        </div>

                        <!-- Backlog -->
                        <div class="flex items-center justify-between bg-white/15 rounded-xl px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-red-300 shrink-0"></span>
                                <span class="text-xs sm:text-sm font-semibold text-white">Backlog</span>
                            </div>
                            <span class="text-sm sm:text-base font-bold text-white">{{ $backlog }}</span>
                        </div>

                        <!-- Total -->
                        <div class="flex items-center justify-between bg-white/10 rounded-xl px-3 py-2 border border-white/20">
                            <span class="text-xs sm:text-sm font-semibold text-white/80">Total Kontrak</span>
                            <span class="text-sm sm:text-base font-bold text-white">{{ $totalKontrak }}</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="h-px bg-white/20 my-1"></div>

                    <!-- Profil Objek Aset Potensial Daop 4 -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs sm:text-sm font-bold text-white">Profil Aset Daop 4</h3>
                            <span class="text-[10px] text-white/80 bg-white/20 px-2 py-0.5 rounded-full font-medium">Sampling Stasiun</span>
                        </div>

                        <div class="grid grid-cols-3 gap-1.5 text-center mb-2.5">
                            <div class="bg-white/15 rounded-xl p-2">
                                <p class="text-[10px] text-white/80">Total</p>
                                <p class="text-sm font-bold text-white mt-0.5" id="rightTotalAset">{{ $assetStats['total'] ?? 58 }}</p>
                            </div>
                            <div class="bg-white/15 rounded-xl p-2">
                                <div class="flex items-center justify-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                    <p class="text-[10px] text-white/80">Potensial</p>
                                </div>
                                <p class="text-sm font-bold text-white mt-0.5" id="rightPotensialAset">{{ $assetStats['potensial'] ?? 27 }}</p>
                            </div>
                            <div class="bg-white/15 rounded-xl p-2">
                                <div class="flex items-center justify-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-300"></span>
                                    <p class="text-[10px] text-white/80">Terkontrak</p>
                                </div>
                                <p class="text-sm font-bold text-white mt-0.5" id="rightTerkontrakAset">{{ $assetStats['terkontrak'] ?? 31 }}</p>
                            </div>
                        </div>

                        <!-- Daftar Stasiun Sampling Ringkas -->
                        <div class="bg-black/15 rounded-xl p-2.5 max-h-[140px] overflow-y-auto space-y-1 text-[11px] mb-2.5">
                            @foreach(($assetStats['samplingStations'] ?? []) as $stasiun)
                                <div class="flex items-center justify-between py-0.5 border-b border-white/5 last:border-b-0">
                                    <span class="font-medium text-white/90 truncate max-w-[130px]">{{ $stasiun['name'] }}</span>
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <span class="text-emerald-300 font-semibold">{{ $stasiun['potensial'] }} pot</span>
                                        <span class="text-white/40">/</span>
                                        <span class="text-red-300 font-semibold">{{ $stasiun['terkontrak'] }} ktr</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Links di Bagian Bawah Card -->
                    <div class="mt-auto space-y-1.5">
                        <a href="{{ route('map.index') }}"
                           class="flex items-center justify-center gap-1.5 bg-white text-[#0062F5] hover:bg-white/90 rounded-xl py-2 text-xs font-bold transition shadow-sm active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#0062F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Buka Peta Potensi Lahan</span>
                        </a>

                        <a href="{{ route('contracts.index') }}"
                           class="flex items-center justify-center gap-1.5 bg-white/20 hover:bg-white/30 rounded-xl py-2 text-xs font-semibold text-white transition active:scale-95">
                            <span>Lihat Semua Kontrak</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Section Grafik Real-Time (Data Bergerak) Full Width -->
        <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-5 lg:p-5 border border-gray-100/90 dark:border-white/5 shadow-[0_2px_10px_rgba(0,0,0,0.02)] mt-3.5 sm:mt-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <h2 class="text-xs sm:text-base font-bold text-gray-900 dark:text-white">
                        Grafik Real-Time Tren Kinerja & Realisasi (Data Bergerak)
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-gray-400 dark:text-gray-500" id="liveChartLastSync">Live Sync Aktif (5s)</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
                        Live Tracking
                    </span>
                </div>
            </div>

            <div class="w-full h-[220px] sm:h-[260px] relative">
                <canvas id="livePerformanceChart"></canvas>
            </div>
        </div>

    </main>

    <x-toast />

    <!-- Script Chart.js Real-Time & AJAX Update Kinerja -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ── Inisialisasi Chart.js untuk Grafik Real-Time ──
            const ctx = document.getElementById('livePerformanceChart');
            if (ctx) {
                const initialCrowl = @json($performance['monthlyCrowl'] ?? []);
                const initialRealisasi = @json($performance['monthlyRealisasi'] ?? []);

                const labels = initialCrowl.map(item => item.key);
                const crowlData = initialCrowl.map(item => item.val);
                const realisasiData = initialRealisasi.map(item => item.val);

                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9AA0A6' : '#64748B';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';

                const liveChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Crowl Akuntansi (Rp)',
                                data: crowlData,
                                borderColor: '#1570EF',
                                backgroundColor: 'rgba(21, 112, 239, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Realisasi Tagihan (Rp)',
                                data: realisasiData,
                                borderColor: '#10B981',
                                backgroundColor: 'transparent',
                                borderDash: [4, 4],
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: 2.5,
                                pointHoverRadius: 5,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: textColor,
                                    font: { size: 11, family: 'Plus Jakarta Sans' },
                                    boxWidth: 12,
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let val = context.parsed.y || 0;
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor, font: { size: 10 } }
                            },
                            y: {
                                grid: { color: gridColor },
                                ticks: {
                                    color: textColor,
                                    font: { size: 10 },
                                    callback: function (val) {
                                        if (val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
                                        if (val >= 1000000) return (val / 1000000).toFixed(0) + 'Jt';
                                        return val;
                                    }
                                }
                            }
                        }
                    }
                });

                // Polling Real-Time setiap 5 detik (Data Bergerak)
                setInterval(function () {
                    fetch('{{ route("dashboard.live_chart") }}', {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.monthlyCrowl) {
                            liveChart.data.datasets[0].data = data.monthlyCrowl.map(i => i.val);
                            liveChart.data.datasets[1].data = data.monthlyRealisasi.map(i => i.val);
                            liveChart.update('none'); // Update halus tanpa redraw animasi penuh

                            const syncLabel = document.getElementById('liveChartLastSync');
                            if (syncLabel) {
                                const now = new Date();
                                syncLabel.innerText = 'Sync: ' + now.toLocaleTimeString('id-ID');
                            }
                        }
                    })
                    .catch(() => {});
                }, 5000);
            }

            // ── AJAX Form Submission untuk Tombol "Update Kinerja" ──
            const form = document.getElementById('updateKinerjaForm');
            const btn = document.getElementById('btnUpdateKinerja');
            const icon = document.getElementById('iconRefresh');

            if (form && btn) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    if (icon) icon.classList.add('animate-spin');

                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        if (icon) icon.classList.remove('animate-spin');

                        if (data.success && data.metrics) {
                            // Update nilai pada strip & kartu
                            const formatRupiah = (val) => {
                                if (val >= 1000000000) return 'Rp ' + (val / 1000000000).toFixed(1).replace('.', ',') + ' M';
                                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1).replace('.', ',') + ' Jt';
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            };

                            const stripKna = document.getElementById('stripKinerjaKna');
                            if (stripKna) stripKna.innerText = formatRupiah(data.metrics.totalNilaiKontrak);

                            const stripCrowl = document.getElementById('stripCrowlTerakrual');
                            if (stripCrowl) stripCrowl.innerText = formatRupiah(data.metrics.totalCrowlTerakrual);

                            const stripHarian = document.getElementById('stripCrowlHarian');
                            if (stripHarian) stripHarian.innerText = formatRupiah(data.metrics.totalCrowlHarianAktif) + '/hari';

                            const rPotensial = document.getElementById('rightPotensialAset');
                            if (rPotensial) rPotensial.innerText = data.metrics.asetPotensial;

                            const rTerkontrak = document.getElementById('rightTerkontrakAset');
                            if (rTerkontrak) rTerkontrak.innerText = data.metrics.asetTerkontrak;

                            alert(data.message || 'Perhitungan kinerja kontrak & crowl akuntansi berhasil diperbarui!');
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        if (icon) icon.classList.remove('animate-spin');
                        // Fallback reload
                        form.submit();
                    });
                });
            }
        });
    </script>
</body>
</html>
