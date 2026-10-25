<?php

namespace App\Http\Controllers;

use App\Models\KaiContract;
use App\Models\PaymentRecord;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentRecord::with(['contract.tenant', 'contract.asset'])
            ->orderBy('nomor_kontrak', 'asc')
            ->orderBy('termin_ke', 'asc');

        // Search: nomor kontrak, nama mitra, invoice, VA, transaction_id
        if ($request->filled('search')) {
            $s = trim($request->search);
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
        $totalTagihan = (float) PaymentRecord::sum('total_bayar');
        $totalTagihanFmt = 'Rp '.number_format($totalTagihan, 0, ',', '.');

        // Dropdown options
        $kelompokOptions = Cache::remember('dropdown_kelompok', 3600, fn () => KaiContract::select('kelompok')->distinct()
            ->whereNotNull('kelompok')->where('kelompok', '!=', '')
            ->pluck('kelompok')->toArray()
        );
        $statusMitraOptions = Cache::remember('dropdown_status_mitra', 3600, fn () => Tenant::select('status_mitra')->distinct()
            ->whereNotNull('status_mitra')->where('status_mitra', '!=', '')
            ->pluck('status_mitra')->toArray()
        );

        return view('payments.index', compact(
            'payments', 'kelompokOptions', 'statusMitraOptions',
            'totalTagihanFmt', 'totalTagihan'
        ));
    }

    /**
     * Tampilkan form pembuatan tagihan baru (Hanya Super Admin / Admin).
     */
    public function create()
    {
        $contracts = KaiContract::select('nomor_kontrak')
            ->orderBy('nomor_kontrak')
            ->get();

        return view('payments.create', compact('contracts'));
    }

    /**
     * Simpan tagihan baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kontrak' => 'required|string|exists:contracts,nomor_kontrak',
            'termin_ke' => 'required|integer|min:1',
            'virtual_account' => 'nullable|string|max:100',
            'tanggal_termin' => 'nullable|date',
            'total_bayar' => 'required|numeric|min:0',
            'invoice_id' => 'nullable|string|max:150',
            'transaction_id' => 'nullable|string|max:150',
        ], [
            'nomor_kontrak.required' => 'Nomor Kontrak wajib dipilih.',
            'nomor_kontrak.exists' => 'Nomor Kontrak tidak ditemukan dalam database.',
            'termin_ke.required' => 'Termin ke wajib diisi.',
            'total_bayar.required' => 'Total bayar / tagihan wajib diisi.',
        ]);

        PaymentRecord::create([
            'nomor_kontrak' => trim($validated['nomor_kontrak']),
            'termin_ke' => (int) $validated['termin_ke'],
            'virtual_account' => $validated['virtual_account'] ?? null,
            'tanggal_termin' => $validated['tanggal_termin'] ?? null,
            'total_bayar' => $validated['total_bayar'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'transaction_id' => $validated['transaction_id'] ?? null,
            'created_at' => now(),
        ]);

        DashboardController::forgetCache();

        return redirect()->route('payments.index')->with('success', 'Data tagihan baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form pengeditan data tagihan (Hanya Super Admin / Admin).
     */
    public function edit(int $payment)
    {
        $paymentRecord = PaymentRecord::with(['contract.tenant', 'contract.asset'])->findOrFail($payment);

        $contracts = KaiContract::select('nomor_kontrak')
            ->orderBy('nomor_kontrak')
            ->get();

        return view('payments.edit', compact('paymentRecord', 'contracts'));
    }

    /**
     * Update data tagihan di database.
     */
    public function update(Request $request, int $payment)
    {
        $paymentRecord = PaymentRecord::findOrFail($payment);

        $validated = $request->validate([
            'nomor_kontrak' => 'required|string|exists:contracts,nomor_kontrak',
            'termin_ke' => 'required|integer|min:1',
            'virtual_account' => 'nullable|string|max:100',
            'tanggal_termin' => 'nullable|date',
            'total_bayar' => 'required|numeric|min:0',
            'invoice_id' => 'nullable|string|max:150',
            'transaction_id' => 'nullable|string|max:150',
        ], [
            'nomor_kontrak.required' => 'Nomor Kontrak wajib dipilih.',
            'nomor_kontrak.exists' => 'Nomor Kontrak tidak ditemukan dalam database.',
            'termin_ke.required' => 'Termin ke wajib diisi.',
            'total_bayar.required' => 'Total bayar wajib diisi.',
        ]);

        $paymentRecord->update([
            'nomor_kontrak' => trim($validated['nomor_kontrak']),
            'termin_ke' => (int) $validated['termin_ke'],
            'virtual_account' => $validated['virtual_account'] ?? null,
            'tanggal_termin' => $validated['tanggal_termin'] ?? null,
            'total_bayar' => $validated['total_bayar'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'transaction_id' => $validated['transaction_id'] ?? null,
        ]);

        DashboardController::forgetCache();

        return redirect()->route('payments.index')->with('success', "Data tagihan termin {$paymentRecord->termin_ke} ({$paymentRecord->nomor_kontrak}) berhasil diperbarui.");
    }

    /**
     * Hapus data tagihan (Hanya Super Admin / Admin).
     */
    public function destroy(int $payment)
    {
        $paymentRecord = PaymentRecord::findOrFail($payment);
        $nomorKontrak = $paymentRecord->nomor_kontrak;
        $termin = $paymentRecord->termin_ke;

        $paymentRecord->delete();

        DashboardController::forgetCache();

        return redirect()->route('payments.index')->with('success', "Data tagihan termin {$termin} ({$nomorKontrak}) berhasil dihapus.");
    }
}
