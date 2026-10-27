<?php

namespace App\Services;

use App\Models\KaiAsset;
use App\Models\KaiContract;
use App\Models\PaymentRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PerformanceCrowlService
{
    /**
     * Menghitung seluruh metrik Kinerja & Crowl Akuntansi
     */
    public function getPerformanceMetrics(): array
    {
        $today = now()->startOfDay();

        // 1. Kinerja Kontrak (Diakui 100% di depan)
        $totalNilaiKontrak = (float) KaiContract::sum('nilai_kontrak');

        // Kontrak aktif hari ini
        $contracts = KaiContract::whereNotNull('masa_awal')
            ->whereNotNull('masa_akhir')
            ->get();

        $totalCrowlTerakrual = 0.0;
        $totalCrowlHarianAktif = 0.0;
        $totalCrowlSisa = 0.0;

        foreach ($contracts as $c) {
            $awal = Carbon::parse($c->masa_awal)->startOfDay();
            $akhir = Carbon::parse($c->masa_akhir)->endOfDay();
            $nilai = (float) $c->nilai_kontrak;

            // Total hari masa kontrak (minimal 1 hari)
            $totalHari = max(1, $awal->diffInDays($akhir) + 1);
            $crowlPerHari = $nilai / $totalHari;

            if ($today->lt($awal)) {
                // Belum mulai
                $crowlTerakrual = 0.0;
                $crowlSisa = $nilai;
            } elseif ($today->gt($akhir)) {
                // Sudah berakhir: 100% diakui
                $crowlTerakrual = $nilai;
                $crowlSisa = 0.0;
            } else {
                // Sedang berjalan
                $hariBerjalan = $awal->diffInDays($today) + 1;
                $crowlTerakrual = min($nilai, $crowlPerHari * $hariBerjalan);
                $crowlSisa = max(0.0, $nilai - $crowlTerakrual);
                $totalCrowlHarianAktif += $crowlPerHari;
            }

            $totalCrowlTerakrual += $crowlTerakrual;
            $totalCrowlSisa += $crowlSisa;
        }

        // 2. Tunggakan (langsung di depan)
        // Tagihan termin yang jatuh tempo sebelum atau sama dengan hari ini namun statusnya belum lunas / backlog
        $totalTagihan = (float) PaymentRecord::sum('total_bayar');
        $backlogContracts = KaiContract::whereNotNull('masa_akhir')
            ->whereDate('masa_akhir', '<', $today->toDateString())
            ->get();
        $totalTunggakanBacklog = (float) $backlogContracts->sum('nilai_kontrak');

        // 3. Status Profil Aset Potensial Daop 4
        $totalAset = KaiAsset::count();
        $asetPotensial = KaiAsset::where(function ($q) {
            $q->where('status_potensi', 'potensial')
                ->orWhereNull('status_potensi');
        })->count();
        $asetTerkontrak = KaiAsset::where('status_potensi', 'terkontrak')->count();

        // 4. Proyeksi Crowl Bulanan untuk Tahun Berjalan (12 Bulan)
        $currentYear = now()->year;
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyCrowl = [];
        $monthlyRealisasi = [];

        for ($m = 1; $m <= 12; $m++) {
            $startOfMonth = Carbon::create($currentYear, $m, 1)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $m, 1)->endOfMonth();

            // Realisasi tagihan aktual dari payment_records
            $realisasiSum = (float) PaymentRecord::whereMonth('tanggal_termin', $m)
                ->whereYear('tanggal_termin', $currentYear)
                ->sum('total_bayar');

            // Akrual Crowl bulanan dari kontrak yang aktif pada bulan ini
            $crowlMonthSum = 0.0;
            foreach ($contracts as $c) {
                $awal = Carbon::parse($c->masa_awal)->startOfDay();
                $akhir = Carbon::parse($c->masa_akhir)->endOfDay();

                // Cek irisan (overlap) kontrak dengan bulan ini
                if ($awal->lte($endOfMonth) && $akhir->gte($startOfMonth)) {
                    $overlapStart = $awal->gt($startOfMonth) ? $awal : $startOfMonth;
                    $overlapEnd = $akhir->lt($endOfMonth) ? $akhir : $endOfMonth;
                    $overlapDays = max(0, $overlapStart->diffInDays($overlapEnd) + 1);

                    $totalHari = max(1, $awal->diffInDays($akhir) + 1);
                    $nilaiPerHari = ((float) $c->nilai_kontrak) / $totalHari;
                    $crowlMonthSum += ($nilaiPerHari * $overlapDays);
                }
            }

            $monthlyCrowl[] = [
                'key' => $bulanLabels[$m - 1],
                'val' => round($crowlMonthSum),
            ];

            $monthlyRealisasi[] = [
                'key' => $bulanLabels[$m - 1],
                'val' => round($realisasiSum),
            ];
        }

        return [
            'totalNilaiKontrak' => $totalNilaiKontrak,
            'totalCrowlTerakrual' => $totalCrowlTerakrual,
            'totalCrowlHarianAktif' => $totalCrowlHarianAktif,
            'totalCrowlSisa' => $totalCrowlSisa,
            'totalTunggakanBacklog' => $totalTunggakanBacklog,
            'totalTagihan' => $totalTagihan,
            'totalAset' => $totalAset,
            'asetPotensial' => $asetPotensial,
            'asetTerkontrak' => $asetTerkontrak,
            'monthlyCrowl' => $monthlyCrowl,
            'monthlyRealisasi' => $monthlyRealisasi,
        ];
    }

    /**
     * Singkronisasi status potensi lahan aset otomatis berdasarkan kontrak aktif
     */
    public function syncAssetPotentialStatus(): void
    {
        $today = now()->toDateString();

        // Cari nomor aset yang memiliki kontrak aktif
        $contractedAssetNumbers = KaiContract::whereNotNull('nomor_aset')
            ->whereNotNull('masa_akhir')
            ->whereDate('masa_akhir', '>=', $today)
            ->pluck('nomor_aset')
            ->unique()
            ->toArray();

        if (! empty($contractedAssetNumbers)) {
            KaiAsset::whereIn('nomor_aset', $contractedAssetNumbers)
                ->update(['status_potensi' => 'terkontrak']);

            KaiAsset::whereNotIn('nomor_aset', $contractedAssetNumbers)
                ->where('status_potensi', '!=', 'terkontrak')
                ->update(['status_potensi' => 'potensial']);
        }
    }

    /**
     * Memproses trigger Update Kinerja & Reset Cache
     */
    public function refreshPerformanceAndCache(): array
    {
        $this->syncAssetPotentialStatus();

        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_kelompok');
        Cache::forget('dashboard_chart');
        Cache::forget('dashboard_upcoming');
        Cache::forget('dashboard_crowl_metrics');
        Cache::forget('map_assets');

        return $this->getPerformanceMetrics();
    }
}
