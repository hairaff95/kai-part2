<?php

namespace App\Http\Controllers;

use App\Models\KaiContract;
use App\Models\KaiAsset;
use App\Models\PaymentRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private const CACHE_TTL = 1800; // 30 menit

    public function index()
    {
        // ── Kartu 4 Statistik ────────────────────────────────────────────
        $stats = Cache::remember('dashboard_stats', self::CACHE_TTL, function () {
            $today = now()->toDateString();
            $totalKontrak    = KaiContract::count();
            $kontrakAktif    = KaiContract::whereNotNull('masa_akhir')->whereDate('masa_akhir', '>=', $today)->count();
            $backlog         = KaiContract::whereNotNull('masa_akhir')->whereDate('masa_akhir', '<', $today)->count();
            $totalTagihan    = (float) PaymentRecord::sum('total_bayar');

            if ($totalTagihan >= 1_000_000_000) {
                $totalTagihanFormatted = 'Rp ' . number_format($totalTagihan / 1_000_000_000, 1, ',', '.') . ' M';
            } elseif ($totalTagihan >= 1_000_000) {
                $totalTagihanFormatted = 'Rp ' . number_format($totalTagihan / 1_000_000, 1, ',', '.') . ' Jt';
            } else {
                $totalTagihanFormatted = 'Rp ' . number_format($totalTagihan, 0, ',', '.');
            }

            return compact('totalKontrak', 'kontrakAktif', 'backlog', 'totalTagihanFormatted');
        });

        // ── Distribusi Kelompok (chart batang) ────────────────────────────
        $kelompokData = Cache::remember('dashboard_kelompok', self::CACHE_TTL, function () {
            $rows = KaiContract::selectRaw('kelompok, COUNT(*) as total')
                ->whereNotNull('kelompok')
                ->where('kelompok', '!=', '')
                ->groupBy('kelompok')
                ->orderByDesc('total')
                ->get();

            $grandTotal = $rows->sum('total') ?: 1;
            $colors     = ['#1570EF', '#10B981', '#F79009', '#EF4444', '#8B5CF6', '#06B6D4'];

            return $rows->map(function ($r, $i) use ($grandTotal, $colors) {
                return [
                    'name'       => $r->kelompok,
                    'total'      => $r->total,
                    'percentage' => round(($r->total / $grandTotal) * 100),
                    'color'      => $colors[$i % count($colors)],
                ];
            })->values()->toArray();
        });

        // ── Tagihan per bulan (chart garis) ──────────────────────────────
        $chartData = Cache::remember('dashboard_chart', self::CACHE_TTL, function () {
            $monthlyRaw = [];
            $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

            for ($m = 1; $m <= 12; $m++) {
                $sum = (float) PaymentRecord::whereMonth('tanggal_termin', $m)
                    ->whereYear('tanggal_termin', now()->year)
                    ->sum('total_bayar');
                $monthlyRaw[] = ['key' => $bulanLabels[$m - 1], 'val' => $sum];
            }

            $vals   = array_column($monthlyRaw, 'val');
            $maxVal = max($vals) ?: 1;
            $minVal = min($vals);

            $yTop    = ceil($maxVal / 50_000_000) * 50_000_000 ?: 100_000_000;
            $yBottom = max(0, floor($minVal / 50_000_000) * 50_000_000);
            if ($yTop <= $yBottom) $yTop = $yBottom + 100_000_000;

            $yGridLabels = [];
            $step = ($yTop - $yBottom) / 4;
            for ($i = 4; $i >= 0; $i--) {
                $cur = $yBottom + ($i * $step);
                if ($cur >= 1_000_000_000) {
                    $yGridLabels[] = number_format($cur / 1_000_000_000, 1, ',', '.') . 'M';
                } elseif ($cur >= 1_000_000) {
                    $yGridLabels[] = round($cur / 1_000_000) . 'jt';
                } else {
                    $yGridLabels[] = (string) round($cur);
                }
            }

            $points = [];
            foreach ($monthlyRaw as $idx => $m) {
                $x          = round($idx * (1000 / 11), 1);
                $normalized = ($m['val'] - $yBottom) / max(1, ($yTop - $yBottom));
                $normalized = max(0.05, min(0.95, $normalized));
                $y          = round(175 - ($normalized * 135), 1);
                $points[]   = ['x' => $x, 'y' => $y, 'val' => $m['val'], 'key' => $m['key']];
            }

            $primaryWavePath = "M {$points[0]['x']},{$points[0]['y']}";
            $lightWavePath   = "M {$points[0]['x']}," . max(20, $points[0]['y'] - 12);

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
                $lightWavePath   .= " C {$cp1x}," . max(15, $cp1y - 12) . " {$cp2x}," . max(15, $cp2y - 12) . " {$p2['x']}," . max(15, $p2['y'] - 12);
            }

            $gradientFillPath = $primaryWavePath . " L 1000,200 L 0,200 Z";

            return compact('yGridLabels', 'primaryWavePath', 'lightWavePath', 'gradientFillPath', 'monthlyRaw');
        });

        // ── Kontrak akan segera jatuh tempo (30 hari ke depan) ───────────
        $upcomingContracts = Cache::remember('dashboard_upcoming', 600, function () {
            $today = now()->toDateString();
            return KaiContract::with('tenant')
                ->whereNotNull('masa_akhir')
                ->whereDate('masa_akhir', '>=', $today)
                ->whereDate('masa_akhir', '<=', now()->addDays(90)->toDateString())
                ->orderBy('masa_akhir')
                ->limit(10)
                ->get()
                ->map(fn ($c) => [
                    'kelompok'   => $c->kelompok ?? '-',
                    'nama'       => $c->tenant?->nama_mitra ?? '-',
                    'jatuh_tempo'=> $c->masa_akhir?->format('d/m/Y') ?? '-',
                    'sisa'       => $c->sisa_hari,
                ])->toArray();
        });

        return view('dashboard.index', array_merge(
            $stats,
            $chartData,
            ['kelompokData' => $kelompokData, 'upcomingContracts' => $upcomingContracts]
        ));
    }

    public static function forgetCache(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_kelompok');
        Cache::forget('dashboard_chart');
        Cache::forget('dashboard_upcoming');
    }
}
