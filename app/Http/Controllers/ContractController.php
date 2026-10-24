<?php

namespace App\Http\Controllers;

use App\Models\KaiContract;
use App\Models\KaiAsset;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = KaiContract::with(['tenant', 'asset'])->latest('created_at');

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_kontrak', 'like', "%{$s}%")
                  ->orWhere('nomor_aset', 'like', "%{$s}%")
                  ->orWhereHas('tenant', fn ($q2) => $q2->where('nama_mitra', 'like', "%{$s}%"))
                  ->orWhereHas('asset', fn ($q2) => $q2->where('blok_aset', 'like', "%{$s}%"));
            });
        }

        // Filter kelompok (Non Row / Row / dll)
        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        // Filter status mitra
        if ($request->filled('status_mitra')) {
            $query->whereHas('tenant', fn ($q) => $q->where('status_mitra', $request->status_mitra));
        }

        // Filter status kontrak (Aktif / Backlog / tanpa_tanggal)
        if ($request->filled('status_kontrak')) {
            $today = now()->toDateString();
            match ($request->status_kontrak) {
                'aktif'         => $query->whereNotNull('masa_akhir')->whereDate('masa_akhir', '>=', $today),
                'backlog'       => $query->whereNotNull('masa_akhir')->whereDate('masa_akhir', '<', $today),
                'tanpa_tanggal' => $query->whereNull('masa_akhir'),
                default         => null,
            };
        }

        $contracts = $query->paginate(50)->withQueryString();

        // Dropdown options
        $kelompokOptions = Cache::remember('dropdown_kelompok', 3600, fn () =>
            KaiContract::select('kelompok')->distinct()
                ->whereNotNull('kelompok')->where('kelompok', '!=', '')
                ->pluck('kelompok')->toArray()
        );
        $statusMitraOptions = Cache::remember('dropdown_status_mitra', 3600, fn () =>
            Tenant::select('status_mitra')->distinct()
                ->whereNotNull('status_mitra')->where('status_mitra', '!=', '')
                ->pluck('status_mitra')->toArray()
        );

        return view('contracts.index', compact('contracts', 'kelompokOptions', 'statusMitraOptions'));
    }

    public static function forgetCache(): void
    {
        Cache::forget('dropdown_kelompok');
        Cache::forget('dropdown_status_mitra');
        DashboardController::forgetCache();
    }
}
