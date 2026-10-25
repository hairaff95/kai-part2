<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Tambah Aset & Kontrak Baru — KAI Tracker</title>
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

        <form action="{{ route('contracts.store') }}" method="POST" id="formCreateContract" class="flex flex-col gap-6">
            @csrf

            <!-- Header with Title & Top-Right Actions (matching Screenshot 1) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                <div>
                    <h1 class="text-xl sm:text-[28px] font-bold tracking-tight text-gray-950 dark:text-white">
                        Tambah Aset & Kontrak Baru
                    </h1>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5">
                        <a href="{{ route('contracts.index') }}" class="hover:text-[#0066FF] transition">Daftar Kontrak</a>
                        <span>/</span>
                        <span class="text-gray-600 dark:text-gray-300 font-medium">Tambah Aset</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 self-start sm:self-auto">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Aset</span>
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
                        Terdapat kesalahan pengisian data:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. Informasi Penyewa (Tenant) -->
            <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-5">
                <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-white/10 pb-3">
                    1. Informasi Penyewa (Tenant)
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Penyewa / Customer (Fullname) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_mitra" value="{{ old('nama_mitra') }}" required placeholder="Contoh: PT MEGA JAYA PERKASA"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Status Customer <span class="text-red-500">*</span>
                        </label>
                        <select name="status_mitra" class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                            <option value="" disabled {{ old('status_mitra') ? '' : 'selected' }}>Pilih atau ketik status...</option>
                            @foreach($statusMitraOptions as $opt)
                                <option value="{{ $opt }}" {{ old('status_mitra', 'Swasta') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jenis Perusahaan
                        </label>
                        <input type="text" name="jenis_perusahaan" value="{{ old('jenis_perusahaan') }}" placeholder="-"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Brand / Usaha
                    </label>
                    <input type="text" name="peruntukan" value="{{ old('peruntukan') }}" placeholder="Contoh: Indomaret / Rumah Makan / Hunian..."
                        class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                </div>
            </div>

            <!-- 2. Informasi Aset KAI -->
            <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-5">
                <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-white/10 pb-3">
                    2. Informasi Aset KAI
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nomor / Kode Aset <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_aset" value="{{ old('nomor_aset') }}" placeholder="Contoh: AST-SMG-001"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jenis Aset <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_aset" class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                            <option value="" disabled {{ old('jenis_aset') ? '' : 'selected' }}>Pilih atau ketik jenis aset...</option>
                            @foreach($jenisAsetOptions as $j)
                                <option value="{{ $j }}" {{ old('jenis_aset', 'Tanah') === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Luas Aset (m²)
                        </label>
                        <input type="number" step="0.01" name="luas_tanah" value="{{ old('luas_tanah', 0) }}" placeholder="0.00"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Peruntukan
                        </label>
                        <input type="text" name="peruntukan_detail" value="{{ old('peruntukan_detail', 'RUMAH TINGGAL') }}" placeholder="RUMAH TINGGAL"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Stasiun Terdekat
                        </label>
                        <input type="text" name="kmhm" value="{{ old('kmhm') }}" placeholder="Pilih atau ketik stasiun..."
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Wilayah Aset
                        </label>
                        <input type="text" name="alamat_mitra" value="{{ old('alamat_mitra') }}" placeholder="Pilih atau ketik wilayah..."
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Blok Aset / Lokasi Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="blok_aset" value="{{ old('blok_aset') }}" required placeholder="Contoh: BLOK PERTOKOAN STASIUN PONCOL SEMARANG"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>
                </div>

                <!-- Tambah Gambar Section (matching Screenshot 1) -->
                <div class="flex flex-col gap-3 pt-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Tambah Gambar <span class="text-red-500">*</span>
                    </label>

                    <!-- Upload Zone -->
                    <div class="border-2 border-dashed border-gray-200 dark:border-white/15 rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center text-center bg-gray-50/50 dark:bg-[#282A2C]/50 hover:bg-gray-50 transition cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/10 flex items-center justify-center text-gray-400 dark:text-gray-300 mb-2.5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">Klik ikon untuk tambah gambar di bawah 10 MB</p>
                        <p class="text-[11px] text-gray-400 dark:text-[#9AA0A6] mt-0.5">pilih dalam format (HEIC, JPG, PNG, WEBP)</p>
                    </div>

                    <!-- Uploaded Image Items Preview -->
                    <div class="flex flex-col gap-2.5">
                        <!-- Utama -->
                        <div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1 block">Utama</span>
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#2D3034]">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-white/10 flex items-center justify-center text-gray-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white">gambar-utama.jpg</p>
                                        <p class="text-[10px] text-gray-400">10 MB</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <button type="button" class="hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    <button type="button" class="hover:text-gray-600 dark:hover:text-white transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg></button>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary Images Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            @foreach(['gambar-1.jpg', 'gambar-2.jpg', 'gambar-3.jpg'] as $imgName)
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#2D3034]">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/10 flex items-center justify-center text-gray-500">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $imgName }}</p>
                                            <p class="text-[10px] text-gray-400">10 MB</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-400">
                                        <button type="button" class="hover:text-red-500 transition"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        <button type="button" class="hover:text-gray-600 dark:hover:text-white transition"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Titik Koordinat G Maps (matching Screenshot 1) -->
                <div class="flex flex-col gap-2 pt-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Titik Koordinat G Maps <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                        <!-- Mini Map Preview Box -->
                        <div class="lg:col-span-3 h-24 rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden relative bg-blue-50/50 dark:bg-white/5 flex items-center justify-center p-2">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 text-red-600 mb-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                </div>
                                <p class="text-[10px] font-bold text-gray-800 dark:text-white leading-tight">Pekalongan</p>
                                <p class="text-[9px] text-gray-400">RS Siti Khodijah Area</p>
                            </div>
                        </div>

                        <!-- Latitude & Longitude Inputs -->
                        <div class="lg:col-span-9 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400 w-16">Latitude</span>
                                <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', '-6.8885') }}" placeholder="Contoh: -6.8885"
                                    class="flex-1 h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400 w-16">Longitude</span>
                                <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', '109.6753') }}" placeholder="Contoh: 109.6753"
                                    class="flex-1 h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 3. Informasi Kontrak & Periode Sewa -->
            <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-7 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-5">
                <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-white/10 pb-3">
                    3. Informasi Kontrak & Periode Sewa
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nomor Kontrak <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_kontrak" value="{{ old('nomor_kontrak') }}" required placeholder="Contoh: 001/HK.201/XI/2026"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jenis Kontrak <span class="text-red-500">*</span>
                        </label>
                        <select name="kelompok" class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                            <option value="" disabled {{ old('kelompok') ? '' : 'selected' }}>Pilih atau ketik jenis kontrak...</option>
                            @foreach($kelompokOptions as $k)
                                <option value="{{ $k }}" {{ old('kelompok', 'Non Row') === $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Area Kontrak
                        </label>
                        <input type="text" name="area_kontrak" value="{{ old('area_kontrak') }}" placeholder="Pilih atau ketik area..."
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nilai Kontrak / Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="nilai_kontrak" value="{{ old('nilai_kontrak', 0) }}" required placeholder="0"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400 font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Tanggal Kontrak
                        </label>
                        <input type="text" name="tanggal_kontrak" value="{{ old('tanggal_kontrak') }}" placeholder="DD/MM/YYYY"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Masa Awal Kontrak
                        </label>
                        <input type="date" name="masa_awal" value="{{ old('masa_awal') }}"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Masa Akhir Kontrak
                        </label>
                        <input type="date" name="masa_akhir" value="{{ old('masa_akhir') }}"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            SPV / Penanggung Jawab
                        </label>
                        <input type="text" name="spv" value="{{ old('spv') }}" placeholder="Contoh: Manager Aset Non Produksi"
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Keterangan
                        </label>
                        <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Keterangan tambahan..."
                            class="w-full h-11 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400">
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('contracts.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-200 dark:bg-white/10 hover:bg-gray-300 dark:hover:bg-white/15 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 transition">
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Aset</span>
                </button>
            </div>

        </form>

    </main>

    <x-toast />

</body>
</html>
