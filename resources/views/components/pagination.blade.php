@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-white/10 text-xs text-gray-500 dark:text-[#9AA0A6]">
        {{-- Info pagination --}}
        <div>
            Menampilkan <span class="font-semibold text-gray-800 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-800 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-800 dark:text-white">{{ $paginator->total() }}</span> data
        </div>

        {{-- Page buttons --}}
        <div class="flex items-center gap-1">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 text-gray-300 dark:text-gray-600 cursor-not-allowed select-none">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-[#2D3034] text-gray-700 dark:text-gray-200 transition">
                    Sebelumnya
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage    = $paginator->lastPage();
                $startPage   = max(1, $currentPage - 2);
                $endPage     = min($lastPage, $currentPage + 2);
            @endphp

            @if ($startPage > 1)
                <a href="{{ $paginator->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-[#2D3034] text-gray-700 dark:text-gray-200 transition">
                    1
                </a>
                @if ($startPage > 2)
                    <span class="px-1 text-gray-400">...</span>
                @endif
            @endif

            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#0066FF] text-white font-semibold shadow-xs">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-[#2D3034] text-gray-700 dark:text-gray-200 transition">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <span class="px-1 text-gray-400">...</span>
                @endif
                <a href="{{ $paginator->url($lastPage) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-[#2D3034] text-gray-700 dark:text-gray-200 transition">
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-[#2D3034] text-gray-700 dark:text-gray-200 transition">
                    Selanjutnya
                </a>
            @else
                <span class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 text-gray-300 dark:text-gray-600 cursor-not-allowed select-none">
                    Selanjutnya
                </span>
            @endif
        </div>
    </div>
@endif
