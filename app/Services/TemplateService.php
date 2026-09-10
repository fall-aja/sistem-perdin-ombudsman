<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class TemplateService
{
    /**
     * Path absolut ke workbook master. TIDAK PERNAH ditulis balik oleh sistem.
     */
    public function masterPath(): string
    {
        $path = storage_path('app/' . config('perdin.template_path'));

        if (! file_exists($path)) {
            throw new RuntimeException("Template master tidak ditemukan di: {$path}");
        }

        return $path;
    }

    /**
     * Copy workbook master ke lokasi kerja sementara lalu load dengan
     * PhpSpreadsheet. Semua style, merge cell, formula, border, row height,
     * column width, print area & page setup ikut terbawa otomatis karena
     * kita membaca file HASIL COPY, bukan membangun ulang dari nol.
     */
    public function loadWorkingCopy(): array
    {
        $workDir = storage_path('app/tmp');
        File::ensureDirectoryExists($workDir);

        $workingPath = $workDir . '/' . Str::uuid() . '.xlsx';
        File::copy($this->masterPath(), $workingPath);

        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        // Penting: JANGAN set data_only, kita butuh formula tetap ada.
        $spreadsheet = $reader->load($workingPath);

        return [$spreadsheet, $workingPath];
    }

    /**
     * Simpan hasil isian ke folder output dengan nama file stabil.
     */
    public function saveGenerated(Spreadsheet $spreadsheet, string $filename): string
    {
        // Reset posisi kursor/scroll setiap sheet ke A1 sebelum disimpan.
        // Tanpa ini, Excel akan membuka file dalam kondisi ter-scroll ke
        // cell terakhir yang ditulis kode (biasanya di tengah tabel data),
        // membuatnya TERLIHAT seolah header/judul di atas hilang, padahal
        // sebenarnya tetap ada — cuma tidak kelihatan karena posisi scroll.
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheet->setSelectedCell('A1');
            $sheet->freezePane('A1'); // pastikan tidak ada freeze pane nyasar
            $sheet->getSheetView()->setZoomScale(100);
        }
        $spreadsheet->setActiveSheetIndex(0);

        $outputDir = storage_path('app/' . config('perdin.generated_path'));
        File::ensureDirectoryExists($outputDir);

        $outputPath = $outputDir . '/' . $filename;

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // Jaga agar drawing (checkbox dsb) tetap ditulis oleh PhpSpreadsheet.
        $writer->setIncludeCharts(true);
        // Tidak melakukan perhitungan ulang formula saat menyimpan karena
        // template bisa berisi referensi ke workbook eksternal atau formula
        // yang tidak bisa dihitung di server.
        $writer->setPreCalculateFormulas(false);
        $writer->save($outputPath);

        // PhpSpreadsheet dapat merusak shape DrawingML (checkbox form-control)
        // saat re-save. Reposisi/perbaiki via manipulasi zip mentah.
        $this->nudgeCheckboxDrawingsDown($outputPath);

        return $outputPath;
    }

    /**
     * Perbaikan pasca-simpan untuk checkbox berbasis DrawingML (form control)
     * yang posisinya bisa bergeser/rusak saat workbook ditulis ulang oleh
     * PhpSpreadsheet. Kita geser anchor checkbox turun sedikit dan pastikan
     * XML drawing tetap valid dengan memanipulasi file xlsx (zip) langsung,
     * bukan lewat API PhpSpreadsheet (yang tidak mendukung form-control shapes).
     *
     * CATATAN PENTING (dari pengalaman proyek serupa):
     * JANGAN PERNAH membuka file hasil generate di Microsoft Excel lalu
     * menyimpannya ulang sebagai template baru — Excel akan menghapus
     * DrawingML checkbox yang tidak dikenalinya. Selalu gunakan file
     * Template_PJ.xlsx yang asli sebagai master, apa pun yang terjadi.
     */
    protected function nudgeCheckboxDrawingsDown(string $xlsxPath, int $pixelsDown = 2): void
    {
        $zip = new ZipArchive();

        if ($zip->open($xlsxPath) !== true) {
            return; // gagal buka zip, lewati saja (tidak fatal)
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);

            if (! $entry || ! Str::is('xl/drawings/vmlDrawing*.vml', $entry)) {
                continue;
            }

            $xml = $zip->getFromName($entry);

            if ($xml === false) {
                continue;
            }

            // Geser koordinat "top" pada setiap <v:shape> yang berisi checkbox
            // form-control (ditandai elemen <x:ClientData ObjectType="Checkbox">).
            $patched = preg_replace_callback(
                '/<x:ClientData ObjectType="Checkbox">.*?<\/x:ClientData>/s',
                function ($match) use ($pixelsDown) {
                    return preg_replace_callback(
                        '/<x:Anchor>([^<]+)<\/x:Anchor>/',
                        function ($anchorMatch) use ($pixelsDown) {
                            $parts = array_map('trim', explode(',', $anchorMatch[1]));

                            if (count($parts) === 8) {
                                // index 3 = offset Y baris awal (dalam satuan 1/1024 baris)
                                $parts[3] = (string) ((int) $parts[3] + $pixelsDown);
                            }

                            return '<x:Anchor>' . implode(', ', $parts) . '</x:Anchor>';
                        },
                        $match[0]
                    );
                },
                $xml
            );

            if ($patched !== null && $patched !== $xml) {
                $zip->addFromString($entry, $patched);
            }
        }

        $zip->close();
    }
}