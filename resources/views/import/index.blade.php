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
                <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-8 border border-gray-100/90 dark:border-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.02)]">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-6">Upload File Data</h2>

                    <form id="import-form" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Drag & Drop Zone -->
                        <div id="drop-zone"
                            class="relative flex flex-col items-center justify-center py-10 px-6 sm:py-14 sm:px-10 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-2xl hover:border-[#0066FF] dark:hover:border-[#3B82F6] hover:bg-blue-50/20 dark:hover:bg-blue-900/10 transition-all duration-200 cursor-pointer text-center group">

                            <!-- Image Illustration -->
                            <img src="{{ asset('icon-upload-data.png') }}"
                                 alt="Upload File Data"
                                 class="w-40 sm:w-48 h-auto object-contain mx-auto mb-4 select-none pointer-events-none transition-transform duration-200 group-hover:scale-105">

                            <p class="text-sm sm:text-[15px] font-semibold text-gray-800 dark:text-gray-100 mb-1">
                                Pilih file atau drag & drop ke area ini
                            </p>
                            <p class="text-xs sm:text-sm text-gray-400 dark:text-gray-400">
                                file mendukung format .csv, .xlsx, .xls
                            </p>

                            <input type="file" id="file-input" name="excel_file" accept=".xlsx,.xls,.csv,.txt" class="hidden">
                        </div>

                        <!-- File Preview Card -->
                        <div id="file-preview-card" class="hidden mt-4 rounded-xl border border-gray-200 dark:border-white/10 p-3.5 sm:p-4 bg-white dark:bg-[#282A2C] items-center justify-between shadow-xs">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Green Excel Icon -->
                                <div class="h-12 w-12 rounded-xl border border-gray-200/90 dark:border-white/10 flex items-center justify-center bg-emerald-50/40 dark:bg-emerald-950/20 shrink-0">
                                    <svg class="w-7 h-7 text-[#107C41]" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M21.17 3.25Q21.5 3.25 21.75 3.5 22 3.75 22 4.08V19.92Q22 20.25 21.75 20.5 21.17 20.75H7.83Q7.5 20.75 7.25 20.5 7 20.25 7 19.92V17H2.83Q2.5 17 2.25 16.75 2 16.5 2 16.17V7.83Q2 7.5 2.25 7.25 2.5 7 2.83 7H7V4.08Q7 3.75 7.25 3.5 7.5 3.25 7.83 3.25ZM7 8.5H3.5V15.5H7ZM8.5 4.75V19.25H20.5V4.75ZM11.12 7.25H12.88L14.5 10.7 16.12 7.25H17.88L15.38 12 17.88 16.75H16.12L14.5 13.3 12.88 16.75H11.12L13.62 12Z"/>
                                    </svg>
                                </div>
                                <!-- File Name & Size -->
                                <div class="truncate">
                                    <p id="preview-file-name" class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate"></p>
                                    <p id="preview-file-size" class="text-xs text-gray-400 dark:text-[#9AA0A6] mt-0.5"></p>
                                </div>
                            </div>

                            <!-- Close / Remove File Button -->
                            <button type="button" id="btn-remove-file" class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition cursor-pointer" title="Hapus file">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        @error('excel_file')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Action Buttons (Batal & Import) -->
                        <div class="mt-8 flex items-center justify-end gap-3.5">
                            <button type="button" id="btn-cancel" class="inline-flex items-center justify-center min-w-[110px] sm:min-w-[120px] px-7 py-2.5 sm:py-3 rounded-xl bg-[#E50000] hover:bg-[#CC0000] text-white text-sm sm:text-base font-semibold tracking-wide whitespace-nowrap transition-all duration-150 active:scale-95 cursor-pointer shadow-sm">
                                Batal
                            </button>
                            <button type="submit" id="btn-import" class="inline-flex items-center justify-center min-w-[110px] sm:min-w-[120px] px-7 py-2.5 sm:py-3 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm sm:text-base font-semibold tracking-wide whitespace-nowrap transition-all duration-150 active:scale-95 cursor-pointer shadow-sm">
                                <span id="btn-import-text">Import</span>
                            </button>
                        </div>

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
        const previewCard = document.getElementById('file-preview-card');
        const previewName = document.getElementById('preview-file-name');
        const previewSize = document.getElementById('preview-file-size');
        const btnRemove   = document.getElementById('btn-remove-file');
        const btnCancel   = document.getElementById('btn-cancel');
        const btnImport   = document.getElementById('btn-import');
        const btnText     = document.getElementById('btn-import-text');
        const form        = document.getElementById('import-form');

        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function handleFile(file) {
            if (!file) return;
            previewName.textContent = file.name;
            previewSize.textContent = formatBytes(file.size);
            previewCard.classList.remove('hidden');
            previewCard.classList.add('flex');
        }

        function clearFile() {
            input.value = '';
            previewCard.classList.add('hidden');
            previewCard.classList.remove('flex');
        }

        dropZone.addEventListener('click', function () {
            input.click();
        });

        input.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                handleFile(this.files[0]);
            }
        });

        // Remove file button
        btnRemove?.addEventListener('click', function (e) {
            e.stopPropagation();
            clearFile();
        });

        // Cancel button
        btnCancel?.addEventListener('click', function () {
            if (input.files && input.files.length > 0) {
                clearFile();
            } else {
                window.location.href = "{{ route('dashboard') }}";
            }
        });

        // Drag & drop
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('border-[#0066FF]', 'bg-blue-50/30', 'dark:bg-blue-900/10');
        });
        dropZone.addEventListener('dragleave', function () {
            this.classList.remove('border-[#0066FF]', 'bg-blue-50/30', 'dark:bg-blue-900/10');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('border-[#0066FF]', 'bg-blue-50/30', 'dark:bg-blue-900/10');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                handleFile(file);
            }
        });

        // Form submit validation & loading state
        form.addEventListener('submit', function (e) {
            if (!input.files || input.files.length === 0) {
                e.preventDefault();
                alert('Silakan pilih file Excel atau CSV terlebih dahulu.');
                input.click();
                return;
            }
            btnText.textContent = 'Memproses...';
            btnImport.disabled = true;
        });
    })();
    </script>
    <x-temp-password-guard />
</body>
</html>
