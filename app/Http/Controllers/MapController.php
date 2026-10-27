<?php

namespace App\Http\Controllers;

use App\Models\KaiAsset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    public function index(Request $request)
    {
        // Cache data peta selama 30 menit agar cepat saat panning/zooming
        $assetsForMap = Cache::remember('map_assets', 1800, function () {
            return KaiAsset::with(['contract.tenant'])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->keyBy('nomor_aset')
                ->map(function ($asset) {
                    $contract = $asset->contract;
                    $tenant = $contract?->tenant;

                    $startFmt = $contract?->masa_awal ? Carbon::parse($contract->masa_awal)->format('d/m/Y') : null;
                    $endFmt = $contract?->masa_akhir ? Carbon::parse($contract->masa_akhir)->format('d/m/Y') : null;
                    $period = ($startFmt && $endFmt) ? "{$startFmt} – {$endFmt}" : ($startFmt ?? $endFmt ?? '-');

                    $isPotensial = $asset->status_potensi === 'potensial' || ! $contract;
                    $status = $isPotensial ? 'potensial' : 'terkontrak';

                    return [
                        'code' => $asset->nomor_aset,
                        'name' => $asset->blok_aset ?: ('Aset '.$asset->nomor_aset),
                        'stasiun' => $asset->stasiun ?: 'Daop 4 Semarang',
                        'location' => $asset->stasiun ?: 'Semarang',
                        'address' => $asset->wilayah_asset ?: 'Daop 4 Semarang',
                        'area' => $asset->luas_tanah_formatted ?: (($asset->luas_tanah ?? 0).' m²'),
                        'type' => ($asset->jenis_aset ?? 'Tanah').($asset->peruntukan ? (' — '.$asset->peruntukan) : ''),
                        'value' => $contract ? ('Rp '.number_format((float) $contract->nilai_kontrak, 0, ',', '.')) : '-',
                        'period' => $period,
                        'tenant' => $tenant?->nama_mitra ?? '-',
                        'status' => $status, // 'potensial' (Hijau) atau 'terkontrak' (Merah)
                        'status_label' => $isPotensial ? 'Potensial (Kosong)' : 'Terkontrak (Terisi)',
                        'status_color' => $isPotensial ? '#10B981' : '#EF4444',
                        'latitude' => (string) $asset->latitude,
                        'longitude' => (string) $asset->longitude,
                    ];
                })
                ->toArray();
        });

        // Daftar Stasiun Sampling di Daop 4
        $stations = KaiAsset::whereNotNull('stasiun')
            ->distinct()
            ->orderBy('stasiun')
            ->pluck('stasiun')
            ->toArray();

        if (empty($stations)) {
            $stations = ['Semarang Poncol', 'Semarang Tawang', 'Pekalongan', 'Tegal', 'Batang', 'Weleri', 'Cepu', 'Ngrombo'];
        }

        $totalPotensial = count(array_filter($assetsForMap, fn ($a) => $a['status'] === 'potensial'));
        $totalTerkontrak = count(array_filter($assetsForMap, fn ($a) => $a['status'] === 'terkontrak'));

        return view('map.index', [
            'assets' => $assetsForMap,
            'stations' => $stations,
            'totalPotensial' => $totalPotensial,
            'totalTerkontrak' => $totalTerkontrak,
        ]);
    }

    /**
     * Update manual status aset (Divisi Aset & Superadmin)
     */
    public function updateStatus(Request $request, string $nomorAset)
    {
        $user = auth()->user();
        if (! $user || (! $user->isAsset() && ! $user->isSuperAdmin() && ! $user->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Divisi Aset atau Super Admin yang memiliki hak mengubah status aset.',
            ], 403);
        }

        $asset = KaiAsset::where('nomor_aset', $nomorAset)->firstOrFail();

        $validated = $request->validate([
            'status_potensi' => 'required|in:potensial,terkontrak',
            'stasiun' => 'nullable|string|max:100',
            'peruntukan' => 'nullable|string|max:255',
            'luas_tanah' => 'nullable|numeric|min:0',
        ]);

        $asset->update($validated);

        // Reset cache peta & dashboard
        Cache::forget('map_assets');
        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_crowl_metrics');

        return response()->json([
            'success' => true,
            'message' => 'Status aset berhasil diperbarui menjadi '.($asset->status_potensi === 'potensial' ? 'Potensial (Hijau)' : 'Terkontrak (Merah)').'!',
            'asset' => $asset,
        ]);
    }
}
