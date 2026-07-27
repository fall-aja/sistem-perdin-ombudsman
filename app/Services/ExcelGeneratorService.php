<?php

namespace App\Services;

use App\Models\Perdin;
use App\Models\PerdinSheetTemplate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
        $tanggal = optional($perdin->tanggal_st)->format('Ymd') ?? now()->format('Ymd');
        $slug = str($perdin->maksud_perjalanan ?? 'perdin')->limit(40, '')->slug('_');

        return "PERDIN_{$slug}_{$tanggal}_{$perdin->id}.xlsx";
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
            'untuk_pembayaran' => $this->sheetText('kwitansi', 'teks_awalan', $perdin, (string) $perdin->maksud_perjalanan),
            'kota_tanggal_ttd'    => $this->formatKotaTanggal($perdin),
            'nama_yang_bepergian' => $pemohon?->nama,
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
            'nama_bendahara'   => $perdin->nama_bendahara,
            'nip_bendahara'    => $this->formatNip($perdin->nip_bendahara),
            'nama_bepergian'   => $perdin->travelers->first()?->nama,
        ]);

        $table = $cfg['table'];
        $rows = $perdin->rincianItems;
        $startRow = $this->ensureRowCapacity($sheet, $table, $rows->count());

        foreach ($rows as $index => $item) {
            $row = $startRow + $index;
            $columns = $table['columns'];

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['uraian'] . $row, $item->uraian);
            $sheet->setCellValue($columns['keterangan_tambahan'] . $row, $item->keterangan_tambahan);
            $sheet->setCellValue($columns['jumlah_satuan'] . $row, $item->jumlah_satuan);
            $sheet->setCellValue($columns['harga_satuan'] . $row, $item->harga_satuan);
            // Kolom "jumlah" (F) TETAP formula "=D*E" bawaan template kalau ada;
            // hanya isi manual jika baris ini hasil insert baru (formula kosong).
            if (! $sheet->getCell($columns['jumlah'] . $row)->getValue()) {
                $sheet->setCellValue($columns['jumlah'] . $row, '=' . $columns['jumlah_satuan'] . $row . '*' . $columns['harga_satuan'] . $row);
            }
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
            'nip'              => $this->formatNip($perdin->dpr_nip),
            'jabatan'          => $perdin->dpr_jabatan,
            'tanggal_spd_text' => $this->sheetText('dpr', 'teks_awalan', $perdin, 'Berdasarkan Surat Perjalanan Dinas (SPD) tanggal ' . $this->formatTanggal($perdin->tanggal_spd, 'd F Y')),
            'nomor_spd'        => $this->sheetText('dpr', 'teks_penutup', $perdin, 'Nomor : ' . $perdin->nomor_spd . ', dengan ini kami menyatakan dengan sesungguhnya bahwa:'),
            'kota_tanggal_ttd' => $this->formatKotaTanggal($perdin),
            'nama_ppk'         => $perdin->nama_ppk,
            'nip_ppk'          => $this->formatNip($perdin->nip_ppk),
            'nama_bepergian'   => $perdin->dpr_nama,
        ]);

        $table = $cfg['table'];
        $rows = $perdin->dprItems;
        $startRow = $this->ensureRowCapacity($sheet, $table, $rows->count());

        foreach ($rows as $index => $item) {
            $row = $startRow + $index;
            $columns = $table['columns'];

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['uraian'] . $row, $item->uraian);
            $sheet->setCellValue($columns['jumlah'] . $row, $item->jumlah);
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

        $this->writeFields($sheet, $cfg, [
            'maksud_perjalanan' => $this->sheetText('pernyataan', 'teks_awalan', $perdin, 'dalam ' . lcfirst($perdin->maksud_perjalanan ?? '')),
        ]);

        $table = $cfg['table'];
        $travelers = $perdin->travelers;
        $startRow = $this->ensureRowCapacity($sheet, $table, $travelers->count());

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
     * Isi tabel peserta (dipakai sheet Pertanggungjawaban & PPA) termasuk
     * menambah baris baru jika peserta lebih banyak dari baris yang tersedia
     * di template, sambil mempertahankan border/format lewat duplicateStyle().
     */
    protected function fillTravelerTable(Worksheet $sheet, array $table, Perdin $perdin): void
    {
        $travelers = $perdin->travelers;
        $startRow = $this->ensureRowCapacity($sheet, $table, $travelers->count());
        $columns = $table['columns'];

        foreach ($travelers as $index => $traveler) {
            $row = $startRow + $index;

            $sheet->setCellValue($columns['no'] . $row, $index + 1);
            $sheet->setCellValue($columns['nama'] . $row, $traveler->nama);
            $sheet->setCellValue($columns['jabatan'] . $row, $traveler->jabatan);
            $sheet->setCellValue($columns['es'] . $row, $traveler->es ?: '-');
            $sheet->setCellValue($columns['gol'] . $row, $traveler->gol ?: '-');
            $sheet->setCellValue($columns['dari'] . $row, $traveler->dari);
            $sheet->setCellValue($columns['ke'] . $row, $traveler->ke);
            $sheet->setCellValue($columns['tanggal_mulai'] . $row, $traveler->tanggal_mulai);
            $sheet->setCellValue($columns['tanggal_sampai'] . $row, $traveler->tanggal_sampai);
            $sheet->getStyle($columns['tanggal_mulai'] . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $sheet->getStyle($columns['tanggal_sampai'] . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue($columns['hari'] . $row, $traveler->hari);
            $sheet->setCellValue($columns['uang_harian'] . $row, $traveler->uang_harian);
            $sheet->setCellValue($columns['penginapan'] . $row, $traveler->penginapan);
            $sheet->setCellValue($columns['represen'] . $row, $traveler->represen);
            $sheet->setCellValue($columns['tiket'] . $row, $traveler->tiket);
            $sheet->setCellValue($columns['transportasi'] . $row, $traveler->transportasi);
            $sheet->setCellValue($columns['sewa_kendaraan'] . $row, $traveler->sewa_kendaraan);
            $sheet->setCellValue(
                $columns['jumlah'] . $row,
                "={$columns['uang_harian']}{$row}*{$columns['hari']}{$row}"
                    . "+{$columns['penginapan']}{$row}*MAX({$columns['hari']}{$row}-1,0)"
                    . "+{$columns['represen']}{$row}+{$columns['tiket']}{$row}+{$columns['transportasi']}{$row}+{$columns['sewa_kendaraan']}{$row}"
            );
        }
    }

    /**
     * Pastikan tabel dinamis punya cukup baris untuk $needed data.
     * Kalau template hanya punya 2 baris kosong tapi butuh 5, sistem akan
     * menyisipkan baris baru sebelum baris total sambil menduplikasi gaya
     * (border, merge, tinggi baris) dari $table['style_row'].
     *
     * @return int baris pertama untuk mulai mengisi data
     */
    protected function ensureRowCapacity(Worksheet $sheet, array $table, int $needed): int
    {
        $startRow = $table['start_row'];
        $styleRow = $table['style_row'] ?? $startRow;
        $totalRow = $table['total_row'] ?? null;

        $existingCapacity = $totalRow ? ($totalRow - $startRow) : $needed;

        if ($needed > $existingCapacity && $totalRow) {
            $rowsToAdd = $needed - $existingCapacity;

            // Sisipkan baris baru TEPAT SEBELUM baris total, PhpSpreadsheet
            // otomatis menggeser & menyesuaikan referensi formula (mis. SUM).
            $sheet->insertNewRowBefore($totalRow, $rowsToAdd);

            for ($i = 0; $i < $rowsToAdd; $i++) {
                $targetRow = $totalRow + $i;
                $sheet->duplicateStyle(
                    $sheet->getStyle('A' . $styleRow . ':' . $this->lastColumn($table) . $styleRow),
                    'A' . $targetRow . ':' . $this->lastColumn($table) . $targetRow
                );
                $sheet->getRowDimension($targetRow)->setRowHeight(
                    $sheet->getRowDimension($styleRow)->getRowHeight()
                );
            }
        }

        return $startRow;
    }

    protected function lastColumn(array $table): string
    {
        return collect($table['columns'])->sort()->last() ?? 'Z';
    }

    protected function formatTanggal(?Carbon $tanggal, string $format = 'd F Y'): ?string
    {
        return $tanggal?->translatedFormat($format);
    }

    protected function formatKotaTanggal(Perdin $perdin): string
    {
        $kota = $perdin->kota_tanda_tangan ?: 'Jakarta';
        $tanggal = $perdin->tanggal_tanda_tangan ?? now();

        return $kota . ', ' . $tanggal->translatedFormat('d F Y');
    }

    protected function formatNip(?string $nip): ?string
    {
        if (! $nip) {
            return null;
        }

        return str_starts_with($nip, 'NIP') ? $nip : 'NIP. ' . $nip;
    }
}
