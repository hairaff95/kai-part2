<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Import Data — KAI Tracker</title>
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
            <h1 class="text-xl sm:text-[30px] font-bold tracking-tight text-gray-950 dark:text-white">Import Data</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] mt-0.5">Upload file CSV atau Excel sesuai format <span class="font-semibold text-gray-700 dark:text-gray-300">new-kai.csv</span></p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

            <!-- Form Upload -->
            <div class="lg:col-span-2">
                <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-6 border border-gray-100/90 dark:border-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.02)]">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4">Upload File</h2>

                    <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" id="import-form">
                        @csrf

                        <!-- Drop Zone -->
                        <div id="drop-zone"
                             class="relative border-2 border-dashed border-gray-200 dark:border-white/15 rounded-2xl p-8 sm:p-10 flex flex-col items-center justify-center text-center gap-3 transition-all duration-200 cursor-pointer hover:border-[#0066FF] hover:bg-blue-50/40 dark:hover:bg-blue-900/10 group"
                             onclick="document.getElementById('file-input').click()">

                            <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">Klik atau drag & drop file di sini</p>
                                <p class="text-xs sm:text-sm text-gray-400 dark:text-[#9AA0A6] mt-1">Format: <span class="font-medium">.csv, .xlsx, .xls</span> — Maks. 30 MB</p>
                            </div>

                            <div id="file-name-display" class="hidden items-center gap-2 bg-blue-50 dark:bg-blue-900/30 rounded-xl px-3 py-1.5">
                                <svg class="w-4 h-4 text-[#0066FF] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span id="file-name-text" class="text-xs font-semibold text-[#0066FF] truncate max-w-[200px]"></span>
                            </div>

                            <input type="file" id="file-input" name="excel_file" accept=".csv,.xlsx,.xls,.txt" class="hidden">
                        </div>

                        @error('excel_file')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Submit Button -->
                        <button type="submit" id="btn-import"
                            class="mt-4 w-full sm:w-auto flex items-center justify-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span id="btn-import-text">Import Data</span>
                        </button>

                    </form>
                </div>
            </div>

            <!-- Panduan Format Kolom -->
            <div class="lg:col-span-1">
                <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-4 sm:p-6 border border-gray-100/90 dark:border-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.02)] h-full">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white mb-3">Format Kolom CSV</h2>
                    <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mb-3">Header kolom yang dikenali (tidak case-sensitive):</p>

                    <div class="space-y-1.5 text-xs">
                        @php
                            $columns = [
                                ['Nomor Kontrak', 'Wajib'],
                                ['Tanggal Kontrak', 'Opsional'],
                                ['Kelompok', 'Opsional — misal: Non Row'],
                                ['Masa Awal', 'Opsional'],
                                ['Masa Akhir', 'Menentukan status kontrak'],
                                ['Nama Mitra', 'Opsional'],
                                ['Alamat Mitra', 'Opsional'],
                                ['Status Mitra', 'Opsional — misal: Swasta'],
                                ['Peruntukan', 'Opsional'],
                                ['Blok Aset', 'Opsional'],
                                ['Luas tanah (m²)', 'Opsional'],
                                ['Luas bangunan (m²)', 'Opsional'],
                                ['Nomor Aset', 'Opsional'],
                                ['Jenis Aset', 'Opsional — misal: Tanah'],
                                ['Kmhm', 'Opsional'],
                                ['Termin Ke', 'Data tagihan'],
                                ['Virtual Account', 'Data tagihan'],
                                ['Tanggal Termin', 'Data tagihan'],
                                ['Total Bayar', 'Data tagihan'],
                                ['Invoice ID', 'Data tagihan'],
                                ['Transaction ID', 'Data tagihan'],
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
        const input    = document.getElementById('file-input');
        const dropZone = document.getElementById('drop-zone');
        const nameDisplay = document.getElementById('file-name-display');
        const nameText    = document.getElementById('file-name-text');
        const btnText     = document.getElementById('btn-import-text');
        const form        = document.getElementById('import-form');

        function showFileName(name) {
            nameText.textContent = name;
            nameDisplay.classList.remove('hidden');
            nameDisplay.classList.add('flex');
        }

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
                // Transfer ke input
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showFileName(file.name);
            }
        });

        // Loading state on submit
        form.addEventListener('submit', function () {
            btnText.textContent = 'Memproses...';
            document.getElementById('btn-import').disabled = true;
        });
    })();
    </script>
</body>
</html>
