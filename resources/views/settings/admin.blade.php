<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Pengaturan — Profil Admin</title>

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

        {{-- Main 2-Column Layout (Sidebar Kiri & Konten Kanan) --}}
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
                        onclick="switchAdminTab('profil')"
                        id="tab-btn-profil"
                        class="shrink-0 text-left text-sm font-semibold transition cursor-pointer text-[#0066FF] dark:text-[#3B82F6] px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-blue-50 dark:bg-blue-900/30 lg:bg-transparent lg:dark:bg-transparent"
                    >
                        Profil Saya
                    </button>

                    @auth
                        {{-- 2. Tab Pengajuan Reset Sandi --}}
                        <button
                            type="button"
                            onclick="switchAdminTab('reset-sandi')"
                            id="tab-btn-reset-sandi"
                            class="shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent"
                        >
                            Pengajuan Reset Sandi
                        </button>

                        {{-- 3. Tab Import Data Excel --}}
                        <button
                            type="button"
                            onclick="switchAdminTab('import-excel')"
                            id="tab-btn-import-excel"
                            class="shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent"
                        >
                            Import Data Excel
                        </button>
                    @endauth
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
                                    @auth {{ auth()->user()->name }} @else Tamu @endauth
                                </h2>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] font-medium mt-1">
                                    @auth
                                        {{ auth()->user()->isSuperAdmin() ? 'Super Admin KAI' : 'Admin KAI Aset' }}
                                    @else
                                        Pengunjung / Tamu
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

                        @php
                            $fullName = auth()->check() ? auth()->user()->name : 'Tamu';
                            $parts = explode(' ', $fullName, 2);
                            $firstName = $parts[0] ?? 'Tamu';
                            $lastName = $parts[1] ?? '';
                            $email = auth()->check() ? auth()->user()->email : 'tamu@kai.id';
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 pt-2">
                            {{-- Nama Awal --}}
                            <div>
                                <span class="block text-xs font-medium text-gray-400 dark:text-[#9AA0A6] mb-1.5">Nama Awal</span>
                                <span class="block text-sm sm:text-[15px] font-semibold text-gray-900 dark:text-white" id="display-first-name">
                                    {{ $firstName }}
                                </span>
                            </div>

                            {{-- Nama Akhir --}}
                            <div>
                                <span class="block text-xs font-medium text-gray-400 dark:text-[#9AA0A6] mb-1.5">Nama Akhir</span>
                                <span class="block text-sm sm:text-[15px] font-semibold text-gray-900 dark:text-white" id="display-last-name">
                                    {{ $lastName ?: '-' }}
                                </span>
                            </div>

                            {{-- Alamat Email --}}
                            <div class="sm:col-span-2">
                                <span class="block text-xs font-medium text-gray-400 dark:text-[#9AA0A6] mb-1.5">Alamat Email</span>
                                <span class="block text-sm sm:text-[15px] font-semibold text-gray-900 dark:text-white" id="display-email">
                                    {{ $email }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>


                @auth
                {{-- ------------------- TAB 2: PENGAJUAN RESET SANDI ------------------- --}}
                <div id="panel-reset-sandi" class="hidden">
                    <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-10 shadow-xs space-y-6 max-w-3xl transition-colors">
                        
                        {{-- Title --}}
                        <h2 class="text-2xl sm:text-[30px] font-bold text-gray-950 dark:text-white tracking-tight leading-tight">
                            Pengajuan Ubah Kata Sandi
                        </h2>

                        {{-- Description per Desain Figma --}}
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 font-normal leading-relaxed">
                            Ajukan Reset Kata Sandi login aplikasi Tracker APP, ingat pengajuan kata sandi menunggu persetujuan dari superadmin selama 1x24 jam, jika belum di setujui silahkan cek email untuk mendapatkan kata sandi sementara untuk login dan untuk pengubahan kata sandi
                        </p>

                        {{-- Buttons: Batal & Ajukan Perubahan Kata Sandi --}}
                        <div class="flex items-center gap-3 pt-4">
                            {{-- Tombol Batal --}}
                            <button
                                type="button"
                                onclick="switchAdminTab('profil')"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-[8px] bg-[#E00000] hover:bg-red-700 text-xs sm:text-sm font-medium text-white transition shadow-xs cursor-pointer"
                            >
                                <x-icon name="close" class="w-3.5 h-3.5" />
                                <span>Batal</span>
                            </button>

                            {{-- Tombol Ajukan Perubahan Kata Sandi --}}
                            <button
                                type="button"
                                onclick="submitResetRequest()"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-[8px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-medium text-white transition shadow-xs cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Ajukan Perubahan Kata Sandi</span>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- ------------------- TAB 3: IMPORT DATA EXCEL ------------------- --}}
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
                    {{-- Upload File Data Card (Sesuai Gambar Mockup) --}}
                    <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white dark:bg-[#1F2123] p-6 sm:p-10 shadow-xs space-y-8 transition-colors">
                        
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-950 dark:text-white tracking-tight">
                            Upload File Data
                        </h2>

                        <form id="admin-excel-import-form" method="POST" action="{{ route('settings.import-excel') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            {{-- Drag & Drop Upload Zone with icon-upload-data.svg --}}
                            <div
                                id="admin-dropzone-area"
                                onclick="document.getElementById('admin-excel-file-input').click()"
                                class="relative border-2 border-dashed border-gray-200 dark:border-white/15 hover:border-[#0066FF] dark:hover:border-[#3B82F6] bg-transparent hover:bg-blue-50/20 dark:hover:bg-blue-900/10 rounded-2xl p-8 sm:p-12 text-center transition cursor-pointer group flex flex-col items-center justify-center"
                            >
                                <input
                                    type="file"
                                    name="excel_file"
                                    id="admin-excel-file-input"
                                    accept=".csv, .xlsx, .xls, .txt"
                                    class="hidden"
                                    onchange="handleAdminFileSelected(this)"
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
                            <div id="admin-selected-file-container" class="hidden rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#282A2C] p-4 sm:p-5 transition-all">
                                <div class="flex items-center gap-4">
                                    {{-- Icon Box (Circular Import or Green Excel Icon) --}}
                                    <div id="admin-icon-wrapper" class="shrink-0 flex items-center justify-center">
                                        <x-icon id="admin-preview-excel-icon" name="excel-icon" class="w-9 h-9 object-contain" />
                                    </div>

                                    {{-- Info & Progress Bar --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-0.5">
                                            <span id="admin-selected-file-name" class="font-semibold text-sm text-gray-900 dark:text-white truncate">pk.xlsx</span>
                                            <span id="admin-upload-percentage" class="text-xs font-semibold text-gray-500 dark:text-gray-400">100%</span>
                                        </div>

                                        <span id="admin-selected-file-size" class="text-xs text-gray-400 dark:text-gray-500 block mb-2">10 MB</span>

                                        {{-- Progress Bar --}}
                                        <div id="admin-progress-wrapper" class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                            <div id="admin-import-progress-bar" class="bg-[#0066FF] h-full rounded-full transition-all duration-300" style="width: 100%;"></div>
                                        </div>
                                    </div>

                                    {{-- Cancel File Selection Button --}}
                                    <button
                                        type="button"
                                        onclick="clearAdminSelectedFile(event)"
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
                                    onclick="clearAdminSelectedFile(event)"
                                    class="px-7 py-2.5 rounded-xl bg-[#E00000] hover:bg-red-700 text-sm font-semibold text-white transition shadow-sm hover:shadow active:scale-98 cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    id="btn-admin-submit-import"
                                    class="px-7 py-2.5 rounded-xl bg-[#0066FF] hover:bg-blue-700 text-sm font-semibold text-white transition shadow-sm hover:shadow active:scale-98 cursor-pointer"
                                >
                                    Import
                                </button>
                            </div>
                        </form>

                    </div>

                </div>
                @endauth

            </div>

        </div>

    </main>

    {{-- ================= MODAL EDIT INFORMASI PROFIL ================= --}}
    <div id="modal-edit-profile" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-3 sm:p-4">
        <div class="w-full max-w-[340px] sm:max-w-md rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-8 shadow-2xl border border-gray-100 dark:border-white/10 space-y-3.5 sm:space-y-5 animate-in fade-in zoom-in-95 duration-200 transition-colors max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-2.5 sm:pb-3">
                <h3 class="text-sm sm:text-lg font-bold text-gray-900 dark:text-white">Edit Informasi Profil</h3>
                <button type="button" onclick="closeEditProfileModal()" class="p-1 rounded-lg text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer">
                    <x-icon name="close" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
            </div>

            <form id="form-edit-profile" onsubmit="saveProfileChanges(event)" class="space-y-2.5 sm:space-y-4">
                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Nama Awal</label>
                    <input
                        type="text"
                        id="input-first-name"
                        value="{{ $firstName }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Nama Akhir</label>
                    <input
                        type="text"
                        id="input-last-name"
                        value="{{ $lastName }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-[#2D3034] px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#0066FF] focus:outline-none transition"
                    >
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-white mb-1 sm:mb-1.5">Alamat Email</label>
                    <input
                        type="email"
                        id="input-email"
                        value="{{ $email }}"
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

    {{-- Scripts untuk Tab Switching & Modal --}}
    <script>
        function switchAdminTab(tabName) {
            const btnProfil = document.getElementById('tab-btn-profil');
            const btnReset = document.getElementById('tab-btn-reset-sandi');
            const btnImport = document.getElementById('tab-btn-import-excel');
            const panelProfil = document.getElementById('panel-profil');
            const panelReset = document.getElementById('panel-reset-sandi');
            const panelImport = document.getElementById('panel-import-excel');

            const activeClass = "shrink-0 text-left text-sm font-semibold transition cursor-pointer text-[#0066FF] dark:text-[#3B82F6] px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-blue-50 dark:bg-blue-900/30 lg:bg-transparent lg:dark:bg-transparent";
            const inactiveClass = "shrink-0 text-left text-sm font-medium transition cursor-pointer text-gray-400 dark:text-[#9AA0A6] hover:text-gray-700 dark:hover:text-white px-4 py-2 lg:px-0 lg:py-0 rounded-full lg:rounded-none bg-gray-100/80 dark:bg-[#2D3034] lg:bg-transparent lg:dark:bg-transparent";

            // Sembunyikan semua panel
            if (panelProfil) panelProfil.classList.add('hidden');
            if (panelReset) panelReset.classList.add('hidden');
            if (panelImport) panelImport.classList.add('hidden');

            // Reset tab styles
            if (btnProfil) btnProfil.className = inactiveClass;
            if (btnReset) btnReset.className = inactiveClass;
            if (btnImport) btnImport.className = inactiveClass;

            if (tabName === 'profil') {
                if (btnProfil) btnProfil.className = activeClass;
                if (panelProfil) panelProfil.classList.remove('hidden');
            } else if (tabName === 'reset-sandi') {
                if (btnReset) btnReset.className = activeClass;
                if (panelReset) panelReset.classList.remove('hidden');
            } else if (tabName === 'import-excel') {
                if (btnImport) btnImport.className = activeClass;
                if (panelImport) panelImport.classList.remove('hidden');
            }
        }

        function handleAdminFileSelected(input) {
            const container = document.getElementById('admin-selected-file-container');
            const nameEl = document.getElementById('admin-selected-file-name');
            const sizeEl = document.getElementById('admin-selected-file-size');
            const percentEl = document.getElementById('admin-upload-percentage');
            const barEl = document.getElementById('admin-import-progress-bar');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const ext = file.name.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                    if (window.showToast) window.showToast('Gagal: Format file harus .xlsx, .xls, atau .csv!', 'error');
                    clearAdminSelectedFile();
                    return;
                }

                nameEl.textContent = file.name;
                const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
                sizeEl.textContent = (sizeMb >= 1) ? `${sizeMb} MB` : `${(file.size / 1024).toFixed(1)} KB`;
                
                // Show container
                container.classList.remove('hidden');
                
                // Progress simulation to 100%
                percentEl.textContent = '100%';
                barEl.style.width = '100%';
                if (window.showToast) window.showToast('File siap diimpor: ' + file.name, 'info');
            } else {
                container.classList.add('hidden');
            }
        }

        function clearAdminSelectedFile(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const input = document.getElementById('admin-excel-file-input');
            const container = document.getElementById('admin-selected-file-container');
            if (input) input.value = '';
            if (container) container.classList.add('hidden');
        }

        // Support Drag & Drop visuals
        const adminDropzone = document.getElementById('admin-dropzone-area');
        if (adminDropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                adminDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    adminDropzone.classList.add('border-[#0066FF]', 'bg-blue-50/20');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                adminDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    adminDropzone.classList.remove('border-[#0066FF]', 'bg-blue-50/20');
                });
            });

            adminDropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    const input = document.getElementById('admin-excel-file-input');
                    input.files = files;
                    handleAdminFileSelected(input);
                }
            });
        }

        // Form Submit Handler for Excel Import
        const adminImportForm = document.getElementById('admin-excel-import-form');
        if (adminImportForm) {
            adminImportForm.addEventListener('submit', (e) => {
                const input = document.getElementById('admin-excel-file-input');
                if (!input || !input.files || input.files.length === 0) {
                    e.preventDefault();
                    if (window.showToast) window.showToast('Silakan pilih file Excel terlebih dahulu!', 'warning');
                    return;
                }
                const btn = document.getElementById('btn-admin-submit-import');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                    btn.textContent = 'Mengimpor...';
                }
                if (window.showToast) window.showToast('Sedang memproses upload dan impor data Excel...', 'info', 6000);
            });
        }

        // Auto switch ke tab import jika ada session success atau error
        @if(session('success') || session('error') || $errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                switchAdminTab('import-excel');
            });
        @endif

        function openEditProfileModal() {
            const modal = document.getElementById('modal-edit-profile');
            if (modal) modal.classList.remove('hidden');
        }

        function closeEditProfileModal() {
            const modal = document.getElementById('modal-edit-profile');
            if (modal) modal.classList.add('hidden');
        }

        function saveProfileChanges(e) {
            e.preventDefault();
            const firstName = document.getElementById('input-first-name').value.trim();
            const lastName = document.getElementById('input-last-name').value.trim();
            const email = document.getElementById('input-email').value.trim();

            if (!firstName || !email) {
                if (window.showToast) window.showToast('Gagal update: Nama dan email wajib diisi!', 'error');
                return;
            }

            document.getElementById('display-first-name').textContent = firstName;
            document.getElementById('display-last-name').textContent = lastName;
            document.getElementById('display-fullname').textContent = `${firstName} ${lastName}`;
            document.getElementById('display-email').textContent = email;

            closeEditProfileModal();
            if (window.showToast) window.showToast('Sukses update data terbaru!', 'success');
        }

        function submitResetRequest() {
            if (window.showToast) window.showToast('Sukses mengajukan perubahan kata sandi ke Super Admin!', 'success');
        }
    </script>

<x-temp-password-guard />
</body>
</html>
