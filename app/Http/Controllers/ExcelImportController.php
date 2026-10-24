<?php

namespace App\Http\Controllers;

use App\Models\KaiContract;
use App\Models\KaiAsset;
use App\Models\Tenant;
use App\Models\PaymentRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExcelImportController extends Controller
{
    /**
     * Handle import file CSV / Excel sesuai format new-kai.csv.
     *
     * Kolom yang didukung (case-insensitive, alias fleksibel):
     * Nomor Kontrak | Tanggal Kontrak | Kelompok | Masa Awal | Masa Akhir |
     * Nama Mitra    | Alamat Mitra    | Status Mitra | Peruntukan | Blok Aset |
     * Luas tanah    | Luas bangunan   | Nomor Aset | Jenis Aset | Kmhm |
     * Termin Ke     | Virtual Account | Tanggal Termin | Total Bayar | Invoice ID | Transaction ID
     */
    public function import(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'excel_file' => 'required|file|max:30720',
        ], [
            'excel_file.required' => 'Silakan pilih file CSV / Excel terlebih dahulu.',
            'excel_file.max'      => 'Ukuran file maksimal 30 MB.',
        ]);

        $file = $request->file('excel_file');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['csv', 'txt', 'xlsx', 'xls'])) {
            return back()->with('error', 'Format tidak didukung. Gunakan .csv, .xlsx, atau .xls');
        }

        try {
            $rows = $this->parseRows($file, $ext);

            if (empty($rows)) {
                return back()->with('error', 'File kosong atau tidak memiliki data.');
            }

            // Cari baris header
            $headerIndex = $this->detectHeaderRow($rows);
            for ($i = 0; $i < $headerIndex; $i++) array_shift($rows);
            $headerRow         = array_shift($rows);
            $normalizedHeaders = array_map(fn($h) => $this->normalizeHeader($h), $headerRow);

            // Preload maps ke memory
            $tenantsMap   = Tenant::all()->keyBy(fn($t) => strtolower(trim($t->nama_mitra)));
            $assetsMap    = KaiAsset::all()->keyBy(fn($a) => trim($a->nomor_aset));
            $contractsMap = KaiContract::all()->keyBy(fn($c) => trim($c->nomor_kontrak));

            $imported  = 0;
            $updated   = 0;
            $skipped   = [];
            $seen      = [];

            foreach ($rows as $rowIdx => $row) {
                $excelRow = $rowIdx + 2;

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

                // ── Ambil field utama ────────────────────────────────────
                $nomorKontrak = $this->field($data, ['nomor_kontrak', 'no_kontrak', 'no_perjanjian', 'kontrak']);
                if (!$nomorKontrak) {
                    $nomorKontrak = isset($row[0]) && trim((string)$row[0]) !== '' ? trim((string)$row[0]) : null;
                }
                if (!$nomorKontrak) {
                    $skipped[] = ['row' => $excelRow, 'reason' => 'Nomor Kontrak kosong'];
                    continue;
                }

                // Handle duplikat dalam satu import
                if (!isset($seen[$nomorKontrak])) {
                    $seen[$nomorKontrak] = 1;
                    $targetKontrak = $nomorKontrak;
                } else {
                    $seen[$nomorKontrak]++;
                    $targetKontrak = $nomorKontrak . ' (' . $seen[$nomorKontrak] . ')';
                }

                $namaMitra   = $this->field($data, ['nama_mitra', 'mitra', 'penyewa', 'nama', 'tenant', 'customer']) ?? '-';
                $alamatMitra = $this->field($data, ['alamat_mitra', 'alamat', 'address']);
                $statusMitra = $this->field($data, ['status_mitra', 'status_customer', 'status']) ?? 'Swasta';

                $nomorAset   = $this->field($data, ['nomor_aset', 'no_aset', 'asset_number', 'id_aset']);
                $blokAset    = $this->field($data, ['blok_aset', 'blok', 'lokasi_aset', 'nama_blok']);
                $jenisAset   = $this->field($data, ['jenis_aset', 'jenis_asset', 'tipe_aset']) ?? 'Tanah';
                $peruntukan  = $this->field($data, ['peruntukan']);
                $luasTanah   = $this->parseNumber($this->field($data, ['luas_tanah', 'luas_m2', 'luas'])) ?? 0;
                $luasBangunan= $this->parseNumber($this->field($data, ['luas_bangunan', 'bangunan'])) ?? 0;
                $kmhm        = $this->field($data, ['kmhm', 'km_hm', 'km']);

                $tglKontrak  = $this->field($data, ['tanggal_kontrak', 'tgl_kontrak', 'waktu_kontrak']);
                $kelompok    = $this->field($data, ['kelompok', 'kategori', 'jenis_pendapatan', 'tipe']);
                $masaAwal    = $this->parseDate($this->field($data, ['masa_awal', 'start_datetime', 'awal', 'mulai', 'tgl_mulai']));
                $masaAkhir   = $this->parseDate($this->field($data, ['masa_akhir', 'end_datetime', 'akhir', 'selesai', 'tgl_akhir', 'jatuh_tempo']));
                $nilaiKontrak= $this->parseNumber($this->field($data, ['nilai_kontrak', 'harga', 'nilai', 'tarif'])) ?? 0;

                $terminKe    = (int) ($this->parseNumber($this->field($data, ['termin_ke', 'termin', 'no_termin'])) ?? 1);
                $virtualAcc  = $this->field($data, ['virtual_account', 'va', 'no_va', 'rekening_va']);
                $tglTermin   = $this->parseDate($this->field($data, ['tanggal_termin', 'tgl_termin', 'tgl_bayar']));
                $totalBayar  = $this->parseNumber($this->field($data, ['total_bayar', 'bayar', 'pembayaran', 'nilai_bayar'])) ?? 0;
                $invoiceId   = $this->field($data, ['invoice_id', 'invoice', 'no_invoice']);
                $transId     = $this->field($data, ['transaction_id', 'trx_id', 'id_transaksi']);

                // ── 1. Upsert Tenant ─────────────────────────────────────
                $tenantKey = strtolower(trim($namaMitra));
                if ($tenantsMap->has($tenantKey)) {
                    $tenant = $tenantsMap->get($tenantKey);
                } else {
                    $tenant = Tenant::create([
                        'nama_mitra'   => $namaMitra,
                        'alamat_mitra' => $alamatMitra,
                        'status_mitra' => $statusMitra,
                    ]);
                    $tenantsMap->put($tenantKey, $tenant);
                }

                // ── 2. Upsert Asset ──────────────────────────────────────
                if ($nomorAset) {
                    if (!$assetsMap->has($nomorAset)) {
                        $asset = KaiAsset::create([
                            'nomor_aset'    => $nomorAset,
                            'blok_aset'     => $blokAset,
                            'jenis_aset'    => $jenisAset,
                            'peruntukan'    => $peruntukan,
                            'luas_tanah'    => $luasTanah,
                            'luas_bangunan' => $luasBangunan,
                            'kmhm'          => $kmhm,
                        ]);
                        $assetsMap->put($nomorAset, $asset);
                    }
                }

                // ── 3. Upsert Contract ───────────────────────────────────
                if ($contractsMap->has($targetKontrak)) {
                    $contract = $contractsMap->get($targetKontrak);
                    $contract->update([
                        'tenant_id'      => $tenant->id,
                        'nomor_aset'     => $nomorAset,
                        'tanggal_kontrak'=> $tglKontrak,
                        'kelompok'       => $kelompok,
                        'masa_awal'      => $masaAwal,
                        'masa_akhir'     => $masaAkhir,
                        'nilai_kontrak'  => $nilaiKontrak,
                    ]);
                    $updated++;
                } else {
                    $contract = KaiContract::create([
                        'nomor_kontrak'  => $targetKontrak,
                        'tenant_id'      => $tenant->id,
                        'nomor_aset'     => $nomorAset,
                        'tanggal_kontrak'=> $tglKontrak,
                        'kelompok'       => $kelompok,
                        'masa_awal'      => $masaAwal,
                        'masa_akhir'     => $masaAkhir,
                        'nilai_kontrak'  => $nilaiKontrak,
                    ]);
                    $contractsMap->put($targetKontrak, $contract);
                    $imported++;
                }

                // ── 4. Insert Payment Record ─────────────────────────────
                // Setiap baris CSV = 1 record tagihan (satu termin)
                PaymentRecord::create([
                    'nomor_kontrak'  => $targetKontrak,
                    'termin_ke'      => max(1, $terminKe),
                    'virtual_account'=> $virtualAcc,
                    'tanggal_termin' => $tglTermin,
                    'total_bayar'    => $totalBayar,
                    'invoice_id'     => $invoiceId,
                    'transaction_id' => $transId,
                ]);
            }

            // Invalidasi cache dashboard
            DashboardController::forgetCache();
            ContractController::forgetCache();

            $message = "Import selesai. {$imported} kontrak baru, {$updated} diperbarui.";
            if (!empty($skipped)) {
                $message .= ' ' . count($skipped) . ' baris dilewati.';
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            \Log::error('Import CSV failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Baca baris dari file CSV/XLSX. Mengembalikan array of arrays.
     */
    private function parseRows($file, string $ext): array
    {
        $path = $file->getRealPath();

        if (in_array($ext, ['csv', 'txt'])) {
            $rows = [];
            if (($handle = fopen($path, 'r')) !== false) {
                // Deteksi separator: coma atau semicolon
                $firstLine = fgets($handle);
                rewind($handle);
                $sep = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

                while (($line = fgetcsv($handle, 0, $sep)) !== false) {
                    $rows[] = $line;
                }
                fclose($handle);
            }
            return $rows;
        }

        // XLSX — baca via ZIP XML
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) {
                throw new \RuntimeException('Tidak bisa buka file XLSX.');
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

            // Cari sheet pertama
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if (!$sheetXml) {
                // coba dari workbook.xml.rels
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
                throw new \RuntimeException('Tidak menemukan sheet di file XLSX.');
            }

            $sheet = simplexml_load_string($sheetXml);
            $rows  = [];

            foreach ($sheet->sheetData->row as $row) {
                $rowArr = [];
                $lastColIdx = -1;

                foreach ($row->c as $cell) {
                    // Hitung index kolom dari ref (A=0, B=1, ...)
                    preg_match('/^([A-Z]+)(\d+)$/', (string)$cell['r'], $m);
                    $colIdx = 0;
                    foreach (str_split($m[1]) as $ch) {
                        $colIdx = $colIdx * 26 + (ord($ch) - ord('A') + 1);
                    }
                    $colIdx--;

                    // Isi kolom kosong di antara
                    while ($lastColIdx < $colIdx - 1) {
                        $rowArr[] = null;
                        $lastColIdx++;
                    }

                    $type  = (string)($cell['t'] ?? '');
                    $value = (string)($cell->v ?? '');

                    if ($type === 's') {
                        $value = $sharedStrings[(int)$value] ?? '';
                    } elseif ($type === '' && is_numeric($value) && (float)$value > 30000 && (float)$value < 60000) {
                        // Excel serial date → Y-m-d
                        $value = date('d.m.Y', (int)(((float)$value - 25569) * 86400));
                    }

                    $rowArr[] = $value;
                    $lastColIdx = $colIdx;
                }

                $rows[] = $rowArr;
            }

            return $rows;

        } catch (\Throwable $e) {
            \Log::warning('XLSX parse failed, fallback to CSV: ' . $e->getMessage());
            // Fallback: coba baca sebagai CSV
            $rows = [];
            if (($handle = fopen($path, 'r')) !== false) {
                while (($line = fgetcsv($handle)) !== false) $rows[] = $line;
                fclose($handle);
            }
            return $rows;
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
     * Parse tanggal dari berbagai format:
     * - Excel serial (misal 42710)
     * - DD.MM.YYYY HH:MM:SS   (format CSV new-kai.csv)
     * - DD.MM.YYYY
     * - DD/MM/YYYY
     * - YYYY-MM-DD
     * - Carbon::parse fallback
     *
     * @return string|null format Y-m-d
     */
    private function parseDate(?string $val): ?string
    {
        if ($val === null || trim($val) === '') return null;
        $val = trim($val);

        // Blacklist nilai tidak valid
        if (in_array(strtolower($val), ['kosong', '-', 'null', 'n/a', '0'])) return null;

        // Excel serial date integer
        if (is_numeric($val) && (float)$val > 30000 && (float)$val < 60000) {
            $unix = ((float)$val - 25569) * 86400;
            return date('Y-m-d', (int)$unix);
        }

        // DD.MM.YYYY HH:MM:SS  (format utama new-kai.csv)
        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})/', $val, $m)) {
            $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
            if (checkdate($mo, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
        }

        // DD/MM/YYYY atau DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
            $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
            if ($d > 12) { // pasti DD/MM
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
     * Parse angka dari string dengan format Indonesia (titik sebagai ribuan, koma sebagai desimal).
     * Contoh: "2,623,637" → 2623637.0 | "1.500.000" → 1500000.0 | "42" → 42.0
     */
    private function parseNumber(?string $val): ?float
    {
        if ($val === null || trim($val) === '') return null;
        $v = trim($val);
        $v = preg_replace('/[^\d.,]/', '', $v);
        if ($v === '') return null;

        // Jika ada koma sebagai pemisah ribuan: "2,623,637"
        if (preg_match('/,\d{3}(,\d{3})*$/', $v) || preg_match('/^\d{1,3}(,\d{3})+$/', $v)) {
            $v = str_replace(',', '', $v);
        }
        // Titik sebagai pemisah ribuan: "1.500.000"
        elseif (preg_match('/\.\d{3}(\.\d{3})*$/', $v) || preg_match('/^\d{1,3}(\.\d{3})+$/', $v)) {
            $v = str_replace('.', '', $v);
        }
        // Koma sebagai desimal: "1234,56"
        elseif (str_contains($v, ',') && !str_contains($v, '.')) {
            $v = str_replace(',', '.', $v);
        }

        return is_numeric($v) ? (float)$v : null;
    }
}
