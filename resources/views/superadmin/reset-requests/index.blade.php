<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Persetujuan Reset Sandi — Super Admin KAI Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <x-theme-script />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 flex flex-col justify-between transition-colors duration-200">

    <x-navbar active="reset-requests" />

    <main class="w-full flex-1 max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 sm:pt-6 pb-28 lg:pb-8 flex flex-col gap-4 sm:gap-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-[30px] font-bold tracking-tight text-gray-950 dark:text-white flex items-center gap-3">
                    <span>Permintaan Reset Sandi</span>
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                            {{ $pendingCount }} Menunggu
                        </span>
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-[#9AA0A6] mt-0.5">
                    Tinjau, setujui (kirim kode OTP), atau tolak permohonan reset kata sandi dari pengguna dan admin.
                </p>
            </div>
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

        <!-- Card Container -->
        <div class="rounded-3xl bg-white dark:bg-[#1F2123] border border-gray-100/90 dark:border-white/10 shadow-[0_2px_12px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/10 bg-gray-50/70 dark:bg-white/5">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-[#9AA0A6]">Pengguna</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-[#9AA0A6]">Email & Role</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-[#9AA0A6]">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-[#9AA0A6]">Waktu Request</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-[#9AA0A6]">Kedaluwarsa</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-[#9AA0A6] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($requests as $req)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4 font-semibold text-gray-950 dark:text-white">
                                    {{ $req->user->name ?? 'User #' . $req->user_id }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    <div class="font-medium text-xs">{{ $req->user->email ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-400 capitalize">{{ $req->user->role ?? 'user' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'pending'    => ['bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800', 'Menunggu Persetujuan'],
                                            'approved'   => ['bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800', 'Disetujui'],
                                            'rejected'   => ['bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border border-red-200 dark:border-red-800', 'Ditolak'],
                                            'completed'  => ['bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800', 'Selesai'],
                                            'auto_reset' => ['bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 border border-purple-200 dark:border-purple-800', 'Auto Reset (Temp Pwd)'],
                                        ];
                                        [$cls, $label] = $statusConfig[$req->status] ?? ['bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-300', $req->status];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $req->created_at ? $req->created_at->format('d M Y H:i') : '-' }} WIB
                                </td>
                                <td class="px-6 py-4 text-xs {{ $req->isExpired() ? 'text-red-500 font-medium' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $req->request_expires_at ? $req->request_expires_at->format('d M Y H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($req->isPending())
                                            {{-- Approve --}}
                                            <form action="{{ route('reset-requests.approve', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 bg-[#0066FF] hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition active:scale-95 cursor-pointer shadow-xs"
                                                    onclick="return confirm('Setujui permintaan reset sandi untuk {{ $req->user->name ?? $req->user->email }}? Kode OTP akan dikirim ke email.')">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    Setujui
                                                </button>
                                            </form>

                                            {{-- Reject --}}
                                            <form action="{{ route('reset-requests.reject', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/40 dark:hover:bg-red-900/50 dark:text-red-400 border border-red-200 dark:border-red-800 text-xs font-semibold px-3 py-1.5 rounded-lg transition active:scale-95 cursor-pointer"
                                                    onclick="return confirm('Apakah Anda yakin ingin menolak permohonan reset sandi ini?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Tolak
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sudah diproses</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-10 h-10 mx-auto mb-3 opacity-40 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="font-medium text-sm">Tidak ada permintaan reset sandi saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>

    </main>

    <x-toast />
    <x-temp-password-guard />

</body>
</html>
