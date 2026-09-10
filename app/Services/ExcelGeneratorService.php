<?php

namespace App\Services;

use App\Models\Perdin;
use App\Models\PerdinSheetTemplate;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Carbon;

class ExcelGeneratorService
{
    public function __construct(
        protected TemplateService $templates,
        protected PlaceholderTemplateService $placeholders,
    ) {}

    protected function sheetText(string $key, string $field, Perdin $perdin, string $fallback): string
    {
        $template = PerdinSheetTemplate::where('key', $key)->first();
        $raw = $template?->{$field};

        if (! $raw) {
            return $fallback;
        }

        return $this->placeholders->render($raw, $perdin);
    }
    /**
     * Isi seluruh workbook (6 sheet) berdasarkan data Perdin, lalu simpan
     * ke storage/app/generated dan kembalikan path file hasil.
     */
    public function generate(Perdin $perdin): string
    {
        [$spreadsheet, $workingPath] = $this->templates->loadWorkingCopy();

        $this->fillPertanggungJawaban($spreadsheet, $perdin);
        $this->fillPpa($spreadsheet, $perdin);
        $this->fillKwitansi($spreadsheet, $perdin);
        $this->fillRincian($spreadsheet, $perdin);
        $this->fillDpr($spreadsheet, $perdin);
        $this->fillPernyataan($spreadsheet, $perdin);

        $filename = $this->stableFilename($perdin);
        $outputPath = $this->templates->saveGenerated($spreadsheet, $filename);

        @unlink($workingPath); // file kerja sementara sudah tidak dibutuhkan

        $perdin->update([
            'generated_excel_path' => 'generated/' . $filename,
            'status' => 'generated',
        ]);

        return $outputPath;
    }

    /**
     * Nama file stabil: PERDIN_<slug tujuan>_<tanggal>_<id>.xlsx
     * "Stabil" artinya generate ulang untuk record yang sama menghasilkan
     * nama file yang konsisten (memudahkan tracking), bukan acak setiap klik.
     */
    protected function stableFilename(Perdin $perdin): string
    {
        // Gunakan format sederhana: "Perdin (Nama).xlsx"
        $name = $perdin->nama_bepergian ?: $perdin->travelers->first()?->nama ?: 'perdin';
        // Hapus karakter yang tidak valid di nama file
        $clean = preg_replace('/[\\\\\/\:\*\?\"\<\>\|]/u', '', (string) $name);
        $clean = mb_substr(trim($clean), 0, 60);

        return "Perdin " . $clean . ".xlsx";
    }

    // -----------------------------------------------------------------
    // SHEET: pertanggung jawaban PERDIN
    // -----------------------------------------------------------------
    protected function fillPertanggungJawaban(Spreadsheet $spreadsheet, Perdin $perdin): void
    {
        $cfg = config('perdin.pertanggung_jawaban');
        $sheet = $spreadsheet->getSheetByName($cfg['sheet']);

        if (! $sheet) {
            return;
        }

        $kotaTanggal = $this->formatKotaTanggal($perdin);

        $this->writeFields($sheet, $cfg, [
            'nomor'               => $perdin->nomor,
            'hal' => $this->sheetText('pertanggung_jawaban', 'teks_awalan', $perdin, 'Pertanggungjawaban Perjalanan Dinas'),
            'maksud_perjalanan'   => $perdin->maksud_perjalanan,
            'surat_tugas_jabatan' => $perdin->surat_tugas_jabatan,
            'nomor_st'            => $perdin->nomor_st,
            'tanggal_st'          => $this->formatTanggal($perdin->tanggal_st),
            'nomor_rk'            => $perdin->nomor_rk,
            'pembebanan_anggaran' => $perdin->pembebanan_anggaran,
            'kota_tanggal_ttd_top' => $kotaTanggal,
            'kota_tanggal_ttd_1'  => $kotaTanggal,
            'kota_tanggal_ttd_2'  => $kotaTanggal,
            'kota_tanggal_ttd_3'  => $kotaTanggal,
            'kota_tanggal_ttd_4'  => $kotaTanggal,
            'nama_ppk'            => $perdin->nama_ppk,
            'nip_ppk'             => $this->formatNip($perdin->nip_ppk),
            'nama_kabag_keuangan' => $perdin->nama_kabag_keuangan,
            'nip_kabag_keuangan'  => $this->formatNip($perdin->nip_kabag_keuangan),
            'nama_mengetahui'     => $perdin->nama_mengetahui,
            'nama_pengaju'        => $perdin->nama_pengaju,
        ]);

        $this->fillTravelerTable($sheet, $cfg['table'], $perdin);
    }

    // -----------------------------------------------------------------
    // SHEET: PPA Perdin
    // -----------------------------------------------------------------
    protected function fillPpa(Spreadsheet $spreadsheet, Perdin $perdin): void
    {
        $cfg = config('perdin.ppa');
        $sheet = $spreadsheet->getSheetByName($cfg['sheet']);

        if (! $sheet) {
            return;
        }

        $kotaTanggal = $this->formatKotaTanggal($perdin);

        $this->writeFields($sheet, $cfg, [
            'nomor'               => $perdin->nomor,
            'hal' => $this->sheetText('ppa', 'teks_awalan', $perdin, 'Permohonan Pembebanan Anggaran'),
            'maksud_perjalanan'   => $perdin->maksud_perjalanan,
            'surat_tugas_jabatan' => $perdin->surat_tugas_jabatan,
            'nomor_st'            => $perdin->nomor_st,
            'tanggal_st'          => $this->formatTanggal($perdin->tanggal_st),
            'nomor_rk'            => $perdin->nomor_rk,
            'pembebanan_anggaran' => $perdin->pembebanan_anggaran,
            'kota_tanggal_ttd_top' => $kotaTanggal,
            'kota_tanggal_ttd_1'  => $kotaTanggal,
            'kota_tanggal_ttd_2'  => $kotaTanggal,
            'kota_tanggal_ttd_3'  => $kotaTanggal,
            'kota_tanggal_ttd_4'  => $kotaTanggal,
            'nama_ppk'            => $perdin->nama_ppk,
            'nip_ppk'             => $this->formatNip($perdin->nip_ppk),
            'nama_kabag_keuangan' => $perdin->nama_kabag_keuangan,
            'nip_kabag_keuangan'  => $this->formatNip($perdin->nip_kabag_keuangan),
            'nama_mengetahui'     => $perdin->nama_mengetahui,
            'nama_pengaju'        => $perdin->nama_pengaju,
            'nama_pengaju_ppa'    => $perdin->nama_pengaju_ppa,
            'nip_pengaju_ppa'     => $this->formatNip($perdin->nip_pengaju_ppa),
        ]);

        $this->fillTravelerTable($sheet, $cfg['table'], $perdin);
    }

    // -----------------------------------------------------------------
    // SHEET: Kwit PERDIN 1
    // -----------------------------------------------------------------
    protected function fillKwitansi(Spreadsheet $spreadsheet, Perdin $perdin): void
    {
        $cfg = config('perdin.kwitansi');
        $sheet = $spreadsheet->getSheetByName($cfg['sheet']);

        if (! $sheet) {
            return;
        }

        $jumlah = (int) ($perdin->jumlah_uang_kwitansi ?: $perdin->total_biaya);
        $pemohon = $perdin->travelers->first();

        $this->writeFields($sheet, $cfg, [
            'tahun_anggaran'      => $perdin->tahun_anggaran ?: now()->year,
            'nomor_bukti'         => $perdin->nomor_bukti_kwitansi,
            'mak'                 => $perdin->mak,
            'sudah_terima_dari'   => $perdin->sudah_terima_dari,
            'jumlah_uang'         => $jumlah,
            'terbilang'           => ucfirst(TerbilangService::rupiah($jumlah)),
            'untuk_pembayaran'    => $perdin->untuk_pembayaran ?: $this->sheetText('kwitansi', 'teks_awalan', $perdin, (string) $perdin->maksud_perjalanan),
            // Tanggal "Yang bepergian" & "Dibayar lunas" sekarang punya
            // kota/bulan/tahun sendiri-sendiri (tidak lagi ikut kota/tanggal
            // tanda tangan yang dipakai sheet lain).
            'kota_tanggal_bepergian' => $this->formatKotaTanggalCustom($perdin->kota_bepergian, $perdin->tanggal_bepergian),
            'kota_tanggal_lunas'     => $this->formatKotaTanggalCustom($perdin->kota_lunas, $perdin->tanggal_lunas, 'Dibayar lunas, Tgl '),
            'nama_yang_bepergian' => $perdin->nama_bepergian ?: $pemohon?->nama,
            'nama_ppk'            => $perdin->nama_ppk,
            'nip_ppk'             => $this->formatNip($perdin->nip_ppk),
            'nama_bendahara'      => $perdin->nama_bendahara,
            'nip_bendahara'       => $this->formatNip($perdin->nip_bendahara),
        ]);
    }

    // -----------------------------------------------------------------
    // SHEET: Rincian PERDIN 1
    // -----------------------------------------------------------------
    protected function fillRincian(Spreadsheet $spreadsheet, Perdin $perdin): void
    {
        $cfg = config('perdin.rincian');
        $sheet = $spreadsheet->getSheetByName($cfg['sheet']);

        if (! $sheet) {
            return;
        }

        $this->writeFields($sheet, $cfg, [
            'lampiran_sppd_no' => $perdin->lampiran_sppd_no,
            'tanggal_sppd'     => $this->formatTanggal($perdin->tanggal_sppd),
            'kota_tanggal_ttd' => $this->formatKotaTanggal($perdin),
            'telah_menerima_uang' => '=+F29',
            'nama_bendahara'   => $perdin->nama_bendahara,
            'nip_bendahara'    => $this->formatNip($perdin->nip_bendahara),
            'nama_bepergian'   => $perdin->nama_bepergian ?: $perdin->travelers->first()?->nama,
            'nama_mengetahui_rincian' => $perdin->nama_mengetahui_rincian,
            'nip_mengetahui_rincian'  => $this->formatNip($perdin->nip_mengetahui_rincian),
        ]);

        $table = $cfg['table'];
        $rows = $perdin->rincianItems;
        $startRow = $this->ensureRowCapacity($sheet, $table, $rows->count())['start_row'];

        foreach ($rows as $index => $item) {
            $row = $startRow + $index;
            $columns = $table['columns'];

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['uraian'] . $row, $item->uraian);
            $sheet->setCellValue($columns['keterangan_tambahan'] . $row, $item->keterangan_tambahan);
            $jumlah_satuan = $item->jumlah_satuan ? (int) $item->jumlah_satuan : 0;
            $harga_satuan = $item->harga_satuan ? (float) $item->harga_satuan : 0.0;
            $sheet->setCellValue($columns['jumlah_satuan'] . $row, $jumlah_satuan);
            $sheet->setCellValue($columns['harga_satuan'] . $row, $harga_satuan);
            // Tulis hasil akhir (jumlah_satuan * harga_satuan) sebagai angka,
            // bukan formula, supaya file Excel berisi nilai akhir.
            $sheet->setCellValue($columns['jumlah'] . $row, $jumlah_satuan * $harga_satuan);
            $sheet->setCellValue($columns['keterangan'] . $row, $item->keterangan);
        }
    }

    // -----------------------------------------------------------------
    // SHEET: DPR PERDIN
    // -----------------------------------------------------------------
    protected function fillDpr(Spreadsheet $spreadsheet, Perdin $perdin): void
    {
        $cfg = config('perdin.dpr');
        $sheet = $spreadsheet->getSheetByName($cfg['sheet']);

        if (! $sheet) {
            return;
        }

        $this->writeFields($sheet, $cfg, [
            'nama'             => $perdin->dpr_nama,
            // Cell F12 udah punya label "NIP" terpisah di B12, jadi NIP-nya
            // ditulis polos aja (gak pake prefix "NIP." dari formatNip(),
            // biar gak dobel jadi "NIP : NIP. ...").
            'nip'              => $perdin->dpr_nip,
            'jabatan'          => $perdin->dpr_jabatan,
            'tanggal_spd_text' => $this->sheetText('dpr', 'teks_awalan', $perdin, 'Berdasarkan Surat Perjalanan Dinas (SPD) tanggal ' . $this->formatTanggal($perdin->tanggal_spd, 'd F Y')),
            'nomor_spd'        => $this->sheetText('dpr', 'teks_penutup', $perdin, 'Nomor : ' . $perdin->nomor_spd . ', dengan ini kami menyatakan dengan sesungguhnya bahwa:'),
            'kota_tanggal_ttd' => $this->formatKotaTanggal($perdin),
            'nama_ppk'         => $perdin->nama_ppk,
            'nip_ppk'          => $this->formatNip($perdin->nip_ppk),
            'nama_bepergian'   => $perdin->nama_bepergian ?: $perdin->travelers->first()?->nama,
        ]);

        $table = $cfg['table'];
        $rows = $perdin->dprItems;
        $capacity = $this->ensureRowCapacity($sheet, $table, $rows->count());
        $startRow = $capacity['start_row'];
        $totalRow = $capacity['total_row'];

        $totalJumlah = 0.0;

        foreach ($rows as $index => $item) {
            $row = $startRow + $index;
            $columns = $table['columns'];

            $jumlah = $item->jumlah ? (float) $item->jumlah : 0.0;

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['uraian'] . $row, $item->uraian);
            $sheet->setCellValue($columns['jumlah'] . $row, $jumlah);

            $totalJumlah += $jumlah;
        }

        // G34 (baris "JUMLAH", posisi bisa turun kalau baris disisipkan)
        // gak ada formula SUM di template, jadi ditulis manual dari total
        // yang barusan dihitung. Terbilang (F35) otomatis ngikut karena
        // formulanya baca dari cell ini.
        if ($rows->count() > 0 && $totalRow) {
            $sheet->setCellValue($table['columns']['jumlah'] . $totalRow, $totalJumlah);
        }
    }

    // -----------------------------------------------------------------
    // SHEET: pernyataan
    // -----------------------------------------------------------------
    protected function fillPernyataan(Spreadsheet $spreadsheet, Perdin $perdin): void
    {
        $cfg = config('perdin.pernyataan');
        $sheet = $spreadsheet->getSheetByName($cfg['sheet']);

        if (! $sheet) {
            return;
        }

        $statement = $perdin->pernyataan_teks;

        if (! $statement) {
            if ($perdin->pernyataan_tidak_menggunakan_kendaraan) {
                $statement = 'Peserta tidak menggunakan kendaraan dinas.';
            } else {
                $statement = 'dalam ' . lcfirst($perdin->maksud_perjalanan ?? '');
            }
        }

        $this->writeFields($sheet, $cfg, [
            'maksud_perjalanan' => $statement,
        ]);

        $table = $cfg['table'];
        // Kotak tabel Pernyataan cuma didesain untuk 2 orang (lihat catatan
        // 'max_rows' di config/perdin.php) — kalau dibiarkan menulis semua
        // peserta, baris tambahan bakal tanpa border/style dan nabrak teks
        // "Melakukan Perjalanan Dinas ke ..." di B15. Jadi dipotong di sini,
        // TIDAK ikut ensureRowCapacity() (yang dipakai sheet lain buat
        // nambah baris otomatis).
        $maxRows = $cfg['max_rows'] ?? null;
        $travelers = $maxRows ? $perdin->travelers->take($maxRows) : $perdin->travelers;
        $startRow = $table['start_row'];

        foreach ($travelers as $index => $traveler) {
            $row = $startRow + $index;
            $columns = $table['columns'];

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['nama'] . $row, $traveler->nama);
            $sheet->setCellValue($columns['jabatan'] . $row, $traveler->jabatan);
        }
    }

    // ===================================================================
    // HELPER UMUM
    // ===================================================================

    /**
     * Tulis field tunggal (bukan tabel) sesuai config['fields'].
     * Field yang terdaftar di config['prefix_fields'] ditulis sebagai ": <isi>".
     */
    protected function writeFields(Worksheet $sheet, array $cfg, array $values): void
    {
        $protected = $cfg['protected_formula_cells'] ?? [];

        foreach ($cfg['fields'] as $key => $cell) {
            if (in_array($cell, $protected, true)) {
                continue; // jangan pernah timpa cell berformula
            }

            if (! array_key_exists($key, $values)) {
                continue;
            }

            $value = $values[$key];

            if (in_array($key, $cfg['prefix_fields'] ?? [], true)) {
                $value = ': ' . $value;
            }

            $sheet->setCellValue($cell, $value);
        }
    }

    /**
     * Pastikan tabel dinamis punya cukup baris untuk $needed data.
     * Kalau template hanya punya 2 baris kosong tapi butuh 5, sistem akan
     * menyisipkan baris baru sebelum baris total sambil menduplikasi gaya
     * (border, merge, tinggi baris) dari $table['style_row'].
     *
     * @return array{start_row: int, total_row: int|null} baris pertama untuk
     *         mulai mengisi data, dan posisi baris total SETELAH disesuaikan
     *         (baris total ikut turun kalau ada baris baru disisipkan).
     */
    protected function ensureRowCapacity(Worksheet $sheet, array $table, int $needed): array
    {
        $startRow = $table['start_row'];
        $styleRow = $table['style_row'] ?? $startRow;
        $totalRow = $table['total_row'] ?? null;

        $existingCapacity = $totalRow ? ($totalRow - $startRow) : $needed;
        $rowsAdded = 0;

        if ($needed > $existingCapacity && $totalRow) {
            $rowsToAdd = $needed - $existingCapacity;

            // Kumpulkan dulu cell yang di-merge di style_row (mis. Nama = B:D,
            // Jabatan = E:G), supaya baris baru ikut di-merge sama persis —
            // kalau tidak, kolomnya kelihatan "pecah"/berantakan.
            $mergesInStyleRow = [];
            foreach ($sheet->getMergeCells() as $mergeRange) {
                [$mergeStart, $mergeEnd] = explode(':', $mergeRange);
                $startCoord = Coordinate::coordinateFromString($mergeStart);
                $endCoord = Coordinate::coordinateFromString($mergeEnd);
                if ((int) $startCoord[1] === $styleRow && (int) $endCoord[1] === $styleRow) {
                    $mergesInStyleRow[] = [$startCoord[0], $endCoord[0]];
                }
            }

            // Sisipkan baris baru TEPAT SEBELUM baris total, PhpSpreadsheet
            // otomatis menggeser & menyesuaikan referensi formula (mis. SUM).
            $sheet->insertNewRowBefore($totalRow, $rowsToAdd);

            $startColIndex = Coordinate::columnIndexFromString('A');
            $endColIndex = Coordinate::columnIndexFromString($this->lastColumn($table));

            for ($i = 0; $i < $rowsToAdd; $i++) {
                $targetRow = $totalRow + $i;

                // duplicateStyle() cuma boleh dikasih Style dari SATU cell —
                // kalau dikasih range ('A21:U21'), PhpSpreadsheet diam-diam
                // cuma makai gaya cell PALING KIRI (kolom A) buat SEMUA
                // kolom, jadi format angka/border kolom lain (mis. kolom
                // Penginapan) ikut ketimpa jadi General. Makanya harus
                // di-duplicate per kolom satu-satu.
                for ($col = $startColIndex; $col <= $endColIndex; $col++) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $sheet->duplicateStyle(
                        $sheet->getStyle($colLetter . $styleRow),
                        $colLetter . $targetRow
                    );
                }

                // Re-create merge cell (Nama, Jabatan, dst.) di baris baru.
                foreach ($mergesInStyleRow as [$colStart, $colEnd]) {
                    $sheet->mergeCells($colStart . $targetRow . ':' . $colEnd . $targetRow);
                }

                $sheet->getRowDimension($targetRow)->setRowHeight(
                    $sheet->getRowDimension($styleRow)->getRowHeight()
                );
            }

            $rowsAdded = $rowsToAdd;
        }

        return [
            'start_row' => $startRow,
            'total_row' => $totalRow ? $totalRow + $rowsAdded : null,
        ];
    }

    protected function lastColumn(array $table): string
    {
        return collect($table['columns'])->sort()->last() ?? 'Z';
    }

    protected function fillTravelerTable(Worksheet $sheet, array $table, Perdin $perdin): void
    {
        $travelers = $perdin->travelers;
        $capacity = $this->ensureRowCapacity($sheet, $table, $travelers->count());
        $startRow = $capacity['start_row'];
        $totalRow = $capacity['total_row'];
        $columns = $table['columns'];

        $sums = [
            'uang_harian' => 0.0,
            'penginapan' => 0.0,
            'represen' => 0.0,
            'tiket' => 0.0,
            'transportasi' => 0.0,
            'sewa_kendaraan' => 0.0,
            'jumlah' => 0.0,
        ];

        foreach ($travelers as $index => $traveler) {
            $row = $startRow + $index;

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['nama'] . $row, $traveler->nama);
            $sheet->setCellValue($columns['jabatan'] . $row, $traveler->jabatan);
            $sheet->setCellValue($columns['es'] . $row, $traveler->es ?: '-');
            $sheet->setCellValue($columns['gol'] . $row, $traveler->gol ?: '-');
            $sheet->setCellValue($columns['dari'] . $row, $traveler->dari);
            $sheet->setCellValue($columns['ke'] . $row, $traveler->ke);

            $sheet->setCellValue(
                $columns['tanggal_mulai'] . $row,
                $traveler->tanggal_mulai ? ExcelDate::PHPToExcel($traveler->tanggal_mulai) : null
            );
            $sheet->setCellValue(
                $columns['tanggal_sampai'] . $row,
                $traveler->tanggal_sampai ? ExcelDate::PHPToExcel($traveler->tanggal_sampai) : null
            );
            $sheet->getStyle($columns['tanggal_mulai'] . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $sheet->getStyle($columns['tanggal_sampai'] . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');

            $hari = $traveler->hari ? (int) $traveler->hari : 0;
            $uang_harian = $traveler->uang_harian ? (float) $traveler->uang_harian : 0.0;
            $penginapan = $traveler->penginapan ? (float) $traveler->penginapan : 0.0;
            $represen = $traveler->represen ? (float) $traveler->represen : 0.0;
            $tiket = $traveler->tiket ? (float) $traveler->tiket : 0.0;
            $transportasi = $traveler->transportasi ? (float) $traveler->transportasi : 0.0;
            $sewa_kendaraan = $traveler->sewa_kendaraan ? (float) $traveler->sewa_kendaraan : 0.0;

            $sheet->setCellValue($columns['hari'] . $row, $hari);
            // Kolom "Uang Harian" di Excel = tarif per hari x jumlah hari
            // (persis kayak angka "Total" yang muncul di web).
            $sheet->setCellValue($columns['uang_harian'] . $row, $uang_harian * $hari);
            // Kolom "Penginapan" di Excel = tarif per malam x jumlah malam
            // (hari - 1), persis kayak "Total" yang muncul di web & kolom
            // Uang Harian.
            $nights = max($hari - 1, 0);
            $sheet->setCellValue($columns['penginapan'] . $row, $penginapan * $nights);
            $sheet->setCellValue($columns['represen'] . $row, $represen);
            $sheet->setCellValue($columns['tiket'] . $row, $tiket);
            $sheet->setCellValue($columns['transportasi'] . $row, $transportasi);
            $sheet->setCellValue($columns['sewa_kendaraan'] . $row, $sewa_kendaraan);

            $rowTotal = ($uang_harian * $hari) + ($penginapan * $nights) + $represen + $tiket + $transportasi + $sewa_kendaraan;
            $sheet->setCellValue($columns['jumlah'] . $row, $rowTotal);

            $sums['uang_harian'] += ($uang_harian * $hari);
            $sums['penginapan'] += ($penginapan * $nights);
            $sums['represen'] += $represen;
            $sums['tiket'] += $tiket;
            $sums['transportasi'] += $transportasi;
            $sums['sewa_kendaraan'] += $sewa_kendaraan;
            $sums['jumlah'] += $rowTotal;

            // Semua isi baris di-tengahkan (horizontal & vertikal).
            $sheet->getStyle($columns['no'] . $row . ':' . $this->lastColumn($table) . $row)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        if ($totalRow && $travelers->count() > 0) {
            foreach (['uang_harian', 'penginapan', 'represen', 'tiket', 'transportasi', 'sewa_kendaraan', 'jumlah'] as $key) {
                $col = $columns[$key];
                $sheet->setCellValue($col . $totalRow, $sums[$key] ?? 0.0);
            }

            $sheet->setCellValue($columns['nama'] . $totalRow, 'Jumlah = ' . $travelers->count() . ' Orang');

            $sheet->getStyle($columns['no'] . $totalRow . ':' . $this->lastColumn($table) . $totalRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    protected function formatTanggal(?Carbon $tanggal, string $format = 'd F Y'): ?string
    {
        return $tanggal?->locale('id')->translatedFormat($format);
    }

    protected function formatKotaTanggal(Perdin $perdin): string
    {
        $kota = $perdin->kota_tanda_tangan ?: 'Jakarta';
        $tanggal = $perdin->tanggal_tanda_tangan ?? now();

        return $kota . ', ' . $tanggal->locale('id')->translatedFormat('F Y');
    }

    /**
     * Sama seperti formatKotaTanggal(), tapi kota & tanggalnya dikasih
     * langsung (dipakai buat field yang punya kota/tanggal sendiri di luar
     * "Kota/Bulan Tanda Tangan" umum, mis. tanggal Bepergian & Dibayar
     * Lunas di Kwitansi). $prefix opsional ditaruh di depan (mis. "Dibayar
     * lunas, Tgl ").
     */
    protected function formatKotaTanggalCustom(?string $kota, $tanggal, string $prefix = ''): string
    {
        $kota = $kota ?: 'Jakarta';
        $tanggal = $tanggal ?: now();

        if (is_string($tanggal)) {
            $tanggal = Carbon::parse($tanggal);
        }

        return $prefix . $kota . ', ' . $tanggal->locale('id')->translatedFormat('F Y');
    }

    protected function formatNip(?string $nip): ?string
    {
        if (! $nip) {
            return null;
        }

        return str_starts_with($nip, 'NIP') ? $nip : 'NIP. ' . $nip;
    }
}