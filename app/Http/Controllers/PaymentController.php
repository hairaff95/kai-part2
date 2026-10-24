<?php

namespace App\Http\Controllers;

use App\Models\PaymentRecord;
use App\Models\KaiContract;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentRecord::with(['contract.tenant', 'contract.asset'])->latest('created_at');

        // Search: nomor kontrak, nama mitra, invoice, VA
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_kontrak', 'like', "%{$s}%")
                  ->orWhere('invoice_id', 'like', "%{$s}%")
                  ->orWhere('transaction_id', 'like', "%{$s}%")
                  ->orWhere('virtual_account', 'like', "%{$s}%")
                  ->orWhereHas('contract.tenant', fn ($q2) => $q2->where('nama_mitra', 'like', "%{$s}%"));
            });
        }

        // Filter kelompok kontrak
        if ($request->filled('kelompok')) {
            $query->whereHas('contract', fn ($q) => $q->where('kelompok', $request->kelompok));
        }

        // Filter status mitra
        if ($request->filled('status_mitra')) {
            $query->whereHas('contract.tenant', fn ($q) => $q->where('status_mitra', $request->status_mitra));
        }

        // Filter termin ke
        if ($request->filled('termin_ke') && is_numeric($request->termin_ke)) {
            $query->where('termin_ke', (int) $request->termin_ke);
        }

        $payments = $query->paginate(50)->withQueryString();

        // Ringkasan statistik
        $totalTagihan    = (float) PaymentRecord::sum('total_bayar');
        $totalTagihanFmt = 'Rp ' . number_format($totalTagihan, 0, ',', '.');

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

        return view('payments.index', compact(
            'payments', 'kelompokOptions', 'statusMitraOptions',
            'totalTagihanFmt', 'totalTagihan'
        ));
    }
}
