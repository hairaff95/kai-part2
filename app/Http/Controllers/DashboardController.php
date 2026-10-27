<?php

namespace App\Http\Controllers;

use App\Models\KaiAsset;
use App\Models\KaiContract;
use App\Models\PaymentRecord;
use App\Services\PerformanceCrowlService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private const CACHE_TTL = 1800; // 30 menit

    public function __construct(
        protected PerformanceCrowlService $performanceService
    ) {}

    public function index()
    {
        // ── 1. Statistik Kinerja, Crowl & Aset Daop 4 ───────────────────────
        $performance = Cache::remember('dashboard_crowl_metrics', self::CACHE_TTL, function () {
            return $this->performanceService->getPerformanceMetrics();
        });

        // ── 2. Kartu Statistik Utama ───────────────────────────────────────
        $stats = Cache::remember('dashboard_stats', self::CACHE_TTL, function () use ($performance) {
            $today = now()->toDateString();
            $totalKontrak = KaiContract::count();
            $kontrakAktif = KaiContract::whereNotNull('masa_akhir')->whereDate('masa_akhir', '>=', $today)->count();
            $backlog = KaiContract::whereNotNull('masa_akhir')->whereDate('masa_akhir', '<', $today)->count();
            $totalTagihan = (float) PaymentRecord::sum('total_bayar');

            $formatRupiah = function (float $val) {
                if ($val >= 1_000_000_000) {
                    return 'Rp '.number_format($val / 1_000_000_000, 1, ',', '.').' M';
                } elseif ($val >= 1_000_000) {
                    return 'Rp '.number_format($val / 1_000_000, 1, ',', '.').' Jt';
                }

                return 'Rp '.number_format($val, 0, ',', '.');
            };

            $totalTagihanFormatted = $formatRupiah($totalTagihan);
            $totalNilaiKontrakFormatted = $formatRupiah($performance['totalNilaiKontrak']);
            $totalCrowlTerakrualFormatted = $formatRupiah($performance['totalCrowlTerakrual']);
            $totalCrowlHarianFormatted = $formatRupiah($performance['totalCrowlHarianAktif']).'/hari';
            $totalTunggakanFormatted = $formatRupiah($performance['totalTunggakanBacklog']);

            return compact(
                'totalKontrak',
                'kontrakAktif',
                'backlog',
                'totalTagihanFormatted',
                'totalNilaiKontrakFormatted',
                'totalCrowlTerakrualFormatted',
                'totalCrowlHarianFormatted',
                'totalTunggakanFormatted'
            );
        });

        // ── 3. Profil Aset Potensial Daop 4 ────────────────────────────────
        $assetStats = Cache::remember('dashboard_assets_profile', self::CACHE_TTL, function () {
            $total = KaiAsset::count();
            $potensial = KaiAsset::where(function ($q) {
                $q->where('status_potensi', 'potensial')->orWhereNull('status_potensi');
            })->count();
            $terkontrak = KaiAsset::where('status_potensi', 'terkontrak')->count();

            // Sampling per stasiun utama Daop 4
            $stations = ['Semarang Poncol', 'Semarang Tawang', 'Pekalongan', 'Tegal', 'Cepu', 'Weleri'];
            $samplingStations = [];
            foreach ($stations as $stn) {
                $countPot = KaiAsset::where('stasiun', $stn)->where(function ($q) {
                    $q->where('status_potensi', 'potensial')->orWhereNull('status_potensi');
                })->count();
                $countTer = KaiAsset::where('stasiun', $stn)->where('status_potensi', 'terkontrak')->count();

                $samplingStations[] = [
                    'name' => $stn,
                    'potensial' => $countPot,
                    'terkontrak' => $countTer,
                    'total' => $countPot + $countTer,
                ];
            }

            return compact('total', 'potensial', 'terkontrak', 'samplingStations');
        });

        // ── 4. Wave Chart SVG Generator (Persis Estetika test-project-kai) ──
        $chartData = Cache::remember('dashboard_chart', self::CACHE_TTL, function () use ($performance) {
            $monthlyRaw = $performance['monthlyCrowl'];
            $vals = array_column($monthlyRaw, 'val');
            $maxVal = max($vals) ?: 1;
            $minVal = min($vals);

            $yTop = ceil($maxVal / 50_000_000) * 50_000_000 ?: 100_000_000;
            $yBottom = max(0, floor($minVal / 50_000_000) * 50_000_000);
            if ($yTop <= $yBottom) {
                $yTop = $yBottom + 100_000_000;
            }

            $yGridLabels = [];
            $step = ($yTop - $yBottom) / 5;
            for ($i = 5; $i >= 0; $i--) {
                $cur = $yBottom + ($i * $step);
                if ($cur >= 1_000_000_000) {
                    $yGridLabels[] = rtrim(rtrim(number_format($cur / 1_000_000_000, 1, ',', '.'), '0'), ',').'M';
                } elseif ($cur >= 1_000_000) {
                    $yGridLabels[] = round($cur / 1_000_000).'jt';
                } else {
                    $yGridLabels[] = (string) round($cur);
                }
            }

            $points = [];
            foreach ($monthlyRaw as $idx => $m) {
                $x = round($idx * (1000 / 11), 1);
                $normalized = ($m['val'] - $yBottom) / max(1, ($yTop - $yBottom));
                $normalized = max(0.06, min(0.92, $normalized));
                $y = round(180 - ($normalized * 135), 1);
                $points[] = ['x' => $x, 'y' => $y, 'val' => $m['val'], 'key' => $m['key']];
            }

            $primaryWavePath = "M {$points[0]['x']},{$points[0]['y']}";
            $lightWavePath = "M {$points[0]['x']},".max(15, $points[0]['y'] - 14);

            for ($i = 0; $i < count($points) - 1; $i++) {
                $p0 = $i > 0 ? $points[$i - 1] : $points[$i];
                $p1 = $points[$i];
                $p2 = $points[$i + 1];
                $p3 = $i < count($points) - 2 ? $points[$i + 2] : $p2;

                $cp1x = round($p1['x'] + ($p2['x'] - $p0['x']) / 6, 1);
                $cp1y = round($p1['y'] + ($p2['y'] - $p0['y']) / 6, 1);
                $cp2x = round($p2['x'] - ($p3['x'] - $p1['x']) / 6, 1);
                $cp2y = round($p2['y'] - ($p3['y'] - $p1['y']) / 6, 1);

                $primaryWavePath .= " C {$cp1x},{$cp1y} {$cp2x},{$cp2y} {$p2['x']},{$p2['y']}";
                $lightWavePath .= " C {$cp1x},".max(12, $cp1y - 14)." {$cp2x},".max(12, $cp2y - 14)." {$p2['x']},".max(12, $p2['y'] - 14);
            }

            $gradientFillPath = $primaryWavePath.' L 1000,200 L 0,200 Z';

            $sortedPoints = $points;
            usort($sortedPoints, fn ($a, $b) => $b['val'] <=> $a['val']);
            $p1 = $sortedPoints[0] ?? $points[2];
            $p2 = $sortedPoints[1] ?? $points[9];

            $formatBadge = function ($val) {
                if ($val >= 1_000_000_000) {
                    return number_format($val / 1_000_000_000, 1, ',', '.').'M';
                }
                if ($val >= 1_000_000) {
                    return round($val / 1_000_000).'jt';
                }

                return (string) round($val);
            };

            $peakPoint1 = [
                'percent' => round(($p1['x'] / 1000) * 100),
                'top' => round(($p1['y'] / 200) * 100),
                'label' => $formatBadge($p1['val']),
            ];
            $peakPoint2 = [
                'percent' => round(($p2['x'] / 1000) * 100),
                'top' => round(($p2['y'] / 200) * 100),
                'label' => $formatBadge($p2['val']),
            ];

            return compact('yGridLabels', 'primaryWavePath', 'lightWavePath', 'gradientFillPath', 'monthlyRaw', 'peakPoint1', 'peakPoint2');
        });

        // ── 5. Distribusi Kelompok Kontrak (Chart Batang) ──────────────────
        $kelompokData = Cache::remember('dashboard_kelompok', self::CACHE_TTL, function () {
            $rows = KaiContract::selectRaw('kelompok, COUNT(*) as total')
                ->whereNotNull('kelompok')
                ->where('kelompok', '!=', '')
                ->groupBy('kelompok')
                ->orderByDesc('total')
                ->get();

            $grandTotal = $rows->sum('total') ?: 1;
            $colors = ['#0D63E5', '#94B4FF', '#EB4D4B', '#F99827', '#00C49F', '#8B5CF6'];

            return $rows->map(function ($r, $i) use ($grandTotal, $colors) {
                return [
                    'name' => $r->kelompok,
                    'total' => $r->total,
                    'percentage' => round(($r->total / $grandTotal) * 100),
                    'color' => $colors[$i % count($colors)],
                ];
            })->values()->toArray();
        });

        // ── 6. Kontrak Jatuh Tempo (90 Hari ke Depan) ─────────────────────
        $upcomingContracts = Cache::remember('dashboard_upcoming', 600, function () {
            $today = now()->toDateString();

            return KaiContract::with('tenant')
                ->whereNotNull('masa_akhir')
                ->whereDate('masa_akhir', '>=', $today)
                ->whereDate('masa_akhir', '<=', now()->addDays(90)->toDateString())
                ->orderBy('masa_akhir')
                ->limit(5)
                ->get()
                ->map(fn ($c) => [
                    'kelompok' => $c->kelompok ?? 'Sewa',
                    'nama' => $c->tenant?->nama_mitra ?? '-',
                    'jatuh_tempo' => $c->masa_akhir ? Carbon::parse($c->masa_akhir)->format('d-m-Y') : '-',
                    'sisa' => $c->sisa_hari,
                ])->toArray();
        });

        // Persentase Donut RKA / Capaian
        $donutPercentage = 55;
        if (($performance['totalNilaiKontrak'] ?? 0) > 0) {
            $donutPercentage = min(100, max(10, round(($performance['totalCrowlTerakrual'] / $performance['totalNilaiKontrak']) * 100)));
        }

        return view('dashboard.index', array_merge(
            $stats,
            $chartData,
            [
                'performance' => $performance,
                'assetStats' => $assetStats,
                'kelompokData' => $kelompokData,
                'upcomingContracts' => $upcomingContracts,
                'donutPercentage' => $donutPercentage,
            ]
        ));
    }

    /**
     * Action Tombol "Update Kinerja"
     */
    public function updateKinerja(Request $request)
    {
        $metrics = $this->performanceService->refreshPerformanceAndCache();
        Cache::forget('dashboard_assets_profile');
        Cache::forget('dashboard_chart');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perhitungan kinerja kontrak & crowl akuntansi berhasil diperbarui!',
                'metrics' => $metrics,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('toast_success', 'Perhitungan Kinerja Berhasil Diperbarui! Akrual Crowl harian dan profil potensi aset telah disinkronkan.');
    }

    /**
     * Endpoint API Data Bergerak / Real-Time untuk Grafik Live
     */
    public function liveChartData()
    {
        $metrics = $this->performanceService->getPerformanceMetrics();

        return response()->json([
            'monthlyCrowl' => $metrics['monthlyCrowl'],
            'monthlyRealisasi' => $metrics['monthlyRealisasi'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public static function forgetCache(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_kelompok');
        Cache::forget('dashboard_chart');
        Cache::forget('dashboard_upcoming');
        Cache::forget('dashboard_crowl_metrics');
        Cache::forget('dashboard_assets_profile');
        Cache::forget('map_assets');
    }
}
