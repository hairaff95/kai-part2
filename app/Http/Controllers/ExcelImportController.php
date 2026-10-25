<?php

namespace App\Http\Controllers;

use App\Models\KaiContract;
use App\Models\KaiAsset;
use App\Models\Tenant;
use App\Models\PaymentRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExcelImportController extends Controller
{
    /**
     * Handle import file Excel (.xlsx, .xls) / CSV dengan performa tinggi (Bulk Processing).
     *
     * Setiap baris dalam file Excel dicatat secara utuh (termasuk kontrak dengan nomor sama
     * yang memiliki beberapa invoice/termin pembayaran), sehingga seluruh baris Excel tertulis
     * di web dan re-import tidak menyebabkan duplikasi melainkan terupdate.
     */
    public function import(Request $request)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '1024M');
        @ini_set('precision', '20');
        @ini_set('serialize_precision', '20');

        $request->validate([
            'excel_file' => 'required|file|max:51200',
        ], [
            'excel_file.required' => 'Silakan pilih file Excel (.xlsx, .xls) atau CSV terlebih dahulu.',
            'excel_file.max'      => 'Ukuran file maksimal 50 MB.',
        ]);

        $file = $request->file('excel_file');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return back()->with('error', 'Format tidak didukung. Harap gunakan file Excel (.xlsx, .xls) atau CSV (.csv).');
        }

        try {
            $rows = $this->parseRows($file, $ext);

            if (empty($rows)) {
                return back()->with('error', 'File Excel / CSV kosong atau tidak memiliki data.');
            }

            // Cari baris header
            $headerIndex = $this->detectHeaderRow($rows);
            for ($i = 0; $i < $headerIndex; $i++) {
                array_shift($rows);
            }
            $headerRow         = array_shift($rows);
            $normalizedHeaders = array_map(fn($h) => $this->normalizeHeader($h), $headerRow);

            // Inisialisasi struktur data in-memory
            $uniqueTenantsData = []; // [tenantKey => ['nama_mitra' => ..., ...]]
            $uniqueAssetsData  = []; // [nomorAset => ['nomor_aset' => ..., ...]]
            $contractsData     = []; // [targetKontrak => [...]]
            $paymentsData      = []; // [ [...], [...] ]
            $skipped           = [];
            $seen              = []; // Tracking nomor kontrak berulang agar seluruh baris tertulis

            // ── FASE 1: Parse semua baris dalam memory (0 query database) ─
            foreach ($rows as $rowIdx => $row) {
                $excelRow = $rowIdx + $headerIndex + 2;

                // Lewati baris kosong
                if (empty(array_filter($row, fn($v) => $v !== null && trim((string)$v) !== ''))) {
                    continue;
                }

                // Map header → value
                $data = [];
                foreach ($normalizedHeaders as $idx => $key) {
                    if ($key !== '') {
                        $data[$key] = isset($row[$idx]) ? trim((string)$row[$idx]) : null;
                    }
                }

                // Ambil nomor kontrak
                $nomorKontrak = $this->field($data, [
                    'nomor_kontrak', 'no_kontrak', 'no_perjanjian', 'kontrak',
                    'no_spk', 'nomor_perjanjian'
                ]);
                if (!$nomorKontrak) {
                    $nomorKontrak = isset($row[0]) && trim((string)$row[0]) !== '' ? trim((string)$row[0]) : null;
                }

                if (!$nomorKontrak || str_starts_with($nomorKontrak, '=')) {
                    $skipped[] = ['row' => $excelRow, 'reason' => 'Nomor Kontrak kosong atau tidak valid'];
                    continue;
                }

                $baseKontrak = trim($nomorKontrak);

                // Jika nomor kontrak muncul beberapa kali (misal karena beberapa invoice/termin),
                // setiap baris tetap dicatat secara utuh di web dengan penomoran unik deterministik
                if (!isset($seen[$baseKontrak])) {
                    $seen[$baseKontrak] = 1;
                    $targetKontrak = $baseKontrak;
                } else {
                    $seen[$baseKontrak]++;
                    $targetKontrak = $baseKontrak . ' (' . $seen[$baseKontrak] . ')';
                }

                // Data Mitra
                $namaMitra = $this->field($data, ['nama_mitra', 'mitra', 'penyewa', 'nama', 'tenant', 'customer', 'nama_penyewa', 'nama_customer']) ?? '-';
                if (str_starts_with($namaMitra, '=')) $namaMitra = '-';
                $alamatMitra = $this->field($data, ['alamat_mitra', 'alamat', 'address', 'domisili']);
                if ($alamatMitra && str_starts_with($alamatMitra, '=')) $alamatMitra = null;
                $statusMitra = $this->field($data, ['status_mitra', 'status_customer', 'status', 'tipe_mitra']) ?? 'Swasta';
                if (str_starts_with($statusMitra, '=')) $statusMitra = 'Swasta';

                $tenantKey = strtolower(trim($namaMitra));
                if (!isset($uniqueTenantsData[$tenantKey])) {
                    $uniqueTenantsData[$tenantKey] = [
                        'nama_mitra'   => $namaMitra,
                        'alamat_mitra' => $alamatMitra,
                        'status_mitra' => $statusMitra,
                    ];
                }

                // Data Aset
                $nomorAset = $this->field($data, ['nomor_aset', 'no_aset', 'asset_number', 'id_aset', 'kode_aset']);
                if ($nomorAset && str_starts_with($nomorAset, '=')) $nomorAset = null;
                $blokAset  = $this->field($data, ['blok_aset', 'blok', 'lokasi_aset', 'nama_blok', 'lokasi', 'alamat_aset']);
                $jenisAset = $this->field($data, ['jenis_aset', 'jenis_asset', 'tipe_aset', 'aset']) ?? 'Tanah';
                $peruntukan= $this->field($data, ['peruntukan', 'kegunaan', 'fungsi']);
                $luasTanah = $this->parseNumber($this->field($data, ['luas_tanah', 'luas_m2', 'luas', 'luas_bumi'])) ?? 0;
                $luasBangunan = $this->parseNumber($this->field($data, ['luas_bangunan', 'bangunan', 'luas_gedung'])) ?? 0;
                $kmhm      = $this->field($data, ['kmhm', 'km_hm', 'km', 'lokasi_km']);

                if ($nomorAset && !isset($uniqueAssetsData[$nomorAset])) {
                    $uniqueAssetsData[$nomorAset] = [
                        'nomor_aset'    => $nomorAset,
                        'blok_aset'     => $blokAset,
                        'jenis_aset'    => $jenisAset,
                        'peruntukan'    => $peruntukan,
                        'luas_tanah'    => $luasTanah,
                        'luas_bangunan' => $luasBangunan,
                        'kmhm'          => $kmhm,
                    ];
                }

                // Data Kontrak
                $tglKontrak  = $this->field($data, ['tanggal_kontrak', 'tgl_kontrak', 'waktu_kontrak', 'tgl_perjanjian']);
                $kelompok    = $this->field($data, ['kelompok', 'kategori', 'jenis_pendapatan', 'tipe', 'kelompok_aset']);
                $masaAwal    = $this->parseDate($this->field($data, ['masa_awal', 'start_datetime', 'awal', 'mulai', 'tgl_mulai', 'tanggal_mulai', 'periode_awal']));
                $masaAkhir   = $this->parseDate($this->field($data, ['masa_akhir', 'end_datetime', 'akhir', 'selesai', 'tgl_akhir', 'tanggal_akhir', 'jatuh_tempo', 'tgl_jatuh_tempo', 'periode_akhir']));
                $nilaiKontrak= $this->parseNumber($this->field($data, ['nilai_kontrak', 'harga', 'nilai', 'tarif', 'total_kontrak', 'nominal_kontrak', 'nilai_perjanjian'])) ?? 0;

                // Data Tagihan / Invoice
                $rawTermin   = $this->parseNumber($this->field($data, ['termin_ke', 'termin', 'no_termin', 'ke', 'termin_pembayaran', 'angsuran_ke']));
                $terminKe    = $rawTermin !== null ? (int)$rawTermin : 0;
                $virtualAcc  = $this->cleanIdNumber($this->field($data, ['virtual_account', 'va', 'no_va', 'rekening_va', 'nomor_va']));
                $tglTermin   = $this->parseDate($this->field($data, ['tanggal_termin', 'tgl_termin', 'tgl_bayar', 'tanggal_bayar', 'tgl_invoice', 'tanggal_invoice', 'tgl_tagihan']));
                $totalBayar  = $this->parseNumber($this->field($data, ['total_bayar', 'bayar', 'pembayaran', 'nilai_bayar', 'jumlah_bayar', 'nominal_bayar', 'nominal', 'amount', 'nilai_tagihan', 'total_tagihan'])) ?? 0;
                $invoiceId   = $this->cleanIdNumber($this->field($data, ['invoice_id', 'invoice', 'no_invoice', 'nomor_invoice', 'id_invoice']));
                $transId     = $this->cleanIdNumber($this->field($data, ['transaction_id', 'trx_id', 'id_transaksi', 'no_transaksi', 'id_trans']));

                $actualTermin = $terminKe > 0 ? $terminKe : $seen[$baseKontrak];

                // Nilai kontrak: jika kolom nilai kontrak 0/kosong pada baris ini, gunakan total_bayar baris tersebut
                $finalNilai = $nilaiKontrak > 0 ? $nilaiKontrak : $totalBayar;

                $contractsData[$targetKontrak] = [
                    'nomor_kontrak'  => $targetKontrak,
                    'tenant_key'     => $tenantKey,
                    'nomor_aset'     => $nomorAset,
                    'tanggal_kontrak'=> $tglKontrak,
                    'kelompok'       => $kelompok,
                    'masa_awal'      => $masaAwal,
                    'masa_akhir'     => $masaAkhir,
                    'nilai_kontrak'  => $finalNilai,
                ];

                $paymentsData[] = [
                    'nomor_kontrak'      => $targetKontrak,
                    'base_nomor_kontrak' => $baseKontrak,
                    'termin_ke'          => max(1, $actualTermin),
                    'virtual_account'    => $virtualAcc,
                    'tanggal_termin'     => $tglTermin,
                    'total_bayar'        => $totalBayar,
                    'invoice_id'         => $invoiceId,
                    'transaction_id'     => $transId,
                ];
            }

            // ── FASE 2: Eksekusi Bulk Database (Sangat Cepat & Anti-Duplikasi) ─

            // 1. Bulk Tenants
            $existingTenants = Tenant::all()->keyBy(fn($t) => strtolower(trim($t->nama_mitra)));
            $newTenants = [];
            foreach ($uniqueTenantsData as $key => $tData) {
                if (!$existingTenants->has($key)) {
                    $newTenants[] = $tData;
                }
            }
            if (!empty($newTenants)) {
                foreach (array_chunk($newTenants, 500) as $chunk) {
                    DB::table('tenants')->insert($chunk);
                }
                $existingTenants = Tenant::all()->keyBy(fn($t) => strtolower(trim($t->nama_mitra)));
            }

            // 2. Bulk Assets
            $existingAssets = KaiAsset::all()->keyBy(fn($a) => trim($a->nomor_aset));
            $newAssets = [];
            foreach ($uniqueAssetsData as $num => $aData) {
                if (!$existingAssets->has($num)) {
                    $newAssets[] = $aData;
                }
            }
            if (!empty($newAssets)) {
                foreach (array_chunk($newAssets, 500) as $chunk) {
                    DB::table('assets')->insertOrIgnore($chunk);
                }
            }

            // 3. Bulk Upsert Contracts (Seluruh 2523 baris dicatat, re-import terupdate)
            $existingContractsInDb = KaiContract::all()->keyBy('nomor_kontrak');
            $contractsPayload = [];
            $contractsCreated = 0;
            $contractsUpdated = 0;

            foreach ($contractsData as $c) {
                $cNum = $c['nomor_kontrak'];
                $tenantId = $existingTenants->get($c['tenant_key'])?->id;

                if ($existingContractsInDb->has($cNum)) {
                    $contractsUpdated++;
                } else {
                    $contractsCreated++;
                }

                $contractsPayload[] = [
                    'nomor_kontrak'   => $cNum,
                    'tenant_id'       => $tenantId,
                    'nomor_aset'      => $c['nomor_aset'],
                    'tanggal_kontrak' => $c['tanggal_kontrak'],
                    'kelompok'        => $c['kelompok'],
                    'masa_awal'       => $c['masa_awal'],
                    'masa_akhir'      => $c['masa_akhir'],
                    'nilai_kontrak'   => $c['nilai_kontrak'],
                    'created_at'      => now(),
                ];
            }

            if (!empty($contractsPayload)) {
                foreach (array_chunk($contractsPayload, 500) as $chunk) {
                    DB::table('contracts')->upsert(
                        $chunk,
                        ['nomor_kontrak'],
                        ['tenant_id', 'nomor_aset', 'tanggal_kontrak', 'kelompok', 'masa_awal', 'masa_akhir', 'nilai_kontrak']
                    );
                }
            }

            // 4. Bulk Upsert Payments (Anti-Duplikasi dengan pencocokan cerdas)
            $existingPayments = PaymentRecord::all();
            $byTargetKontrak  = $existingPayments->groupBy('nomor_kontrak');
            $byInvoiceId      = $existingPayments->whereNotNull('invoice_id')->where('invoice_id', '!=', '')->keyBy('invoice_id');
            $byTransactionId  = $existingPayments->whereNotNull('transaction_id')->where('transaction_id', '!=', '')->keyBy('transaction_id');
            $byBaseTermin     = $existingPayments->groupBy(fn($p) => $p->nomor_kontrak . '___' . $p->termin_ke);

            $claimedIds       = [];
            $paymentsToInsert = [];
            $paymentsToUpdate = [];
            $paymentsCreated  = 0;
            $paymentsUpdated  = 0;

            foreach ($paymentsData as $pay) {
                $targetKontrak = $pay['nomor_kontrak'];
                $baseKontrak   = $pay['base_nomor_kontrak'];
                $termin        = $pay['termin_ke'];
                $invId         = $pay['invoice_id'];
                $trxId         = $pay['transaction_id'];

                $existing = null;

                // 1. Cocokkan target kontrak yang sama persis
                if ($byTargetKontrak->has($targetKontrak)) {
                    $cand = $byTargetKontrak->get($targetKontrak)->first(fn($p) => !isset($claimedIds[$p->id]));
                    if ($cand) $existing = $cand;
                }

                // 2. Cocokkan invoice_id unik
                if (!$existing && $invId && $byInvoiceId->has($invId)) {
                    $cand = $byInvoiceId->get($invId);
                    if ($cand && !isset($claimedIds[$cand->id])) $existing = $cand;
                }

                // 3. Cocokkan transaction_id unik
                if (!$existing && $trxId && $byTransactionId->has($trxId)) {
                    $cand = $byTransactionId->get($trxId);
                    if ($cand && !isset($claimedIds[$cand->id])) $existing = $cand;
                }

                // 4. Cocokkan base kontrak + termin (untuk data lama yang belum ber-suffix)
                if (!$existing) {
                    $key = $baseKontrak . '___' . $termin;
                    if ($byBaseTermin->has($key)) {
                        $cand = $byBaseTermin->get($key)->first(fn($p) => !isset($claimedIds[$p->id]));
                        if ($cand) $existing = $cand;
                    }
                }

                unset($pay['base_nomor_kontrak']);

                if ($existing) {
                    $claimedIds[$existing->id] = true;
                    $pay['id'] = $existing->id;
                    $paymentsToUpdate[] = $pay;
                    $paymentsUpdated++;
                } else {
                    $pay['created_at'] = now();
                    $paymentsToInsert[] = $pay;
                    $paymentsCreated++;
                }
            }

            if (!empty($paymentsToInsert)) {
                foreach (array_chunk($paymentsToInsert, 500) as $chunk) {
                    DB::table('payment_records')->insert($chunk);
                }
            }

            if (!empty($paymentsToUpdate)) {
                foreach (array_chunk($paymentsToUpdate, 500) as $chunk) {
                    DB::table('payment_records')->upsert(
                        $chunk,
                        ['id'],
                        ['nomor_kontrak', 'termin_ke', 'virtual_account', 'tanggal_termin', 'total_bayar', 'invoice_id', 'transaction_id']
                    );
                }
            }

            // Invalidate Cache
            DashboardController::forgetCache();
            ContractController::forgetCache();
            Cache::forget('dropdown_kelompok');
            Cache::forget('dropdown_status_mitra');

            $totalContracts = $contractsCreated + $contractsUpdated;
            $totalPayments  = $paymentsCreated + $paymentsUpdated;

            $message = "Import berhasil: {$totalContracts} kontrak tertulis di web ({$contractsCreated} baru, {$contractsUpdated} diperbarui), {$totalPayments} data tagihan diproses.";
            if (!empty($skipped)) {
                $message .= ' (' . count($skipped) . ' baris dilewati karena format tidak lengkap)';
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            Log::error('Import Excel/CSV failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Bersihkan angka ID/VA yang mungkin berformat notasi ilmiah dari Excel (e.g. 1.2188E+14).
     */
    private function cleanIdNumber(?string $val): ?string
    {
        if ($val === null || trim($val) === '') return null;
        $val = trim($val);
        if (is_numeric($val) && (stripos($val, 'e+') !== false || stripos($val, 'e-') !== false)) {
            return number_format((float)$val, 0, '', '');
        }
        return $val;
    }

    /**
     * Baca baris dari file Excel (.xlsx, .xls) atau CSV (.csv, .txt).
     *
     * @return array of arrays
     */
    private function parseRows($file, string $ext): array
    {
        $path = $file->getRealPath();

        // 1. Ekstensi CSV / TXT -> fgetcsv (cepat & hemat memori)
        if (in_array($ext, ['csv', 'txt'])) {
            $rows = [];
            if (($handle = fopen($path, 'r')) !== false) {
                $firstLine = fgets($handle);
                rewind($handle);
                $sep = ',';
                if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                    $sep = ';';
                } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                    $sep = "\t";
                }

                while (($line = fgetcsv($handle, 0, $sep)) !== false) {
                    $rows[] = $line;
                }
                fclose($handle);
            }
            return $rows;
        }

        // 2. Ekstensi Excel (.xlsx, .xls) -> PhpSpreadsheet dengan evaluasi formula
        if ($ext === 'xlsx' || $ext === 'xls') {
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($path);
                $sheet = $spreadsheet->getActiveSheet();

                $rows = [];
                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $val = null;
                        if ($cell->isFormula()) {
                            $val = $cell->getOldCalculatedValue();
                            if ($val === null || $val === '#REF!' || $val === '#VALUE!' || $val === '#N/A') {
                                try {
                                    $val = $cell->getCalculatedValue();
                                } catch (\Throwable $e) {
                                    $val = $cell->getValue();
                                }
                            }
                        } else {
                            $val = $cell->getValue();
                        }

                        // Format tanggal jika sel berupa Excel date
                        if ($val !== null && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                            try {
                                $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                                $val = $dateObj->format('Y-m-d');
                            } catch (\Throwable $e) {
                                // fallback
                            }
                        }
                        if (is_float($val) && floor($val) == $val) {
                            $val = number_format($val, 0, '', '');
                        }
                        $cells[] = $val !== null ? (string)$val : null;
                    }
                    $rows[] = $cells;
                }

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                return $rows;
            } catch (\Throwable $e) {
                Log::warning('PhpSpreadsheet parse failed, falling back to Zip/XML: ' . $e->getMessage());
            }
        }

        // 3. Fallback manual untuk file XLSX via ZipArchive
        if ($ext === 'xlsx') {
            return $this->parseXlsxXmlFallback($path);
        }

        return [];
    }

    /**
     * Fallback manual XLSX reader via ZipArchive.
     */
    private function parseXlsxXmlFallback(string $path): array
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) {
                throw new \RuntimeException('Tidak bisa membuka arsip file XLSX.');
            }

            $sharedStrings = [];
            $ssXml = $zip->getFromName('xl/sharedStrings.xml');
            if ($ssXml) {
                $ss = simplexml_load_string($ssXml);
                foreach ($ss->si as $si) {
                    $text = '';
                    foreach ($si->r as $r) { $text .= (string) $r->t; }
                    if ($text === '') { $text = (string) $si->t; }
                    $sharedStrings[] = $text;
                }
            }

            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if (!$sheetXml) {
                $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
                if ($relsXml) {
                    $rels = simplexml_load_string($relsXml);
                    foreach ($rels->Relationship as $rel) {
                        if (str_contains((string)$rel['Target'], 'sheet')) {
                            $sheetXml = $zip->getFromName('xl/' . (string)$rel['Target']);
                            if ($sheetXml) break;
                        }
                    }
                }
            }
            $zip->close();

            if (!$sheetXml) {
                throw new \RuntimeException('Tidak menemukan worksheet pada file XLSX.');
            }

            $sheet = simplexml_load_string($sheetXml);
            $rows  = [];

            foreach ($sheet->sheetData->row as $row) {
                $rowArr = [];
                $lastColIdx = -1;

                foreach ($row->c as $cell) {
                    preg_match('/^([A-Z]+)(\d+)$/', (string)$cell['r'], $m);
                    $colIdx = 0;
                    if (isset($m[1])) {
                        foreach (str_split($m[1]) as $ch) {
                            $colIdx = $colIdx * 26 + (ord($ch) - ord('A') + 1);
                        }
                        $colIdx--;
                    }

                    while ($lastColIdx < $colIdx - 1) {
                        $rowArr[] = null;
                        $lastColIdx++;
                    }

                    $type  = (string)($cell['t'] ?? '');
                    $value = (string)($cell->v ?? '');

                    if ($type === 's') {
                        $value = $sharedStrings[(int)$value] ?? '';
                    } elseif ($type === '' && is_numeric($value) && (float)$value > 30000 && (float)$value < 60000) {
                        $value = date('Y-m-d', (int)(((float)$value - 25569) * 86400));
                    }

                    $rowArr[] = $value;
                    $lastColIdx = $colIdx;
                }

                $rows[] = $rowArr;
            }

            return $rows;
        } catch (\Throwable $e) {
            Log::error('XLSX fallback failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Deteksi index baris header berdasarkan keyword.
     */
    private function detectHeaderRow(array $rows): int
    {
        foreach ($rows as $i => $row) {
            if (empty(array_filter($row, fn($v) => $v !== null && trim((string)$v) !== ''))) continue;

            $matched = 0;
            foreach ($row as $cell) {
                $n = $this->normalizeHeader($cell);
                if (in_array($n, [
                    'nomor_kontrak','no_kontrak','kelompok','masa_awal','masa_akhir',
                    'nama_mitra','nomor_aset','jenis_aset','termin_ke','virtual_account',
                    'invoice_id','total_bayar','tanggal_termin','transaction_id',
                ])) {
                    $matched++;
                }
            }
            if ($matched >= 2) return $i;
        }
        return 0;
    }

    /**
     * Normalisasi nama header: lowercase, spasi & karakter khusus → underscore.
     */
    private function normalizeHeader($val): string
    {
        if ($val === null) return '';
        $s = strtolower(trim((string)$val));
        $s = preg_replace('/[\s\-\/\(\)\.]+/', '_', $s);
        $s = preg_replace('/_+/', '_', $s);
        return trim($s, '_');
    }

    /**
     * Ambil nilai field dari data dengan beberapa alias.
     */
    private function field(array $data, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            if (isset($data[$alias]) && $data[$alias] !== null && trim($data[$alias]) !== '') {
                return trim($data[$alias]);
            }
        }
        return null;
    }

    /**
     * Parse tanggal dari berbagai format (Excel serial date, DD.MM.YYYY, DD/MM/YYYY, YYYY-MM-DD).
     */
    private function parseDate(?string $val): ?string
    {
        if ($val === null || trim($val) === '') return null;
        $val = trim($val);

        if (in_array(strtolower($val), ['kosong', '-', 'null', 'n/a', '0'])) return null;

        // Excel serial date integer
        if (is_numeric($val) && (float)$val > 30000 && (float)$val < 60000) {
            $unix = ((float)$val - 25569) * 86400;
            return date('Y-m-d', (int)$unix);
        }

        // DD.MM.YYYY HH:MM:SS
        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})/', $val, $m)) {
            $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
            if (checkdate($mo, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
        }

        // DD/MM/YYYY atau DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
            $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
            if ($d > 12) {
                if (checkdate($mo, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
            if (checkdate($mo, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }

        // YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $val, $m)) {
            if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) return $val;
        }

        // Carbon fallback
        try {
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Parse angka dari string dengan format Indonesia atau internasional.
     */
    private function parseNumber(?string $val): ?float
    {
        if ($val === null || trim($val) === '') return null;
        $v = trim($val);
        $v = preg_replace('/[^\d.,]/', '', $v);
        if ($v === '') return null;

        if (preg_match('/,\d{3}(,\d{3})*$/', $v) || preg_match('/^\d{1,3}(,\d{3})+$/', $v)) {
            $v = str_replace(',', '', $v);
        } elseif (preg_match('/\.\d{3}(\.\d{3})*$/', $v) || preg_match('/^\d{1,3}(\.\d{3})+$/', $v)) {
            $v = str_replace('.', '', $v);
        } elseif (str_contains($v, ',') && !str_contains($v, '.')) {
            $v = str_replace(',', '.', $v);
        }

        return is_numeric($v) ? (float)$v : null;
    }
}