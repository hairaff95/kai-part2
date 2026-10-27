<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

    <title>{{ config('app.name', 'KAI Tracker') }} — Peta</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Theme Script -->
    <script>
        if (localStorage.getItem('kai_theme') === 'dark' || (!('kai_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite([
            'resources/css/app.css',
            'resources/js/app.js',
        ])
    @endif

    {{-- Leaflet JS & CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .tool-active-bg {
            background-color: rgba(229, 231, 235, 0.8) !important;
        }
        .dark .tool-active-bg {
            background-color: rgba(255, 255, 255, 0.15) !important;
        }

        #map {
            background-color: #F6F7F9 !important;
            cursor: grab !important;
        }
        .dark #map {
            background-color: #282A2C !important;
        }

        #map:active, #map.leaflet-drag-target {
            cursor: grabbing !important;
        }

        .leaflet-container {
            background: #F6F7F9 !important;
            font-family: inherit;
            outline: none;
            cursor: grab !important;
        }
        .dark .leaflet-container {
            background: #282A2C !important;
        }

        .leaflet-container:active, .leaflet-container.leaflet-drag-target {
            cursor: grabbing !important;
        }

        .leaflet-grab {
            cursor: grab !important;
        }

        .leaflet-popup-content-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            border-radius: 24px !important;
        }

        .leaflet-popup-content {
            margin: 0 !important;
            width: auto !important;
            line-height: inherit !important;
        }

        .leaflet-popup-tip-container {
            display: none !important;
        }

        .leaflet-container a.btn-detail-lanjutan,
        .leaflet-container a.btn-detail-lanjutan * {
            color: #ffffff !important;
            text-decoration: none !important;
        }

        .leaflet-container a.btn-buka-maps {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
            color: #374151 !important;
            text-decoration: none !important;
        }

        .leaflet-container a.btn-buka-maps * {
            color: #374151 !important;
        }

        .dark .leaflet-container a.btn-buka-maps {
            background-color: #2D3034 !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            text-decoration: none !important;
        }

        .dark .leaflet-container a.btn-buka-maps * {
            color: #ffffff !important;
        }

        .custom-pin-wrapper {
            background: transparent;
            border: none;
        }

        .custom-pin-marker {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .custom-pin-marker:hover {
            transform: scale(1.2) translateY(-2px);
        }
    </style>
</head>

<body class="h-screen overflow-hidden bg-[#F6F7F9] dark:bg-[#282A2C] font-sans antialiased text-gray-900 dark:text-gray-100 selection:bg-blue-100 selection:text-blue-600 relative transition-colors duration-200">

    {{-- Navbar --}}
    <x-navbar active="map" />

    {{-- Header: Judul Heatmaps, Tools Zoom, & Tombol Filter (z-30) --}}
    <div class="relative z-30 w-full max-w-[1600px] mx-auto px-4 sm:px-8 lg:px-10 pt-4 pointer-events-none">

        {{-- Mobile: Left (Title + Tools underneath) and Right (Filter button) --}}
        <div class="flex items-start justify-between lg:hidden">
            <div class="pointer-events-auto flex flex-col gap-2">
                <h1 class="text-[22px] font-bold text-gray-950 dark:text-white tracking-tight leading-tight">
                    Heatmaps
                </h1>
                
                {{-- Zoom tools underneath Heatmaps text --}}
                <div class="flex items-center gap-1.5">
                    <button
                        id="zoomInButton"
                        type="button"
                        onclick="if(window.mapZoomIn) window.mapZoomIn();"
                        class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-[#8E8E8E] dark:border-white/15 bg-white/90 dark:bg-[#34383D]/90 backdrop-blur-sm hover:bg-gray-100 dark:hover:bg-[#40454B] active:bg-gray-200 text-gray-700 dark:text-white transition cursor-pointer shadow-xs"
                        title="Zoom In"
                    >
                        <x-icon name="zoom-in" class="w-4 h-4" />
                    </button>
                    <button
                        id="zoomOutButton"
                        type="button"
                        onclick="if(window.mapZoomOut) window.mapZoomOut();"
                        class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-[#8E8E8E] dark:border-white/15 bg-white/90 dark:bg-[#34383D]/90 backdrop-blur-sm hover:bg-gray-100 dark:hover:bg-[#40454B] active:bg-gray-200 text-gray-700 dark:text-white transition cursor-pointer shadow-xs"
                        title="Zoom Out"
                    >
                        <x-icon name="zoom-out" class="w-4 h-4" />
                    </button>
                    <button
                        id="panToolButton"
                        type="button"
                        onclick="if(window.mapPanTool) window.mapPanTool();"
                        class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-[#8E8E8E] dark:border-white/15 bg-white/90 dark:bg-[#34383D]/90 backdrop-blur-sm hover:bg-gray-100 dark:hover:bg-[#40454B] active:bg-gray-200 text-gray-700 dark:text-white transition cursor-pointer shadow-xs"
                        title="Pusatkan Peta Indonesia"
                    >
                        <x-icon name="hand-pan" class="w-4 h-4 text-gray-600 dark:text-white" />
                    </button>
                </div>
            </div>

            <div class="pointer-events-auto">
                <button
                    id="filterButton"
                    type="button"
                    class="w-11 h-11 flex items-center justify-center rounded-[10px] bg-white dark:bg-[#1F2123] shadow-[0_4px_12px_rgba(0,0,0,0.08)] dark:shadow-none border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-[#2D3034] active:bg-gray-100 text-gray-800 dark:text-white transition cursor-pointer z-30 relative select-none"
                    style="cursor: pointer !important;"
                    title="Filter Peta"
                >
                    <x-icon name="filter-peta" class="w-5 h-5 text-gray-800 dark:text-white pointer-events-none" />
                </button>
            </div>
        </div>

        {{-- Desktop: single row (title + center tools + filter button) --}}
        <div class="hidden lg:flex items-center justify-between relative min-h-[44px]">
            <div class="pointer-events-auto">
                <h1 class="text-[30px] font-bold text-gray-950 dark:text-white tracking-tight">
                    Heatmaps
                </h1>
            </div>

            <div class="pointer-events-auto absolute left-1/2 -translate-x-1/2 flex items-center gap-[5px] bg-transparent">
                <button
                    id="zoomInButtonDesktop"
                    type="button"
                    onclick="if(window.mapZoomIn) window.mapZoomIn();"
                    class="w-11 h-11 flex items-center justify-center rounded-[10px] border-2 border-[#8E8E8E] dark:border-white/20 bg-transparent hover:bg-gray-200/80 dark:hover:bg-[#34383D] active:bg-gray-300/80 text-gray-700 dark:text-white transition cursor-pointer"
                    title="Zoom In"
                >
                    <x-icon name="zoom-in" class="w-6 h-6" />
                </button>
                <button
                    id="zoomOutButtonDesktop"
                    type="button"
                    onclick="if(window.mapZoomOut) window.mapZoomOut();"
                    class="w-11 h-11 flex items-center justify-center rounded-[10px] border-2 border-[#8E8E8E] dark:border-white/20 bg-transparent hover:bg-gray-200/80 dark:hover:bg-[#34383D] active:bg-gray-300/80 text-gray-700 dark:text-white transition cursor-pointer"
                    title="Zoom Out"
                >
                    <x-icon name="zoom-out" class="w-6 h-6" />
                </button>
                <button
                    id="panToolButtonDesktop"
                    type="button"
                    onclick="if(window.mapPanTool) window.mapPanTool();"
                    class="w-11 h-11 flex items-center justify-center rounded-[10px] border-2 border-[#8E8E8E] dark:border-white/20 bg-transparent hover:bg-gray-200/80 dark:hover:bg-[#34383D] active:bg-gray-300/80 text-gray-700 dark:text-white transition cursor-pointer"
                    title="Pusatkan Peta Indonesia"
                >
                    <x-icon name="hand-pan" class="w-6 h-6 text-gray-600 dark:text-white" />
                </button>
            </div>

            <div class="pointer-events-auto">
                <button
                    id="filterButtonDesktop"
                    type="button"
                    class="w-11 h-11 sm:w-12 sm:h-12 flex items-center justify-center rounded-[10px] bg-white dark:bg-[#1F2123] shadow-[0_4px_12px_rgba(0,0,0,0.06)] dark:shadow-none border border-gray-100 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-[#2D3034] active:bg-gray-100 text-gray-800 dark:text-white transition-transform duration-300 ease-in-out cursor-pointer z-50 relative select-none"
                    style="cursor: pointer !important;"
                    title="Filter Peta"
                >
                    <x-icon name="filter-peta" class="w-6 h-6 text-gray-800 dark:text-white pointer-events-none" />
                </button>
            </div>
        </div>
    </div>

    {{-- Map Canvas --}}
    <div id="map" class="absolute inset-0 z-0 h-screen w-full"></div>

    {{-- Overlay Background Filter (z-30 di bawah tombol) --}}
    <div id="filterOverlay" class="invisible fixed inset-0 z-30 bg-black/[0.08] opacity-0 transition-all duration-300 pointer-events-auto"></div>

    {{-- Modal Filter (Slide in from Right on both Mobile & Desktop, scaled down for Mobile) --}}
    <aside
        id="filterModal"
        class="fixed right-3 sm:right-6 lg:right-10 top-16 sm:top-20 bottom-24 lg:bottom-8 z-40 w-[295px] sm:w-[350px] lg:w-[373px] bg-white dark:bg-[#1F2123] rounded-2xl lg:rounded-[10px] p-4 sm:p-6 lg:p-7 shadow-[0_20px_50px_rgba(0,0,0,0.14)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.6)] border border-gray-100/90 dark:border-white/5 transition-all duration-300 ease-in-out transform translate-x-[140%] opacity-0 pointer-events-none flex flex-col justify-start overflow-y-auto"
    >
        <div class="mb-3 sm:mb-5 lg:mb-6 flex items-center justify-between">
            <h2 class="text-base sm:text-lg lg:text-[20px] font-bold text-gray-950 dark:text-white tracking-tight">
                Filter Peta
            </h2>
        </div>

        {{-- FILTER 2-COLUMN GRID --}}
        <div class="grid grid-cols-2 gap-x-2.5 sm:gap-x-4 gap-y-3 sm:gap-y-5">

            {{-- 1. STASIUN --}}
            <div class="flex flex-col">
                <label class="flex items-center gap-1 sm:gap-[6px] text-[11px] sm:text-xs lg:text-sm font-semibold text-gray-700 dark:text-[#9AA0A6] mb-1 sm:mb-[6px]">
                    <x-icon name="subway" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 text-gray-400 dark:text-[#9AA0A6]" />
                    Stasiun
                </label>
                <div class="relative custom-filter-container">
                    <input type="hidden" id="stasiun" name="stasiun" value="">
                    <button type="button" class="filter-dropdown-btn flex items-center justify-between w-full h-[36px] sm:h-[42px] lg:h-[46px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3.5 py-1.5 sm:py-2 transition cursor-pointer">
                        <span class="filter-selected-label text-[#8B8B8B] dark:text-[#9AA0A6] font-medium text-[11px] sm:text-xs lg:text-[13px] truncate select-none">Semua Stasiun</span>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] shrink-0 ml-1 pointer-events-none" />
                    </button>
                    <div class="filter-dropdown-menu hidden absolute left-0 top-full mt-1 z-[100] w-full min-w-[140px] max-h-[200px] overflow-y-auto rounded-xl lg:rounded-2xl bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1 sm:p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectMapFilter('stasiun', '', 'Semua Stasiun')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium bg-blue-50 dark:bg-blue-600/20 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Semua Stasiun</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('stasiun', 'gambir', 'Gambir')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Gambir</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('stasiun', 'bandung', 'Bandung')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Bandung</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('stasiun', 'surabaya', 'Surabaya')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Surabaya</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('stasiun', 'semarang', 'Semarang Poncol')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Semarang Poncol</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('stasiun', 'pekalongan', 'Pekalongan')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Pekalongan</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('stasiun', 'tegal', 'Tegal')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Tegal</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. WILAYAH --}}
            <div class="flex flex-col">
                <label class="flex items-center gap-1 sm:gap-[6px] text-[11px] sm:text-xs lg:text-sm font-semibold text-gray-700 dark:text-[#9AA0A6] mb-1 sm:mb-[6px]">
                    <x-icon name="explore_nearby" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 text-gray-400 dark:text-[#9AA0A6]" />
                    Wilayah
                </label>
                <div class="relative custom-filter-container">
                    <input type="hidden" id="wilayah" name="wilayah" value="">
                    <button type="button" class="filter-dropdown-btn flex items-center justify-between w-full h-[36px] sm:h-[42px] lg:h-[46px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3.5 py-1.5 sm:py-2 transition cursor-pointer">
                        <span class="filter-selected-label text-[#8B8B8B] dark:text-[#9AA0A6] font-medium text-[11px] sm:text-xs lg:text-[13px] truncate select-none">Row & Non Row</span>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] shrink-0 ml-1 pointer-events-none" />
                    </button>
                    <div class="filter-dropdown-menu hidden absolute left-0 top-full mt-1 z-[100] w-full min-w-[140px] max-h-[200px] overflow-y-auto rounded-xl lg:rounded-2xl bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1 sm:p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectMapFilter('wilayah', '', 'Row & Non Row')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium bg-blue-50 dark:bg-blue-600/20 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Row & Non Row</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('wilayah', 'row', 'Row')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Row</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('wilayah', 'non-row', 'Non Row')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Non Row</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. ASET --}}
            <div class="flex flex-col">
                <label class="flex items-center gap-1 sm:gap-[6px] text-[11px] sm:text-xs lg:text-sm font-semibold text-gray-700 dark:text-[#9AA0A6] mb-1 sm:mb-[6px]">
                    <x-icon name="aset-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 text-gray-400 dark:text-[#9AA0A6]" />
                    Aset
                </label>
                <div class="relative custom-filter-container">
                    <input type="hidden" id="aset" name="aset" value="">
                    <button type="button" class="filter-dropdown-btn flex items-center justify-between w-full h-[36px] sm:h-[42px] lg:h-[46px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3.5 py-1.5 sm:py-2 transition cursor-pointer">
                        <span class="filter-selected-label text-[#8B8B8B] dark:text-[#9AA0A6] font-medium text-[11px] sm:text-xs lg:text-[13px] truncate select-none">Semua Aset</span>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] shrink-0 ml-1 pointer-events-none" />
                    </button>
                    <div class="filter-dropdown-menu hidden absolute left-0 top-full mt-1 z-[100] w-full min-w-[140px] max-h-[200px] overflow-y-auto rounded-xl lg:rounded-2xl bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1 sm:p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectMapFilter('aset', '', 'Semua Aset')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium bg-blue-50 dark:bg-blue-600/20 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Semua Aset</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('aset', 'tanah', 'Tanah')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Tanah</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('aset', 'bangunan', 'Bangunan')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Bangunan</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4. JENIS KONTRAK --}}
            <div class="flex flex-col">
                <label class="flex items-center gap-1 sm:gap-[6px] text-[11px] sm:text-xs lg:text-sm font-semibold text-gray-700 dark:text-[#9AA0A6] mb-1 sm:mb-[6px]">
                    <x-icon name="contract-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 text-gray-400 dark:text-[#9AA0A6]" />
                    Jenis Kontrak
                </label>
                <div class="relative custom-filter-container">
                    <input type="hidden" id="jenis_kontrak" name="jenis_kontrak" value="">
                    <button type="button" class="filter-dropdown-btn flex items-center justify-between w-full h-[36px] sm:h-[42px] lg:h-[46px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3.5 py-1.5 sm:py-2 transition cursor-pointer">
                        <span class="filter-selected-label text-[#8B8B8B] dark:text-[#9AA0A6] font-medium text-[11px] sm:text-xs lg:text-[13px] truncate select-none">Semua Kontrak</span>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] shrink-0 ml-1 pointer-events-none" />
                    </button>
                    <div class="filter-dropdown-menu hidden absolute left-0 top-full mt-1 z-[100] w-full min-w-[140px] max-h-[200px] overflow-y-auto rounded-xl lg:rounded-2xl bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1 sm:p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectMapFilter('jenis_kontrak', '', 'Semua Kontrak')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium bg-blue-50 dark:bg-blue-600/20 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Semua Kontrak</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('jenis_kontrak', 'sewa', 'Sewa')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Sewa</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('jenis_kontrak', 'kerjasama', 'Kerja Sama')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Kerja Sama</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 5. JENIS PENDAPATAN --}}
            <div class="flex flex-col">
                <label class="flex items-center gap-1 sm:gap-[6px] text-[11px] sm:text-xs lg:text-sm font-semibold text-gray-700 dark:text-[#9AA0A6] mb-1 sm:mb-[6px]">
                    <x-icon name="jenis-pendapatan-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 text-gray-400 dark:text-[#9AA0A6]" />
                    Jenis Pendapatan
                </label>
                <div class="relative custom-filter-container">
                    <input type="hidden" id="jenis_pendapatan" name="jenis_pendapatan" value="">
                    <button type="button" class="filter-dropdown-btn flex items-center justify-between w-full h-[36px] sm:h-[42px] lg:h-[46px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3.5 py-1.5 sm:py-2 transition cursor-pointer">
                        <span class="filter-selected-label text-[#8B8B8B] dark:text-[#9AA0A6] font-medium text-[11px] sm:text-xs lg:text-[13px] truncate select-none">Semua Pend...</span>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] shrink-0 ml-1 pointer-events-none" />
                    </button>
                    <div class="filter-dropdown-menu hidden absolute left-0 top-full mt-1 z-[100] w-full min-w-[140px] max-h-[200px] overflow-y-auto rounded-xl lg:rounded-2xl bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1 sm:p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectMapFilter('jenis_pendapatan', '', 'Semua Pend...')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium bg-blue-50 dark:bg-blue-600/20 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Semua Pend...</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('jenis_pendapatan', 'sewa', 'Sewa')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Sewa</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('jenis_pendapatan', 'iklan', 'Iklan')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Iklan</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('jenis_pendapatan', 'lainnya', 'Lainnya')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Lainnya</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 6. SPV --}}
            <div class="flex flex-col">
                <label class="flex items-center gap-1 sm:gap-[6px] text-[11px] sm:text-xs lg:text-sm font-semibold text-gray-700 dark:text-[#9AA0A6] mb-1 sm:mb-[6px]">
                    <x-icon name="spv-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 text-gray-400 dark:text-[#9AA0A6]" />
                    SPV
                </label>
                <div class="relative custom-filter-container">
                    <input type="hidden" id="spv" name="spv" value="">
                    <button type="button" class="filter-dropdown-btn flex items-center justify-between w-full h-[36px] sm:h-[42px] lg:h-[46px] bg-white dark:bg-[#2D3034] border border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-lg lg:rounded-[10px] px-2.5 sm:px-3.5 py-1.5 sm:py-2 transition cursor-pointer">
                        <span class="filter-selected-label text-[#8B8B8B] dark:text-[#9AA0A6] font-medium text-[11px] sm:text-xs lg:text-[13px] truncate select-none">Semua SPV</span>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-400 dark:text-[#9AA0A6] shrink-0 ml-1 pointer-events-none" />
                    </button>
                    <div class="filter-dropdown-menu hidden absolute left-0 top-full mt-1 z-[100] w-full min-w-[140px] max-h-[200px] overflow-y-auto rounded-xl lg:rounded-2xl bg-white dark:bg-[#2D3034] border border-gray-100 dark:border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.14)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)] p-1 sm:p-1.5 flex flex-col gap-0.5">
                        <button type="button" onclick="selectMapFilter('spv', '', 'Semua SPV')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium bg-blue-50 dark:bg-blue-600/20 text-[#0066FF] dark:text-[#3B82F6] rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>Semua SPV</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('spv', 'spv1', 'SPV 1')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>SPV 1</span>
                        </button>
                        <button type="button" onclick="selectMapFilter('spv', 'spv2', 'SPV 2')" class="filter-option-btn flex items-center justify-between w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-[11px] sm:text-xs lg:text-[13px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 rounded-lg lg:rounded-xl transition text-left cursor-pointer">
                            <span>SPV 2</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ACTION BUTTONS --}}
        <div class="mt-4 sm:mt-6 lg:mt-8 flex gap-2 sm:gap-3.5 shrink-0">
            <button
                id="applyFilter"
                type="button"
                class="h-[38px] sm:h-[44px] lg:h-[48px] flex-1 rounded-lg lg:rounded-[10px] bg-[#0066FF] hover:bg-blue-700 text-xs sm:text-sm font-semibold text-white transition flex items-center justify-center gap-1.5 sm:gap-2 shadow-xs cursor-pointer"
            >
                <x-icon name="filter-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" />
                Terapkan Filter
            </button>

            <button
                id="resetFilter"
                type="button"
                class="h-[38px] sm:h-[44px] lg:h-[48px] w-[75px] sm:w-[95px] lg:w-[115px] rounded-lg lg:rounded-[10px] border border-gray-300 dark:border-white/15 bg-white dark:bg-[#2D3034] text-xs sm:text-sm font-semibold text-gray-800 dark:text-white transition hover:bg-gray-50 dark:hover:bg-white/10 cursor-pointer"
            >
                Reset
            </button>
        </div>

    </aside>


    {{-- Javascript & Peta Leaflet --}}
    <script>
        // Data aset dari database
        const assets = @json($assets);

        let indonesiaGeojsonLayer = null;
        let indonesiaBounds = null;
        let baseIndonesiaZoom = 5;

        // Inisialisasi peta Leaflet dengan smooth & responsive zoom
        const map = L.map('map', {
            center: [-2.2, 118.0],
            zoom: 5,
            zoomSnap: 0.1,
            zoomDelta: 0.5,
            wheelPxPerZoomLevel: 85,
            wheelDebounceTime: 0,
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true,
            zoomControl: false,
            attributionControl: false,
            dragging: true,
            touchZoom: true,
            scrollWheelZoom: true,
            doubleClickZoom: true
        });

        // Pusatkan peta ke kepulauan Indonesia
        function fitIndonesiaBounds() {
            if (indonesiaBounds) {
                map.fitBounds(indonesiaBounds, {
                    padding: [10, 10],
                    animate: true
                });
            }
        }

        const zoomInButton = document.getElementById('zoomInButton');
        const zoomOutButton = document.getElementById('zoomOutButton');
        const panToolButton = document.getElementById('panToolButton');

        let zoomInTimer = null;
        let zoomOutTimer = null;
        let isCurrentlyZooming = false;
        let zoomEndTimer = null;

        function flashZoomIn() {
            if (!zoomInButton) return;
            isCurrentlyZooming = true;
            zoomInButton.classList.add('tool-active-bg');
            if (panToolButton) panToolButton.classList.remove('tool-active-bg');
            clearTimeout(zoomInTimer);
            zoomInTimer = setTimeout(() => {
                zoomInButton.classList.remove('tool-active-bg');
            }, 300);
            clearTimeout(zoomEndTimer);
            zoomEndTimer = setTimeout(() => {
                isCurrentlyZooming = false;
            }, 350);
        }

        function flashZoomOut() {
            if (!zoomOutButton) return;
            isCurrentlyZooming = true;
            zoomOutButton.classList.add('tool-active-bg');
            if (panToolButton) panToolButton.classList.remove('tool-active-bg');
            clearTimeout(zoomOutTimer);
            zoomOutTimer = setTimeout(() => {
                zoomOutButton.classList.remove('tool-active-bg');
            }, 300);
            clearTimeout(zoomEndTimer);
            zoomEndTimer = setTimeout(() => {
                isCurrentlyZooming = false;
            }, 350);
        }

        function setPanToolVisual(active) {
            if (!panToolButton || isCurrentlyZooming) return;
            if (active) {
                panToolButton.classList.add('tool-active-bg');
            } else {
                panToolButton.classList.remove('tool-active-bg');
            }
        }

        window.mapZoomIn = function() {
            flashZoomIn();
            map.setZoom(Math.floor(map.getZoom()) + 1);
        };

        window.mapZoomOut = function() {
            flashZoomOut();
            map.setZoom(Math.ceil(map.getZoom()) - 1);
        };

        window.mapPanTool = function() {
            fitIndonesiaBounds();
        };

        let lastZoomLevel = map.getZoom();

        map.on('zoomstart', function() {
            isCurrentlyZooming = true;
            if (panToolButton) panToolButton.classList.remove('tool-active-bg');
            lastZoomLevel = map.getZoom();
        });

        map.on('zoomanim', function(e) {
            isCurrentlyZooming = true;
            if (panToolButton) panToolButton.classList.remove('tool-active-bg');
            if (e.zoom > lastZoomLevel) {
                flashZoomIn();
            } else if (e.zoom < lastZoomLevel) {
                flashZoomOut();
            }
            lastZoomLevel = e.zoom;
        });

        map.on('zoom', function() {
            isCurrentlyZooming = true;
            if (panToolButton) panToolButton.classList.remove('tool-active-bg');
            const currentZoom = map.getZoom();
            if (currentZoom > lastZoomLevel) {
                flashZoomIn();
            } else if (currentZoom < lastZoomLevel) {
                flashZoomOut();
            }
            lastZoomLevel = currentZoom;
        });

        map.on('zoomend', function() {
            clearTimeout(zoomEndTimer);
            zoomEndTimer = setTimeout(() => {
                isCurrentlyZooming = false;
            }, 300);
        });

        map.on('dragstart', function() {
            if (!isCurrentlyZooming) {
                setPanToolVisual(true);
            }
        });

        map.on('drag', function() {
            if (!isCurrentlyZooming) {
                setPanToolVisual(true);
            }
        });

        map.on('dragend', function() {
            setPanToolVisual(false);
        });

        window.addEventListener('mouseup', function() {
            setPanToolVisual(false);
        });

        window.addEventListener('touchend', function() {
            setPanToolVisual(false);
        });

        // Ikon Pin Lokasi
        const redPinIcon = L.divIcon({
            className: 'custom-pin-wrapper',
            html: `
                <div class="custom-pin-marker cursor-pointer hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 drop-shadow-[0_4px_8px_rgba(190,0,0,0.4)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.62 8.45C19.57 3.83 15.54 1.75 12 1.75C12 1.75 12 1.75 11.99 1.75C8.45997 1.75 4.41997 3.82 3.36997 8.44C2.19997 13.6 5.35997 17.97 8.21997 20.72C9.27997 21.74 10.64 22.25 12 22.25C13.36 22.25 14.72 21.74 15.77 20.72C18.63 17.97 21.79 13.61 20.62 8.45Z" fill="#BE0000"/>
                        <path d="M12 13.46C13.7397 13.46 15.15 12.0497 15.15 10.31C15.15 8.57031 13.7397 7.16 12 7.16C10.2603 7.16 8.84998 8.57031 8.84998 10.31C8.84998 12.0497 10.2603 13.46 12 13.46Z" fill="white"/>
                    </svg>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 30]
        });

        // Function to get current GeoJSON style based on theme
        function getGeoJsonStyle() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                fillColor: isDark ? '#4A4E54' : '#757575',
                fillOpacity: 1,
                color: isDark ? '#282A2C' : '#F6F7F9',
                weight: 0.7,
                opacity: 1
            };
        }

        // Load GeoJSON batas wilayah Indonesia
        fetch('/js/indonesia.json')
            .then(response => {
                if (!response.ok) throw new Error('Failed to load indonesia.json');
                return response.json();
            })
            .then(data => {
                indonesiaGeojsonLayer = L.geoJSON(data, {
                    style: getGeoJsonStyle(),
                    onEachFeature: function(feature, layer) {
                        layer.on({
                            mouseover: function(e) {
                                const isDark = document.documentElement.classList.contains('dark');
                                e.target.setStyle({
                                    fillColor: isDark ? '#5C6269' : '#636363'
                                });
                            },
                            mouseout: function(e) {
                                const isDark = document.documentElement.classList.contains('dark');
                                e.target.setStyle({
                                    fillColor: isDark ? '#4A4E54' : '#757575'
                                });
                            }
                        });
                    }
                }).addTo(map);

                indonesiaBounds = indonesiaGeojsonLayer.getBounds();

                map.fitBounds(indonesiaBounds, {
                    padding: [10, 10]
                });

                const baseMinZoom = map.getBoundsZoom(indonesiaBounds, false, [10, 10]);
                baseIndonesiaZoom = baseMinZoom;
                map.setMinZoom(baseMinZoom);
                map.setMaxZoom(12);
                map.setMaxBounds(indonesiaBounds.pad(0.25));

                renderMarkers();
            })
            .catch(error => {
                console.error('GeoJSON error:', error);
            });

        // Theme synchronization for map and popups
        window.updateMapTheme = function() {
            if (indonesiaGeojsonLayer) {
                indonesiaGeojsonLayer.setStyle(getGeoJsonStyle());
            }
            if (map) {
                map.eachLayer(layer => {
                    if (layer instanceof L.Marker && layer._assetData && layer._assetId) {
                        const popup = layer.getPopup();
                        if (popup) {
                            popup.setContent(createPopupCardHTML(layer._assetData, layer._assetId));
                        }
                    }
                });
            }
        };

        // Listen for theme toggle events
        window.addEventListener('themeChanged', function() {
            if (window.updateMapTheme) window.updateMapTheme();
        });

        // Hook observer for class changes on documentElement
        const themeObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (window.updateMapTheme) window.updateMapTheme();
                }
            });
        });
        themeObserver.observe(document.documentElement, { attributes: true });

        window.addEventListener('resize', function() {
            if (indonesiaBounds) {
                const baseMinZoom = map.getBoundsZoom(indonesiaBounds, false, [10, 10]);
                baseIndonesiaZoom = baseMinZoom;
                map.setMinZoom(baseMinZoom);
            }
        });

        // Ikon 3D untuk popup kartu aset dengan Drop Shadow persis Figma (responsif scalable)
        const ICON_CORP = `<svg class="w-full h-full text-[#0066FF]" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 18.7775V0H10.4319V4.17278H20.8639V18.7775H0ZM2.08639 16.6911H8.34555V14.6047H2.08639V16.6911ZM2.08639 12.5183H8.34555V10.4319H2.08639V12.5183ZM2.08639 8.34555H8.34555V6.25916H2.08639V8.34555ZM2.08639 4.17278H8.34555V2.08639H2.08639V4.17278ZM10.4319 16.6911H18.7775V6.25916H10.4319V16.6911ZM12.5183 10.4319V8.34555H16.6911V10.4319H12.5183ZM12.5183 14.6047V12.5183H16.6911V14.6047H12.5183Z" fill="currentColor"/></svg>`;
        const ICON_ALAMAT = `<svg class="w-full h-full" viewBox="-1 -1 24 21" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#f_d_alm)"><g filter="url(#f1_d_alm)"><path d="M4.51 3.01C4.51 2.18 5.18 1.5 6.01 1.5H18.04C18.87 1.5 19.54 2.18 19.54 3.01V11.28C19.54 12.11 18.87 12.78 18.04 12.78H6.01C5.18 12.78 4.51 12.11 4.51 11.28V3.01Z" fill="url(#p0_lin_alm)"/></g><path fill-rule="evenodd" clip-rule="evenodd" d="M10.52 4.51L13.53 5.26V16.54L10.52 15.79V10.31C10.78 10.77 11.27 11.09 11.84 11.09C12.67 11.09 13.34 10.41 13.34 9.58C13.34 8.75 12.67 8.08 11.84 8.08C11.27 8.08 10.78 8.39 10.52 8.86V4.51Z" fill="white" fill-opacity="0.4"/><path d="M7.52 5.26L4.51 4.51V15.79L7.52 16.54V5.26Z" fill="white" fill-opacity="0.4"/><path d="M13.53 5.26L16.54 4.51V15.79L13.53 16.54V5.26Z" fill="white" fill-opacity="0.4"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.52 4.51L7.52 5.26V16.54L10.52 15.79V10.31C10.4 10.1 10.34 9.85 10.34 9.58C10.34 9.32 10.4 9.07 10.52 8.86V4.51Z" fill="white" fill-opacity="0.4"/><path d="M1.5 5.26L4.51 4.51V15.79L1.5 16.54V5.26Z" fill="white" fill-opacity="0.4"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.06 14.28C12.7 13.72 14.47 11.93 14.47 9.69C14.47 8.32 13.65 6.95 11.84 6.95C10.03 6.95 9.21 8.32 9.21 9.69C9.21 11.93 10.98 13.72 11.62 14.28C11.75 14.4 11.93 14.4 12.06 14.28ZM11.84 11.09C12.67 11.09 13.34 10.41 13.34 9.58C13.34 8.75 12.67 8.08 11.84 8.08C11.01 8.08 10.34 8.75 10.34 9.58C10.34 10.41 11.01 11.09 11.84 11.09Z" fill="white" fill-opacity="0.6"/></g><defs><filter id="f_d_alm" x="-20%" y="-20%" width="150%" height="150%"><feDropShadow dx="0.75" dy="0.75" stdDeviation="0.75" flood-color="#93DF32" flood-opacity="0.4"/></filter><filter id="f1_d_alm" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="0.75"/><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.8 0"/><feBlend mode="normal" in="SourceGraphic" result="shape"/></filter><linearGradient id="p0_lin_alm" x1="12" y1="1.5" x2="12" y2="12.8" gradientUnits="userSpaceOnUse"><stop stop-color="#93DF32"/><stop offset="1" stop-color="#70BD0D"/></linearGradient></defs></svg>`;
        const ICON_LUAS = `<svg class="w-full h-full" viewBox="-1 -1 23 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#f_d_ls)"><g filter="url(#f1_d_ls)"><path d="M9.99 6.92C9.99 6.49 10.34 6.15 10.76 6.15H17.68C18.1 6.15 18.45 6.49 18.45 6.92V16.14C18.45 16.57 18.1 16.91 17.68 16.91H10.76C10.34 16.91 9.99 16.57 9.99 16.14V6.92Z" fill="url(#p0_lin_ls)"/></g><g filter="url(#f2_d_ls)"><path d="M5.38 2.31C5.38 1.88 5.72 1.54 6.15 1.54H11.53C11.95 1.54 12.3 1.88 12.3 2.31V16.14C12.3 16.57 11.95 16.91 11.53 16.91H6.15C5.72 16.91 5.38 16.57 5.38 16.14V2.31Z" fill="url(#p1_lin_ls)"/></g><path d="M1.54 5.38C1.54 4.96 1.88 4.61 2.31 4.61H7.69C8.11 4.61 8.46 4.96 8.46 5.38V19.22C8.46 19.64 8.11 19.99 7.69 19.99H2.31C1.88 19.99 1.54 19.64 1.54 19.22V5.38Z" fill="white" fill-opacity="0.4"/></g><defs><filter id="f_d_ls" x="-20%" y="-20%" width="150%" height="150%"><feDropShadow dx="0.75" dy="0.75" stdDeviation="0.75" flood-color="#31C8D2" flood-opacity="0.4"/></filter><filter id="f1_d_ls" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="0.75"/><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.8 0"/><feBlend mode="normal" in="SourceGraphic" result="shape"/></filter><filter id="f2_d_ls" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="0.75"/><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.8 0"/><feBlend mode="normal" in="SourceGraphic" result="shape"/></filter><linearGradient id="p0_lin_ls" x1="14.2" y1="6.1" x2="14.2" y2="16.9" gradientUnits="userSpaceOnUse"><stop stop-color="#31C8D2"/><stop offset="1" stop-color="#1AA6E1"/></linearGradient><linearGradient id="p1_lin_ls" x1="8.8" y1="1.5" x2="8.8" y2="16.9" gradientUnits="userSpaceOnUse"><stop stop-color="#31C8D2"/><stop offset="1" stop-color="#1AA6E1"/></linearGradient></defs></svg>`;
        const ICON_JENIS = `<svg class="w-full h-full" viewBox="-1 -1 24 23" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#f_d_jns)"><g filter="url(#f1_d_jns)"><path d="M4.51 3.01C4.51 2.18 5.18 1.5 6.01 1.5H18.04C18.87 1.5 19.54 2.18 19.54 3.01V11.28C19.54 12.11 18.87 12.78 18.04 12.78H6.01C5.18 12.78 4.51 12.11 4.51 11.28V3.01Z" fill="url(#p0_lin_jns)"/></g><path d="M1.5 5.26C1.5 4.85 1.84 4.51 2.25 4.51H15.79C16.2 4.51 16.54 4.85 16.54 5.26V6.77C16.54 7.18 16.2 7.52 15.79 7.52H2.25C1.84 7.52 1.5 7.18 1.5 6.77V5.26Z" fill="white" fill-opacity="0.4"/><path d="M1.5 8.64C1.5 8.44 1.67 8.27 1.88 8.27H14.66C14.87 8.27 15.03 8.44 15.03 8.64V10.15C15.03 10.36 14.87 10.52 14.66 10.52H1.88C1.67 10.52 1.5 10.36 1.5 10.15V8.64Z" fill="white" fill-opacity="0.4"/><path d="M1.5 11.65C1.5 11.44 1.67 11.28 1.88 11.28H8.64C8.85 11.28 9.02 11.44 9.02 11.65V12.4C9.02 12.61 8.85 12.78 8.64 12.78H1.88C1.67 12.78 1.5 12.61 1.5 12.4V11.65Z" fill="white" fill-opacity="0.4"/><path d="M11.22 16.68C12.1 18.11 13.71 18.11 14.63 18C16.48 17.78 17.78 16.73 17.41 13.23C17.31 12.26 16.57 11.86 15.69 12.45C15.36 11.51 13.75 11.78 13.84 12.67L13.64 10.73C13.56 10 13.16 9.31 12.33 9.41C11.5 9.5 11.5 10.49 11.6 11.41L11.96 14.87L11.17 13.98C10.91 13.76 10.24 13.15 9.7 13.41C9.17 13.67 9.08 14.23 9.37 14.68C9.65 15.14 10.65 15.76 11.22 16.68Z" fill="white" fill-opacity="0.6"/></g><defs><filter id="f_d_jns" x="-20%" y="-20%" width="150%" height="150%"><feDropShadow dx="0.75" dy="0.75" stdDeviation="0.75" flood-color="#9D68F3" flood-opacity="0.4"/></filter><filter id="f1_d_jns" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="0.75"/><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.8 0"/><feBlend mode="normal" in="SourceGraphic" result="shape"/></filter><linearGradient id="p0_lin_jns" x1="12" y1="1.5" x2="12" y2="12.8" gradientUnits="userSpaceOnUse"><stop stop-color="#6966FF"/><stop offset="1" stop-color="#9D68F3"/></linearGradient></defs></svg>`;
        const ICON_NILAI = `<svg class="w-full h-full" viewBox="-1 -1 23 20" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#f_d_nl)"><g filter="url(#f1_d_nl)"><path d="M4.2 2.8C4.2 2.03 4.83 1.4 5.6 1.4H16.8C17.57 1.4 18.2 2.03 18.2 2.8V9.1C18.2 9.87 17.57 10.5 16.8 10.5H5.6C4.83 10.5 4.2 9.87 4.2 9.1V2.8Z" fill="url(#p0_lin_nl)"/></g><path d="M3.85 4.25C4.05 3.5 4.82 3.06 5.57 3.26L16.39 6.15C17.13 6.35 17.58 7.12 17.38 7.87L15.75 13.96C15.55 14.7 14.78 15.15 14.03 14.95L3.21 12.05C2.47 11.85 2.02 11.08 2.22 10.33L3.85 4.25Z" fill="white" fill-opacity="0.4"/><path fill-rule="evenodd" clip-rule="evenodd" d="M1.4 7.35C1.4 6.58 2.03 5.95 2.8 5.95H14C14.77 5.95 15.4 6.58 15.4 7.35V13.65C15.4 14.42 14.77 15.05 14 15.05H2.8C2.03 15.05 1.4 14.42 1.4 13.65V7.35ZM10.5 10.5C10.5 12.24 9.56 13.65 8.4 13.65C7.24 13.65 6.3 12.24 6.3 10.5C6.3 8.76 7.24 7.35 8.4 7.35C9.56 7.35 10.5 8.76 10.5 10.5ZM3.85 7.7C3.85 8.09 3.54 8.4 3.15 8.4C2.76 8.4 2.45 8.09 2.45 7.7C2.45 7.31 2.76 7 3.15 7C3.54 7 3.85 7.31 3.85 7.7ZM14.35 7.7C14.35 8.09 14.04 8.4 13.65 8.4C13.26 8.4 12.95 8.09 12.95 7.7C12.95 7.31 13.26 7 13.65 7C14.04 7 14.35 7.31 14.35 7.7ZM3.15 14C3.54 14 3.85 13.69 3.85 13.3C3.85 12.91 3.54 12.6 3.15 12.6C2.76 12.6 2.45 12.91 2.45 13.3C2.45 13.69 2.76 14 3.15 14ZM14.35 13.3C14.35 13.69 14.04 14 13.65 14C13.26 14 12.95 13.69 12.95 13.3C12.95 12.91 13.26 12.6 13.65 12.6C14.04 12.6 14.35 12.91 14.35 13.3Z" fill="white" fill-opacity="0.4"/></g><defs><filter id="f_d_nl" x="-20%" y="-20%" width="150%" height="150%"><feDropShadow dx="0.75" dy="0.75" stdDeviation="0.75" flood-color="#21EB66" flood-opacity="0.4"/></filter><filter id="f1_d_nl" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="0.75"/><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.8 0"/><feBlend mode="normal" in="SourceGraphic" result="shape"/></filter><linearGradient id="p0_lin_nl" x1="11.2" y1="1.4" x2="11.2" y2="10.5" gradientUnits="userSpaceOnUse"><stop stop-color="#21EB66"/><stop offset="1" stop-color="#12D583"/></linearGradient></defs></svg>`;
        const ICON_PERIODE = `<svg class="w-full h-full" viewBox="-1 -1 25 22" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#f_d_prd)"><g filter="url(#f1_d_prd)"><path d="M4.57 3.05C4.57 2.21 5.25 1.52 6.09 1.52H18.28C19.12 1.52 19.81 2.21 19.81 3.05V11.43C19.81 12.27 19.12 12.95 18.28 12.95H6.09C5.25 12.95 4.57 12.27 4.57 11.43V3.05Z" fill="url(#p0_lin_prd)"/></g><path d="M1.52 6.09V6.86H16.76V6.09C16.76 5.25 16.08 4.57 15.24 4.57H3.05C2.21 4.57 1.52 5.25 1.52 6.09Z" fill="white" fill-opacity="0.4"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.76 7.62V15.24C16.76 16.08 16.08 16.76 15.24 16.76H3.05C2.21 16.76 1.52 16.08 1.52 15.24V7.62H16.76ZM13.71 10.67C14.13 10.67 14.47 10.32 14.47 9.9C14.47 9.48 14.13 9.14 13.71 9.14C13.29 9.14 12.95 9.48 12.95 9.9C12.95 10.32 13.29 10.67 13.71 10.67Z" fill="white" fill-opacity="0.4"/></g><defs><filter id="f_d_prd" x="-20%" y="-20%" width="150%" height="150%"><feDropShadow dx="0.75" dy="0.75" stdDeviation="0.75" flood-color="#FFA800" flood-opacity="0.4"/></filter><filter id="f1_d_prd" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="0.75"/><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.8 0"/><feBlend mode="normal" in="SourceGraphic" result="shape"/></filter><linearGradient id="p0_lin_prd" x1="12.2" y1="1.5" x2="12.2" y2="12.9" gradientUnits="userSpaceOnUse"><stop stop-color="#FFA800"/><stop offset="1" stop-color="#FF7A00"/></linearGradient></defs></svg>`;

        // Template HTML untuk kartu popup aset (Responsive: compact di mobile, proporsional di desktop)
        function createPopupCardHTML(asset, id) {
            const googleMapsUrl = (asset.latitude && asset.longitude) 
                ? `https://www.google.com/maps?q=${asset.latitude},${asset.longitude}` 
                : '#';  

            return `
                <div class="w-[260px] sm:w-[360px] rounded-2xl sm:rounded-3xl bg-white dark:bg-[#1F2123] p-3 sm:p-5 shadow-[0_16px_40px_rgba(0,0,0,0.16)] dark:shadow-[0_16px_40px_rgba(0,0,0,0.7)] border border-gray-100/90 dark:border-white/10 font-sans text-left relative">
                    <div class="mb-2 sm:mb-3.5">
                        <div class="flex items-center gap-2 sm:gap-2.5">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 shrink-0 text-[#0066FF] flex items-center justify-center">
                                ${ICON_CORP}
                            </div>
                            <h3 class="m-0 text-xs sm:text-base font-bold leading-tight text-gray-950 dark:text-white truncate">${asset.name || '-'}</h3>
                        </div>
                        <p class="mt-0.5 sm:mt-1 text-[9.5px] sm:text-xs font-medium text-gray-400 dark:text-[#9AA0A6] pl-7 sm:pl-8.5 truncate">${(asset.code ? asset.code + ' • ' : '') + (asset.location || '')}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-x-2 sm:gap-x-4 gap-y-2 sm:gap-y-3 pt-1.5 sm:pt-2.5 border-t sm:border-0 border-gray-100 dark:border-white/10">
                        <div class="flex items-center gap-1.5 sm:gap-2.5 min-w-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center">${ICON_ALAMAT}</div>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[9.5px] sm:text-xs font-semibold text-gray-900 dark:text-[#9AA0A6] leading-tight">Alamat</span>
                                <span class="block text-[9.5px] sm:text-xs text-gray-500 dark:text-white mt-0.5 leading-tight truncate">${asset.address || '-'}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 sm:gap-2.5 min-w-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center">${ICON_LUAS}</div>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[9.5px] sm:text-xs font-semibold text-gray-900 dark:text-[#9AA0A6] leading-tight">Luas</span>
                                <span class="block text-[9.5px] sm:text-xs text-gray-500 dark:text-white mt-0.5 leading-tight truncate">${asset.area || '-'}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 sm:gap-2.5 min-w-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center">${ICON_JENIS}</div>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[9.5px] sm:text-xs font-semibold text-gray-900 dark:text-[#9AA0A6] leading-tight">Jenis Aset</span>
                                <span class="block text-[9.5px] sm:text-xs text-gray-500 dark:text-white mt-0.5 leading-tight truncate">${asset.type || '-'}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 sm:gap-2.5 min-w-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center">${ICON_NILAI}</div>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[9.5px] sm:text-xs font-semibold text-gray-900 dark:text-[#9AA0A6] leading-tight">Nilai Aset</span>
                                <span class="block text-[9.5px] sm:text-xs text-gray-500 dark:text-white mt-0.5 leading-tight truncate">${asset.value || '-'}</span>
                            </div>
                        </div>

                        <div class="col-span-2 flex items-center gap-1.5 sm:gap-2.5 min-w-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center">${ICON_PERIODE}</div>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[9.5px] sm:text-xs font-semibold text-gray-900 dark:text-[#9AA0A6] leading-tight">Periode</span>
                                <span class="block text-[9.5px] sm:text-xs text-gray-500 dark:text-white mt-0.5 leading-tight truncate">${asset.period || '-'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2.5 sm:mt-4 flex items-center gap-2">
                        <a href="/asset/${id}" class="btn-detail-lanjutan flex-1 h-[34px] sm:h-[38px] flex items-center justify-center text-center cursor-pointer rounded-xl bg-[#0066FF] px-2.5 sm:px-4 text-[11px] sm:text-xs font-semibold !text-white transition hover:bg-blue-700 shadow-xs whitespace-nowrap" style="color: #ffffff !important;">Detail Lanjutan</a>
                        <a href="${googleMapsUrl}" target="_blank" class="btn-buka-maps h-[34px] sm:h-[38px] flex items-center justify-center gap-1.5 text-center cursor-pointer rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#2D3034] px-2.5 sm:px-3 text-[11px] sm:text-xs font-medium text-gray-700 dark:text-white transition hover:bg-gray-50 dark:hover:bg-white/10 shrink-0 whitespace-nowrap shadow-xs">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-600 dark:text-white shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 9.00002V15C22 17.5 21.5 19.25 20.38 20.38L14 14L21.73 6.27002C21.91 7.06002 22 7.96002 22 9.00002Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21.73 6.27L6.26999 21.73C3.25999 21.04 2 18.96 2 15V9C2 4 4 2 9 2H15C18.96 2 21.04 3.26 21.73 6.27Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20.38 20.38C19.25 21.5 17.5 22 15 22H9.00003C7.96003 22 7.06002 21.91 6.27002 21.73L14 14L20.38 20.38Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path opacity="0.4" d="M6.24002 7.97997C6.92002 5.04997 11.32 5.04997 12 7.97997C12.39 9.69997 11.31 11.16 10.36 12.06C9.67001 12.72 8.58003 12.72 7.88003 12.06C6.93003 11.16 5.84002 9.69997 6.24002 7.97997Z" stroke="currentColor" stroke-width="1.5"/>
                                <path opacity="0.4" d="M9.0946 8.69995H9.10359" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="whitespace-nowrap">Buka Maps</span>
                        </a>
                    </div>
                </div>
            `;
        }

        // Render marker aset dan popup info
        function renderMarkers() {
            Object.entries(assets).forEach(([id, asset]) => {
                const lat = parseFloat(asset.latitude);
                const lng = parseFloat(asset.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const marker = L.marker([lat, lng], { icon: redPinIcon }).addTo(map);
                    marker._assetData = asset;
                    marker._assetId = id;

                    const popup = L.popup({
                        offset: [0, -16],
                        closeButton: false,
                        autoPan: false,
                        className: 'custom-asset-leaflet-popup'
                    }).setContent(createPopupCardHTML(asset, id));

                    marker.bindPopup(popup);

                    marker.on('click', function(e) {
                        L.DomEvent.stopPropagation(e);

                        const baseZoom = baseIndonesiaZoom || map.getMinZoom() || 5;
                        const pointZoomLevel = baseZoom + 1.2;

                        const projected = map.project([lat, lng], pointZoomLevel);
                        // On mobile: subtract 105px so popup attached to pin is vertically centered
                        const yOffset = isMobile() ? 105 : 150;
                        const targetCenter = map.unproject(projected.subtract([0, yOffset]), pointZoomLevel);

                        map.flyTo(targetCenter, pointZoomLevel, {
                            duration: 0.55
                        });

                        marker.openPopup();
                    });
                }
            });
        }


        map.on('popupclose', function() {
            fitIndonesiaBounds();
        });

        map.on('click', function() {
            closeFilterModal();
        });

        // Kontrol filter panel & button animation
        const filterButton = document.getElementById('filterButton');
        const filterButtonDesktop = document.getElementById('filterButtonDesktop');
        const filterModal = document.getElementById('filterModal');
        const filterOverlay = document.getElementById('filterOverlay');
        const applyFilter = document.getElementById('applyFilter');
        const resetFilter = document.getElementById('resetFilter');

        let isFilterOpen = false;

        function isMobile() {
            return window.innerWidth < 1024;
        }

        function alignFilterModal() {
            if (!isMobile() && filterButtonDesktop && filterModal) {
                const rect = filterButtonDesktop.getBoundingClientRect();
                filterModal.style.top = rect.top + 'px';
            } else if (isMobile() && filterButton && filterModal) {
                const rect = filterButton.getBoundingClientRect();
                filterModal.style.top = rect.top + 'px';
            }
        }

        window.addEventListener('resize', () => {
            if (isFilterOpen) {
                alignFilterModal();
            }
        });

        function openFilter() {
            isFilterOpen = true;
            alignFilterModal();

            // Slide in from right on both mobile and desktop
            filterModal.classList.remove('translate-x-[140%]', 'opacity-0', 'pointer-events-none');
            filterModal.classList.add('translate-x-0', 'opacity-100', 'pointer-events-auto');

            // Shift filter button to the left
            if (isMobile() && filterButton) {
                const shiftWidth = (filterModal.offsetWidth || 295) + 12;
                filterButton.style.transform = 'translateX(-' + shiftWidth + 'px)';
            } else if (!isMobile() && filterButtonDesktop) {
                const shiftWidth = (filterModal.offsetWidth || 373) + 12;
                filterButtonDesktop.style.transform = 'translateX(-' + shiftWidth + 'px)';
            }

            // Show overlay
            filterOverlay.classList.remove('invisible', 'opacity-0');
            filterOverlay.classList.add('visible', 'opacity-100');
        }

        function closeFilterModal() {
            isFilterOpen = false;

            // Slide back right on both mobile and desktop
            filterModal.classList.remove('translate-x-0', 'opacity-100', 'pointer-events-auto');
            filterModal.classList.add('translate-x-[140%]', 'opacity-0', 'pointer-events-none');

            if (filterButton) {
                filterButton.style.transform = 'translateX(0)';
            }
            if (filterButtonDesktop) {
                filterButtonDesktop.style.transform = 'translateX(0)';
            }

            // Hide overlay
            filterOverlay.classList.remove('visible', 'opacity-100');
            filterOverlay.classList.add('invisible', 'opacity-0');

            // Close all dropdown menus
            document.querySelectorAll('.filter-dropdown-menu').forEach(m => m.classList.add('hidden'));
        }

        function toggleFilter() {
            if (isFilterOpen) {
                closeFilterModal();
            } else {
                openFilter();
            }
        }

        // ================= CUSTOM MAP FILTER DROPDOWN LOGIC =================
        window.selectMapFilter = function(filterId, value, labelText) {
            const input = document.getElementById(filterId);
            if (input) {
                input.value = value;
            }

            const container = input ? input.closest('.custom-filter-container') : null;
            if (container) {
                const labelSpan = container.querySelector('.filter-selected-label');
                if (labelSpan) {
                    labelSpan.textContent = labelText;
                    if (value && value !== '') {
                        labelSpan.classList.remove('text-[#8B8B8B]');
                        labelSpan.classList.add('text-black');
                    } else {
                        labelSpan.classList.remove('text-black');
                        labelSpan.classList.add('text-[#8B8B8B]');
                    }
                }

                // Update active state in menu
                const options = container.querySelectorAll('.filter-option-btn');
                options.forEach(btn => {
                    if (btn.querySelector('span')?.textContent.trim() === labelText.trim()) {
                        btn.className = "filter-option-btn flex items-center justify-between w-full px-3 py-2 text-xs sm:text-[13px] font-medium bg-blue-50 text-[#0066FF] rounded-xl transition text-left cursor-pointer";
                    } else {
                        btn.className = "filter-option-btn flex items-center justify-between w-full px-3 py-2 text-xs sm:text-[13px] font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition text-left cursor-pointer";
                    }
                });

                const menu = container.querySelector('.filter-dropdown-menu');
                if (menu) {
                    menu.classList.add('hidden');
                }
            }
        };

        document.addEventListener('click', function(e) {
            const filterBtn = e.target.closest('.filter-dropdown-btn');
            const allFilterMenus = document.querySelectorAll('.filter-dropdown-menu');

            if (filterBtn) {
                e.stopPropagation();
                const container = filterBtn.closest('.custom-filter-container');
                const menu = container ? container.querySelector('.filter-dropdown-menu') : null;
                const wasHidden = menu ? menu.classList.contains('hidden') : true;

                // Close all other dropdown menus
                allFilterMenus.forEach(m => m.classList.add('hidden'));

                if (wasHidden && menu) {
                    menu.classList.remove('hidden');
                }
            } else if (!e.target.closest('.filter-dropdown-menu')) {
                allFilterMenus.forEach(m => m.classList.add('hidden'));
            }
        });

        // Wire up both mobile and desktop filter buttons
        if (filterButton) {
            filterButton.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleFilter();
            });
        }
        if (filterButtonDesktop) {
            filterButtonDesktop.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleFilter();
            });
        }

        filterOverlay.addEventListener('click', function() {
            closeFilterModal();
        });

        applyFilter.addEventListener('click', function() {
            closeFilterModal();
        });

        resetFilter.addEventListener('click', function() {
            selectMapFilter('stasiun', '', 'Semua Stasiun');
            selectMapFilter('wilayah', '', 'Row & Non Row');
            selectMapFilter('aset', '', 'Semua Aset');
            selectMapFilter('jenis_kontrak', '', 'Semua Kontrak');
            selectMapFilter('jenis_pendapatan', '', 'Semua Pend...');
            selectMapFilter('spv', '', 'Semua SPV');
            closeFilterModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFilterModal();
                map.closePopup();
            }
        });
    </script>
</body>
</html>