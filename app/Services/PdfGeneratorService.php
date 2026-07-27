<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class PdfGeneratorService
{
    /**
     * CATATAN PENTING soal pilihan teknis PDF:
     * -----------------------------------------
     * Spec awal menyebut mPDF, tapi mPDF adalah HTML->PDF renderer — ia TIDAK
     * bisa membaca file .xlsx sama sekali, apalagi mereproduksi page setup,
     * print area, merge cell & border Excel secara presisi (acceptance test
     * #8 & #9 minta PDF & hasil print IDENTIK dengan Excel).
     *
     * Satu-satunya cara yang benar-benar menghasilkan PDF identik dengan
     * workbook adalah merender file .xlsx itu sendiri lewat mesin yang
     * memahami format Excel: LibreOffice headless (`soffice --convert-to pdf`).
     * Ini juga otomatis mengikuti orientasi halaman & print area asli sheet.
     *
     * mPDF tetap dipasang (lihat composer.json) sebagai FALLBACK opsional
     * kalau LibreOffice tidak tersedia di server, lewat convertWithMpdfFallback()
     * di bawah — tapi hasilnya HANYA preview kasar, bukan cetak resmi.
     */
    public function generate(string $xlsxPath): string
    {
        $pdfPath = preg_replace('/\.xlsx$/i', '.pdf', $xlsxPath);
        $outputDir = dirname($xlsxPath);

        if ($this->libreOfficeAvailable()) {
            $this->convertWithLibreOffice($xlsxPath, $outputDir);

            if (file_exists($pdfPath)) {
                return $pdfPath;
            }
        }

        Log::warning('LibreOffice tidak tersedia, fallback ke rendering sederhana.', [
            'file' => $xlsxPath,
        ]);

        return $this->convertWithMpdfFallback($xlsxPath, $pdfPath);
    }

    protected function libreOfficeAvailable(): bool
    {
        $result = Process::run('which soffice');

        return $result->successful();
    }

    protected function convertWithLibreOffice(string $xlsxPath, string $outputDir): void
    {
        // --convert-to pdf menghormati print area, page orientation, dan
        // page setup yang tersimpan di dalam workbook (persis requirement).
        $result = Process::timeout(120)->run([
            'soffice',
            '--headless',
            '--norestore',
            '--convert-to', 'pdf',
            '--outdir', $outputDir,
            $xlsxPath,
        ]);

        if (! $result->successful()) {
            throw new RuntimeException('Gagal convert ke PDF via LibreOffice: ' . $result->errorOutput());
        }
    }

    /**
     * Fallback darurat: render ringkasan data (bukan replika Excel presisi)
     * memakai mPDF, hanya dipakai jika LibreOffice benar-benar tidak ada.
     */
    protected function convertWithMpdfFallback(string $xlsxPath, string $pdfPath): string
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($xlsxPath);
        $reader->setLoadSheetsOnly(null);
        $spreadsheet = $reader->load($xlsxPath);

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:9px;}td{border:1px solid #999;padding:2px;}</style>';

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $html .= '<h4>' . e($sheet->getTitle()) . '</h4><table>';
            foreach ($sheet->toArray(null, true, true, true) as $row) {
                $html .= '<tr>' . collect($row)->map(fn ($v) => '<td>' . e((string) $v) . '</td>')->implode('') . '</tr>';
            }
            $html .= '</table><div style="page-break-after: always;"></div>';
        }

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
        $mpdf->WriteHTML($html);

        File::ensureDirectoryExists(dirname($pdfPath));
        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

        return $pdfPath;
    }
}
