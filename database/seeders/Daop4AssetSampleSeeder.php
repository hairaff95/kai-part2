<?php

namespace Database\Seeders;

use App\Models\KaiAsset;
use Illuminate\Database\Seeder;

class Daop4AssetSampleSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            'Semarang Poncol' => ['lat' => -6.969700, 'lng' => 110.413700],
            'Semarang Tawang' => ['lat' => -6.964400, 'lng' => 110.428100],
            'Pekalongan' => ['lat' => -6.886200, 'lng' => 109.673800],
            'Tegal' => ['lat' => -6.879000, 'lng' => 109.125000],
            'Batang' => ['lat' => -6.908000, 'lng' => 109.730000],
            'Weleri' => ['lat' => -6.971500, 'lng' => 110.065800],
            'Cepu' => ['lat' => -7.151900, 'lng' => 111.590800],
            'Ngrombo' => ['lat' => -7.155400, 'lng' => 110.902300],
        ];

        $stationNames = array_keys($stations);
        $assets = KaiAsset::with('contract')->get();

        $index = 0;
        foreach ($assets as $asset) {
            $blok = strtoupper($asset->blok_aset ?? '');

            // Tentukan stasiun berdasarkan teks blok_aset jika ada
            $stasiunTerpilih = null;
            if (str_contains($blok, 'PEKALONGAN')) {
                $stasiunTerpilih = 'Pekalongan';
            } elseif (str_contains($blok, 'TEGAL')) {
                $stasiunTerpilih = 'Tegal';
            } elseif (str_contains($blok, 'BATANG')) {
                $stasiunTerpilih = 'Batang';
            } elseif (str_contains($blok, 'KENDAL') || str_contains($blok, 'WELERI')) {
                $stasiunTerpilih = 'Weleri';
            } elseif (str_contains($blok, 'CEPU') || str_contains($blok, 'BLORA')) {
                $stasiunTerpilih = 'Cepu';
            } elseif (str_contains($blok, 'SEMARANG')) {
                $stasiunTerpilih = ($index % 2 === 0) ? 'Semarang Poncol' : 'Semarang Tawang';
            } else {
                $stasiunTerpilih = $stationNames[$index % count($stationNames)];
            }

            $baseCoord = $stations[$stasiunTerpilih];
            // Jitter kecil agar pin tidak tumpuk tepat di titik yang sama (±0.005 s.d ±0.015 derajat ~ 500m - 1.5km)
            $jitterLat = (($index * 13) % 20 - 10) * 0.0012;
            $jitterLng = (($index * 17) % 20 - 10) * 0.0012;

            $hasActiveContract = $asset->contract && (! $asset->contract->masa_akhir || $asset->contract->masa_akhir >= now()->toDateString());
            $status = $hasActiveContract ? 'terkontrak' : (($index % 3 === 0) ? 'potensial' : 'potensial');

            $asset->stasiun = $stasiunTerpilih;
            $asset->wilayah_asset = 'Daop 4 Semarang';
            $asset->latitude = $baseCoord['lat'] + $jitterLat;
            $asset->longitude = $baseCoord['lng'] + $jitterLng;
            $asset->status_potensi = $status;
            if (! $asset->luas_tanah || $asset->luas_tanah == 0) {
                $asset->luas_tanah = round(150 + (($index * 47) % 1200), 2);
            }
            $asset->save();

            $index++;
        }

        // Pastikan ada beberapa aset potensial unggulan (Lahan Kosong) di Daop 4
        $primeSamples = [
            [
                'nomor_aset' => 'AST-SMG-POT-001',
                'blok_aset' => 'Kavling Lahan Potensial Stasiun Semarang Poncol (Komersial)',
                'jenis_aset' => 'Tanah',
                'peruntukan' => 'Food Court & Minimarket',
                'luas_tanah' => 450.00,
                'luas_bangunan' => 0,
                'kmhm' => 'KM 1+200',
                'stasiun' => 'Semarang Poncol',
                'wilayah_asset' => 'Daop 4 Semarang',
                'status_potensi' => 'potensial',
                'latitude' => -6.969100,
                'longitude' => 110.414500,
            ],
            [
                'nomor_aset' => 'AST-TWG-POT-002',
                'blok_aset' => 'Lahan Kosong Eks Depo Tawang Polder Semarang',
                'jenis_aset' => 'Tanah',
                'peruntukan' => 'Area Parkir & Pusat Oleh-Oleh',
                'luas_tanah' => 1200.00,
                'luas_bangunan' => 0,
                'kmhm' => 'KM 2+100',
                'stasiun' => 'Semarang Tawang',
                'wilayah_asset' => 'Daop 4 Semarang',
                'status_potensi' => 'potensial',
                'latitude' => -6.963800,
                'longitude' => 110.429000,
            ],
            [
                'nomor_aset' => 'AST-PKL-POT-003',
                'blok_aset' => 'Gudang Potensial Barat Stasiun Pekalongan',
                'jenis_aset' => 'Bangunan',
                'peruntukan' => 'Gudang Logistik & Ekspedisi',
                'luas_tanah' => 600.00,
                'luas_bangunan' => 450.00,
                'kmhm' => 'KM 86+200',
                'stasiun' => 'Pekalongan',
                'wilayah_asset' => 'Daop 4 Semarang',
                'status_potensi' => 'potensial',
                'latitude' => -6.885500,
                'longitude' => 109.674800,
            ],
            [
                'nomor_aset' => 'AST-TGL-POT-004',
                'blok_aset' => 'Lahan Kosong Pinggir Rel Stasiun Tegal',
                'jenis_aset' => 'Tanah',
                'peruntukan' => 'Pemasangan Reklame / Media Iklan',
                'luas_tanah' => 200.00,
                'luas_bangunan' => 0,
                'kmhm' => 'KM 148+100',
                'stasiun' => 'Tegal',
                'wilayah_asset' => 'Daop 4 Semarang',
                'status_potensi' => 'potensial',
                'latitude' => -6.878500,
                'longitude' => 109.126200,
            ],
            [
                'nomor_aset' => 'AST-CPU-POT-005',
                'blok_aset' => 'Lahan Siap Bangun Kawasan Stasiun Cepu',
                'jenis_aset' => 'Tanah',
                'peruntukan' => 'Pergudangan & Bongkar Muat Barang',
                'luas_tanah' => 1500.00,
                'luas_bangunan' => 0,
                'kmhm' => 'KM 220+500',
                'stasiun' => 'Cepu',
                'wilayah_asset' => 'Daop 4 Semarang',
                'status_potensi' => 'potensial',
                'latitude' => -7.151200,
                'longitude' => 111.591500,
            ],
        ];

        foreach ($primeSamples as $sample) {
            KaiAsset::updateOrCreate(
                ['nomor_aset' => $sample['nomor_aset']],
                $sample
            );
        }
    }
}
