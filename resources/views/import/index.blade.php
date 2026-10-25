<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Import Data Excel & CSV — KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="import" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-6 pb-28 lg:pb-8 flex flex-col gap-4 sm:gap-5">

        <!-- Page Header -->
        <div class="shrink-0">
            <h1 class="text-xl sm:text-[30px] font-bold tracking-tight text-gray-950 dark:text-white">Import Data Kontrak & Tagihan</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] mt-0.5">Mendukung format file <strong class="text-gray-700 dark:text-gray-300">Microsoft Excel (.xlsx, .xls)</strong> dan <strong class="text-gray-700 dark:text-gray-300">CSV (.csv)</strong></p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-red-500 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

            <!-- Area Upload File -->
            <div class="lg:col-span-2">
                <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-6 border border-gray-100/90 dark:border-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.02)]">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white mb-1">Unggah Berkas Excel / CSV</h2>
                    <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mb-5">Pilih file spreadsheet Excel (.xlsx, .xls) atau CSV dari komputer Anda untuk memperbarui data kontrak, aset, mitra, dan tagihan secara otomatis.</p>

                    <form id="import-form" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Drag & Drop Zone -->
                        <div id="drop-zone"
                            class="relative flex flex-col items-center justify-center p-8 sm:p-12 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-2xl hover:border-[#0066FF] dark:hover:border-[#3B82F6] hover:bg-blue-50/20 dark:hover:bg-blue-900/10 transition-all duration-200 cursor-pointer text-center gap-3 group">

                            <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">Klik atau drag & drop file Excel / CSV di sini</p>
                                <p class="text-xs sm:text-sm text-gray-400 dark:text-[#9AA0A6] mt-1">Format didukung: <span class="font-bold text-[#0066FF] dark:text-[#3B82F6]">.xlsx, .xls, .csv</span> — Maksimal 30 MB</p>
                            </div>

                            <div id="file-name-display" class="hidden items-center gap-2 bg-blue-50 dark:bg-blue-900/30 rounded-xl px-3 py-1.5">
                                <svg class="w-4 h-4 text-[#0066FF] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span id="file-name-text" class="text-xs font-semibold text-[#0066FF] truncate max-w-[280px]"></span>
                            </div>

                            <input type="file" id="file-input" name="excel_file" accept=".xlsx,.xls,.csv,.txt" class="hidden">
                        </div>

                        @error('excel_file')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Submit Button -->
                        <button type="submit" id="btn-import"
                            class="mt-4 w-full sm:w-auto flex items-center justify-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span id="btn-import-text">Import File Excel / CSV</span>
                        </button>

                    </form>
                </div>
            </div>

            <!-- Panduan Format Kolom -->
            <div class="lg:col-span-1">
                <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-6 border border-gray-100/90 dark:border-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.02)] h-full">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white mb-2">Panduan Kolom Excel & CSV</h2>
                    <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mb-3">Header kolom dikenali secara otomatis (tidak case-sensitive):</p>

                    <div class="space-y-1.5 text-xs max-h-[380px] overflow-y-auto pr-1">
                        @php
                            $columns = [
                                ['Nomor Kontrak', 'Wajib — Kunci unik kontrak'],
                                ['Tanggal Kontrak', 'Opsional'],
                                ['Kelompok', 'Opsional — contoh: Non Row / Row'],
                                ['Masa Awal', 'Opsional — Tanggal awal sewa'],
                                ['Masa Akhir', 'Menentukan status kontrak & backlog'],
                                ['Nama Mitra', 'Opsional — Nama penyewa'],
                                ['Alamat Mitra', 'Opsional'],
                                ['Status Mitra', 'Opsional — misal: Swasta / BUMN'],
                                ['Peruntukan', 'Opsional'],
                                ['Blok Aset', 'Opsional — Nama blok/lokasi'],
                                ['Luas tanah (m²)', 'Opsional'],
                                ['Luas bangunan (m²)', 'Opsional'],
                                ['Nomor Aset', 'Opsional'],
                                ['Jenis Aset', 'Opsional — misal: Tanah'],
                                ['Kmhm', 'Opsional'],
                                ['Termin Ke', 'Data tagihan per termin'],
                                ['Virtual Account', 'Data tagihan nomor VA'],
                                ['Tanggal Termin', 'Tanggal jatuh tempo termin'],
                                ['Total Bayar', 'Nominal tagihan'],
                                ['Invoice ID', 'Nomor invoice terbit'],
                                ['Transaction ID', 'Status pembayaran lunas'],
                            ];
                        @endphp

                        @foreach($columns as [$col, $desc])
                            <div class="flex items-start gap-2">
                                <code class="shrink-0 bg-gray-50 dark:bg-[#2D3034] text-gray-800 dark:text-gray-200 rounded px-1.5 py-0.5 text-[10px] font-mono">{{ $col }}</code>
                                <span class="text-gray-400 dark:text-[#9AA0A6] text-[10px] mt-0.5 leading-tight">{{ $desc }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/10">
                        <p class="text-[10px] text-gray-400 dark:text-[#9AA0A6]">
                            Format tanggal yang didukung:<br>
                            <code class="bg-gray-50 dark:bg-[#2D3034] rounded px-1 text-[10px]">Excel Date Format</code>
                            <code class="bg-gray-50 dark:bg-[#2D3034] rounded px-1 text-[10px]">DD.MM.YYYY</code>
                            <code class="bg-gray-50 dark:bg-[#2D3034] rounded px-1 text-[10px]">DD/MM/YYYY</code>
                            <code class="bg-gray-50 dark:bg-[#2D3034] rounded px-1 text-[10px]">YYYY-MM-DD</code>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <x-toast />

    <script>
    (function () {
        const input       = document.getElementById('file-input');
        const dropZone    = document.getElementById('drop-zone');
        const nameDisplay = document.getElementById('file-name-display');
        const nameText    = document.getElementById('file-name-text');
        const btnText     = document.getElementById('btn-import-text');
        const form        = document.getElementById('import-form');

        function showFileName(name) {
            nameText.textContent = name;
            nameDisplay.classList.remove('hidden');
            nameDisplay.classList.add('flex');
        }

        dropZone.addEventListener('click', function () {
            input.click();
        });

        input.addEventListener('change', function () {
            if (this.files.length > 0) showFileName(this.files[0].name);
        });

        // Drag & drop
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('border-[#0066FF]', 'bg-blue-50/40', 'dark:bg-blue-900/10');
        });
        dropZone.addEventListener('dragleave', function () {
            this.classList.remove('border-[#0066FF]', 'bg-blue-50/40', 'dark:bg-blue-900/10');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('border-[#0066FF]', 'bg-blue-50/40', 'dark:bg-blue-900/10');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showFileName(file.name);
            }
        });

        // Loading state on submit
        form.addEventListener('submit', function () {
            btnText.textContent = 'Memproses berkas...';
            document.getElementById('btn-import').disabled = true;
        });
    })();
    </script>
</body>
</html>
