<?php

namespace App\Http\Controllers;

use App\Models\KaiAsset;
use App\Models\KaiContract;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = KaiContract::with(['tenant', 'asset'])->latest('created_at');

        // Search (Nomor Kontrak, Nomor Aset, Nama Mitra, Blok Aset, Transaction ID, Invoice ID, VA)
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nomor_kontrak', 'like', "%{$s}%")
                    ->orWhere('nomor_aset', 'like', "%{$s}%")
                    ->orWhereHas('tenant', fn ($q2) => $q2->where('nama_mitra', 'like', "%{$s}%"))
                    ->orWhereHas('asset', fn ($q2) => $q2->where('blok_aset', 'like', "%{$s}%"))
                    ->orWhereHas('payments', fn ($q3) => $q3->where('transaction_id', 'like', "%{$s}%")
                        ->orWhere('invoice_id', 'like', "%{$s}%")
                        ->orWhere('virtual_account', 'like', "%{$s}%")
                    );
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
                'aktif' => $query->whereNotNull('masa_akhir')->whereDate('masa_akhir', '>=', $today),
                'backlog' => $query->whereNotNull('masa_akhir')->whereDate('masa_akhir', '<', $today),
                'tanpa_tanggal' => $query->whereNull('masa_akhir'),
                default => null,
            };
        }

        $contracts = $query->paginate(50)->withQueryString();

        // Dropdown options
        $kelompokOptions = Cache::remember('dropdown_kelompok', 3600, fn () => KaiContract::select('kelompok')->distinct()
            ->whereNotNull('kelompok')->where('kelompok', '!=', '')
            ->pluck('kelompok')->toArray()
        );
        $statusMitraOptions = Cache::remember('dropdown_status_mitra', 3600, fn () => Tenant::select('status_mitra')->distinct()
            ->whereNotNull('status_mitra')->where('status_mitra', '!=', '')
            ->pluck('status_mitra')->toArray()
        );

        return view('contracts.index', compact('contracts', 'kelompokOptions', 'statusMitraOptions'));
    }

    /**
     * Tampilkan form pembuatan kontrak baru (Hanya Super Admin / Admin).
     */
    public function create()
    {
        $kelompokOptions = Cache::remember('dropdown_kelompok', 3600, fn () => KaiContract::select('kelompok')->distinct()
            ->whereNotNull('kelompok')->where('kelompok', '!=', '')
            ->pluck('kelompok')->toArray()
        );
        if (empty($kelompokOptions)) {
            $kelompokOptions = ['Non Row', 'Row'];
        }

        $statusMitraOptions = Cache::remember('dropdown_status_mitra', 3600, fn () => Tenant::select('status_mitra')->distinct()
            ->whereNotNull('status_mitra')->where('status_mitra', '!=', '')
            ->pluck('status_mitra')->toArray()
        );
        if (empty($statusMitraOptions)) {
            $statusMitraOptions = ['Swasta', 'Perorangan', 'BUMN', 'Pegawai Aktif', 'Pensiunan'];
        }

        $jenisAsetOptions = ['Tanah', 'Bangunan', 'Tanah & Bangunan', 'Fasilitas Umum', 'Lainnya'];

        return view('contracts.create', compact('kelompokOptions', 'statusMitraOptions', 'jenisAsetOptions'));
    }

    /**
     * Simpan kontrak baru beserta data mitra dan aset ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kontrak' => 'required|string|max:150|unique:contracts,nomor_kontrak',
            'nama_mitra' => 'required|string|max:255',
            'status_mitra' => 'nullable|string|max:100',
            'alamat_mitra' => 'nullable|string|max:500',
            'nomor_aset' => 'nullable|string|max:100',
            'blok_aset' => 'nullable|string|max:255',
            'jenis_aset' => 'nullable|string|max:100',
            'peruntukan' => 'nullable|string|max:255',
            'luas_tanah' => 'nullable|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'kmhm' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tanggal_kontrak' => 'nullable|string|max:50',
            'kelompok' => 'nullable|string|max:100',
            'masa_awal' => 'nullable|date',
            'masa_akhir' => 'nullable|date',
            'nilai_kontrak' => 'nullable|numeric|min:0',
        ], [
            'nomor_kontrak.required' => 'Nomor Kontrak wajib diisi.',
            'nomor_kontrak.unique' => 'Nomor Kontrak ini sudah terdaftar sebelumnya.',
            'nama_mitra.required' => 'Nama Mitra / Penyewa wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Simpan atau Cari Mitra (Tenant)
            $tenant = Tenant::firstOrCreate(
                ['nama_mitra' => trim($validated['nama_mitra'])],
                [
                    'status_mitra' => $validated['status_mitra'] ?? 'Swasta',
                    'alamat_mitra' => $validated['alamat_mitra'] ?? null,
                ]
            );

            if ($request->filled('status_mitra') || $request->filled('alamat_mitra')) {
                $tenant->update(array_filter([
                    'status_mitra' => $validated['status_mitra'] ?? null,
                    'alamat_mitra' => $validated['alamat_mitra'] ?? null,
                ]));
            }

            // 2. Simpan Aset jika nomor aset disediakan
            $nomorAset = ! empty($validated['nomor_aset']) ? trim($validated['nomor_aset']) : null;
            if ($nomorAset) {
                KaiAsset::updateOrCreate(
                    ['nomor_aset' => $nomorAset],
                    [
                        'blok_aset' => $validated['blok_aset'] ?? null,
                        'jenis_aset' => $validated['jenis_aset'] ?? 'Tanah',
                        'peruntukan' => $validated['peruntukan'] ?? null,
                        'luas_tanah' => $validated['luas_tanah'] ?? 0,
                        'luas_bangunan' => $validated['luas_bangunan'] ?? 0,
                        'kmhm' => $validated['kmhm'] ?? null,
                        'latitude' => $validated['latitude'] ?? null,
                        'longitude' => $validated['longitude'] ?? null,
                    ]
                );
            }

            // 3. Simpan Kontrak
            KaiContract::create([
                'nomor_kontrak' => trim($validated['nomor_kontrak']),
                'tenant_id' => $tenant->id,
                'nomor_aset' => $nomorAset,
                'tanggal_kontrak' => $validated['tanggal_kontrak'] ?? null,
                'kelompok' => $validated['kelompok'] ?? 'Non Row',
                'masa_awal' => $validated['masa_awal'] ?? null,
                'masa_akhir' => $validated['masa_akhir'] ?? null,
                'nilai_kontrak' => $validated['nilai_kontrak'] ?? 0,
                'created_at' => now(),
            ]);
        });

        self::forgetCache();

        return redirect()->route('contracts.index')->with('success', 'Kontrak baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form pengeditan kontrak (Hanya Super Admin / Admin).
     */
    public function edit(string $contract)
    {
        $contract = KaiContract::with(['tenant', 'asset', 'payments'])->findOrFail($contract);

        $kelompokOptions = Cache::remember('dropdown_kelompok', 3600, fn () => KaiContract::select('kelompok')->distinct()
            ->whereNotNull('kelompok')->where('kelompok', '!=', '')
            ->pluck('kelompok')->toArray()
        );
        if (empty($kelompokOptions)) {
            $kelompokOptions = ['Non Row', 'Row'];
        }

        $statusMitraOptions = Cache::remember('dropdown_status_mitra', 3600, fn () => Tenant::select('status_mitra')->distinct()
            ->whereNotNull('status_mitra')->where('status_mitra', '!=', '')
            ->pluck('status_mitra')->toArray()
        );
        if (empty($statusMitraOptions)) {
            $statusMitraOptions = ['Swasta', 'Perorangan', 'BUMN', 'Pegawai Aktif', 'Pensiunan'];
        }

        $jenisAsetOptions = ['Tanah', 'Bangunan', 'Tanah & Bangunan', 'Fasilitas Umum', 'Lainnya'];

        return view('contracts.edit', compact('contract', 'kelompokOptions', 'statusMitraOptions', 'jenisAsetOptions'));
    }

    /**
     * Update data kontrak, mitra, dan aset di database.
     */
    public function update(Request $request, string $contract)
    {
        $contractModel = KaiContract::with(['tenant', 'asset'])->findOrFail($contract);

        $validated = $request->validate([
            'nomor_kontrak' => 'required|string|max:150|unique:contracts,nomor_kontrak,'.$contractModel->nomor_kontrak.',nomor_kontrak',
            'nama_mitra' => 'required|string|max:255',
            'status_mitra' => 'nullable|string|max:100',
            'alamat_mitra' => 'nullable|string|max:500',
            'nomor_aset' => 'nullable|string|max:100',
            'blok_aset' => 'nullable|string|max:255',
            'jenis_aset' => 'nullable|string|max:100',
            'peruntukan' => 'nullable|string|max:255',
            'luas_tanah' => 'nullable|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'kmhm' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tanggal_kontrak' => 'nullable|string|max:50',
            'kelompok' => 'nullable|string|max:100',
            'masa_awal' => 'nullable|date',
            'masa_akhir' => 'nullable|date',
            'nilai_kontrak' => 'nullable|numeric|min:0',
        ], [
            'nomor_kontrak.required' => 'Nomor Kontrak wajib diisi.',
            'nomor_kontrak.unique' => 'Nomor Kontrak ini sudah digunakan oleh data lain.',
            'nama_mitra.required' => 'Nama Mitra / Penyewa wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $contractModel) {
            // 1. Update atau Buat Tenant
            if ($contractModel->tenant) {
                $contractModel->tenant->update([
                    'nama_mitra' => trim($validated['nama_mitra']),
                    'status_mitra' => $validated['status_mitra'] ?? $contractModel->tenant->status_mitra,
                    'alamat_mitra' => $validated['alamat_mitra'] ?? $contractModel->tenant->alamat_mitra,
                ]);
                $tenantId = $contractModel->tenant->id;
            } else {
                $tenant = Tenant::create([
                    'nama_mitra' => trim($validated['nama_mitra']),
                    'status_mitra' => $validated['status_mitra'] ?? 'Swasta',
                    'alamat_mitra' => $validated['alamat_mitra'] ?? null,
                ]);
                $tenantId = $tenant->id;
            }

            // 2. Update atau Buat Aset
            $nomorAset = ! empty($validated['nomor_aset']) ? trim($validated['nomor_aset']) : null;
            if ($nomorAset) {
                KaiAsset::updateOrCreate(
                    ['nomor_aset' => $nomorAset],
                    [
                        'blok_aset' => $validated['blok_aset'] ?? null,
                        'jenis_aset' => $validated['jenis_aset'] ?? 'Tanah',
                        'peruntukan' => $validated['peruntukan'] ?? null,
                        'luas_tanah' => $validated['luas_tanah'] ?? 0,
                        'luas_bangunan' => $validated['luas_bangunan'] ?? 0,
                        'kmhm' => $validated['kmhm'] ?? null,
                        'latitude' => $validated['latitude'] ?? null,
                        'longitude' => $validated['longitude'] ?? null,
                    ]
                );
            }

            // 3. Jika nomor kontrak diubah, perbarui di DB (cascade ke payments)
            $newNomor = trim($validated['nomor_kontrak']);
            if ($newNomor !== $contractModel->nomor_kontrak) {
                DB::table('contracts')
                    ->where('nomor_kontrak', $contractModel->nomor_kontrak)
                    ->update(['nomor_kontrak' => $newNomor]);
                $contractModel->nomor_kontrak = $newNomor;
            }

            // 4. Update data kontrak lainnya
            $contractModel->update([
                'tenant_id' => $tenantId,
                'nomor_aset' => $nomorAset,
                'tanggal_kontrak' => $validated['tanggal_kontrak'] ?? null,
                'kelompok' => $validated['kelompok'] ?? 'Non Row',
                'masa_awal' => $validated['masa_awal'] ?? null,
                'masa_akhir' => $validated['masa_akhir'] ?? null,
                'nilai_kontrak' => $validated['nilai_kontrak'] ?? 0,
            ]);
        });

        self::forgetCache();

        return redirect()->route('contracts.index')->with('success', "Kontrak {$validated['nomor_kontrak']} berhasil diperbarui.");
    }

    /**
     * Hapus kontrak dan data tagihan terkait (Hanya Super Admin / Admin).
     */
    public function destroy(string $contract)
    {
        $contractModel = KaiContract::findOrFail($contract);

        DB::transaction(function () use ($contractModel) {
            $contractModel->payments()->delete();
            $contractModel->delete();
        });

        self::forgetCache();

        return redirect()->route('contracts.index')->with('success', "Kontrak {$contract} dan seluruh data tagihan terkait berhasil dihapus.");
    }

    public static function forgetCache(): void
    {
        Cache::forget('dropdown_kelompok');
        Cache::forget('dropdown_status_mitra');
        DashboardController::forgetCache();
    }
}
