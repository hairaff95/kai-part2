<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Edit Daftar Kontrak — KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="contracts" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-6 pb-28 lg:pb-8 flex flex-col gap-4 sm:gap-6">

        <form action="{{ route('contracts.update', $contract->nomor_kontrak) }}" method="POST" id="formEditContract" class="flex flex-col gap-6">
            @csrf
            @method('PUT')

            <!-- Top Header & Actions matching Screenshot 2 -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                <div>
                    <h1 class="text-xl sm:text-[28px] font-bold tracking-tight text-gray-950 dark:text-white">
                        Edit Daftar Kontrak
                    </h1>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5">
                        <a href="{{ route('contracts.index') }}" class="hover:text-[#0066FF] transition">Daftar Kontrak</a>
                        <span>/</span>
                        <span class="text-[#0066FF] font-medium">Edit</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 self-start sm:self-auto">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan</span>
                    </button>
                    <a href="{{ route('contracts.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#EF4444] hover:bg-red-600 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/50 p-4 text-xs sm:text-sm text-red-600 dark:text-red-400">
                    <div class="font-bold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Terdapat kesalahan pengisian:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 2-Column Grid Layout matching Screenshot 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">

                <!-- Left Column (Form Inputs) -->
                <div class="lg:col-span-8 flex flex-col gap-5 sm:gap-6">

                    <!-- Card 1: Informasi Penyewa -->
                    <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-4">
                        <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                            Informasi Penyewa
                        </h2>

                        <div class="flex flex-col gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nama Penyewa <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_mitra" value="{{ old('nama_mitra', $contract->tenant?->nama_mitra) }}" required
                                    class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                            </div>

                            <div class="w-full sm:w-1/2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Status Customer <span class="text-red-500">*</span>
                                </label>
                                <select name="status_mitra" class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                                    @foreach($statusMitraOptions as $opt)
                                        <option value="{{ $opt }}" {{ old('status_mitra', $contract->tenant?->status_mitra) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Informasi Kontrak -->
                    <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-4">
                        <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                            Informasi Kontrak
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- No Kontrak -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    No Kontrak <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nomor_kontrak" value="{{ old('nomor_kontrak', $contract->nomor_kontrak) }}" required
                                    class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400 font-semibold">
                            </div>

                            <!-- Jarak Waktu Kontrak -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Jarak Waktu Kontrak <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <span class="block text-[11px] text-gray-400 mb-1">Awal Kontrak</span>
                                        <div class="relative">
                                            <input type="date" name="masa_awal" value="{{ old('masa_awal', $contract->masa_awal?->format('Y-m-d')) }}"
                                                class="w-full h-11 px-3 text-xs bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                    <div>
                                        <span class="block text-[11px] text-gray-400 mb-1">Selesai Kontrak</span>
                                        <div class="relative">
                                            <input type="date" name="masa_akhir" value="{{ old('masa_akhir', $contract->masa_akhir?->format('Y-m-d')) }}"
                                                class="w-full h-11 px-3 text-xs bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Waktu Kontrak -->
                        <div class="w-full sm:w-1/2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                Waktu Kontrak <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tanggal_kontrak" value="{{ old('tanggal_kontrak', $contract->tanggal_kontrak) }}" placeholder="DD/MM/YY"
                                class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                        </div>

                        <!-- Nama Blok Aset -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                Nama Blok Aset <span class="text-red-500">*</span>
                            </label>
                            <textarea name="blok_aset" rows="3" required placeholder="Nama blok / lokasi aset..."
                                class="w-full p-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400 leading-relaxed">{{ old('blok_aset', $contract->asset?->blok_aset) }}</textarea>
                        </div>

                        <!-- Brand & Nilai Kontrak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Brand <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="peruntukan" value="{{ old('peruntukan', $contract->asset?->peruntukan) }}" placeholder="Brand / Peruntukan..."
                                    class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nilai Kontrak <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="nilai_kontrak" value="{{ old('nilai_kontrak', $contract->nilai_kontrak) }}" required
                                    class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white font-bold">
                            </div>
                        </div>

                        <!-- Additional asset fields stored transparently -->
                        <input type="hidden" name="kelompok" value="{{ $contract->kelompok ?? 'Non Row' }}">
                        <input type="hidden" name="nomor_aset" value="{{ $contract->nomor_aset }}">
                        <input type="hidden" name="jenis_aset" value="{{ $contract->asset?->jenis_aset ?? 'Tanah' }}">
                        <input type="hidden" name="luas_tanah" value="{{ $contract->asset?->luas_tanah ?? 0 }}">
                        <input type="hidden" name="luas_bangunan" value="{{ $contract->asset?->luas_bangunan ?? 0 }}">
                        <input type="hidden" name="kmhm" value="{{ $contract->asset?->kmhm }}">
                        <input type="hidden" name="latitude" value="{{ $contract->asset?->latitude }}">
                        <input type="hidden" name="longitude" value="{{ $contract->asset?->longitude }}">
                    </div>

                </div>

                <!-- Right Column (Kustom Table matching Screenshot 2) -->
                <div class="lg:col-span-4 flex flex-col gap-5 sm:gap-6">

                    <!-- Kustom Table Card -->
                    <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                Kustom Table
                            </h3>
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-2">Ubah Urutan Kolom</p>
                            <p class="text-[11px] text-gray-400 dark:text-[#9AA0A6] mt-0.5 leading-snug">
                                Ubah urutan kolom dengan geser pada ikon, dan sesuaikan untuk tampilan anatominya
                            </p>
                        </div>

                        <!-- Draggable Pills Box matching Screenshot 2 -->
                        <div class="bg-gray-50/90 dark:bg-[#282A2C] rounded-2xl p-4 border border-gray-200/60 dark:border-white/5 flex flex-wrap gap-2.5">
                            @php
                                $columns = [
                                    'Nama Penyewa',
                                    'Waktu Kontrak',
                                    'Nilai Kontrak',
                                    'Status Customer',
                                    'Nama Blok Aset',
                                    'Brand',
                                    'No Kontrak',
                                    'Jarak Waktu Kontrak'
                                ];
                            @endphp
                            @foreach($columns as $col)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-[#34373B] border border-gray-200 dark:border-white/10 text-xs font-medium text-gray-800 dark:text-gray-200 shadow-2xs select-none">
                                    <!-- Drag Dots Icon -->
                                    <svg class="w-3.5 h-3.5 text-gray-400 cursor-move" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 2a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zM13 2a2 2 0 100 4 2 2 0 000-4zM13 8a2 2 0 100 4 2 2 0 000-4zM13 14a2 2 0 100 4 2 2 0 000-4z" />
                                    </svg>
                                    <span>{{ $col }}</span>
                                    <button type="button" class="text-gray-400 hover:text-red-500 transition ml-0.5" title="Sembunyikan Kolom">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400 dark:text-[#9AA0A6] uppercase tracking-wider font-semibold">Status Kontrak</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $contract->status_kontrak_badge }}">
                                {{ $contract->status_kontrak }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 pt-2 border-t border-gray-100 dark:border-white/5">
                            <span>Sisa Masa Kontrak:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $contract->sisa_hari }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                            <span>Total Pembayaran / Tagihan:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $contract->payments->count() }} Termin</span>
                        </div>
                    </div>

                </div>

            </div>

        </form>

    </main>

    <x-toast />

</body>
</html>
