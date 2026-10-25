<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Tambah Data Tagihan — KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="payments" />

    <main class="w-full flex-1 max-w-[1200px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-6 pb-28 lg:pb-8 flex flex-col gap-4 sm:gap-6">

        <form action="{{ route('payments.store') }}" method="POST" id="formCreatePayment" class="flex flex-col gap-5">
            @csrf

            <!-- Page Header with Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                <div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-[#9AA0A6] mb-1">
                        <a href="{{ route('payments.index') }}" class="hover:text-[#0066FF] transition">Data Tagihan</a>
                        <span>/</span>
                        <span class="text-gray-600 dark:text-gray-300 font-medium">Tambah Tagihan</span>
                    </div>
                    <h1 class="text-xl sm:text-[28px] font-bold tracking-tight text-gray-950 dark:text-white">
                        Tambah Data Tagihan Baru
                    </h1>
                </div>

                <div class="flex items-center gap-2.5 self-start sm:self-auto">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Tagihan</span>
                    </button>
                    <a href="{{ route('payments.index') }}"
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
                        Terdapat kesalahan formulir:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-3xl bg-white dark:bg-[#1F2123] p-5 sm:p-6 shadow-[0_2px_12px_rgba(0,0,0,0.02)] border border-gray-100/90 dark:border-white/10 flex flex-col gap-4">
                <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-white/10 pb-3">
                    Detail Tagihan & Pembayaran
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nomor Kontrak <span class="text-red-500">*</span>
                        </label>
                        <input list="contractsList" name="nomor_kontrak" value="{{ old('nomor_kontrak') }}" required placeholder="Ketik atau pilih nomor kontrak..."
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white placeholder-gray-400 font-semibold">
                        <datalist id="contractsList">
                            @foreach($contracts as $c)
                                <option value="{{ $c->nomor_kontrak }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Termin Ke <span class="text-red-500">*</span>
                        </label>
                        <input type="number" min="1" name="termin_ke" value="{{ old('termin_ke', 1) }}" required
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Total Bayar / Tagihan (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="total_bayar" value="{{ old('total_bayar', 0) }}" required placeholder="0.00"
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Tanggal Termin / Jatuh Tempo
                        </label>
                        <input type="date" name="tanggal_termin" value="{{ old('tanggal_termin') }}"
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Virtual Account (VA)
                        </label>
                        <input type="text" name="virtual_account" value="{{ old('virtual_account') }}" placeholder="Contoh: 121880000000001"
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white font-mono placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Invoice ID
                        </label>
                        <input type="text" name="invoice_id" value="{{ old('invoice_id') }}" placeholder="Contoh: INV-2026-001"
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white font-mono placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Transaction ID
                        </label>
                        <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="Contoh: TRX-2026-001"
                            class="w-full h-10 px-3.5 text-xs sm:text-sm bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-[#0066FF] text-gray-900 dark:text-white font-mono placeholder-gray-400">
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('payments.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-200 dark:bg-white/10 hover:bg-gray-300 dark:hover:bg-white/15 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 transition">
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] hover:bg-blue-700 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Tagihan</span>
                </button>
            </div>

        </form>

    </main>

    <x-toast />

</body>
</html>
