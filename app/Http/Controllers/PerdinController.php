<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerdinRequest;
use App\Models\Perdin;
use App\Services\ExcelGeneratorService;
use App\Services\PdfGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerdinController extends Controller
{
    public function __construct(
        protected ExcelGeneratorService $excelGenerator,
        protected PdfGeneratorService $pdfGenerator,
    ) {
    }

    /**
     * Simpan / update data form (dipanggil Alpine.js via fetch, tanpa reload).
     * Ini TIDAK langsung generate file — user masih bisa preview & edit dulu.
     */
    public function store(StorePerdinRequest $request): JsonResponse
    {
        $data = $request->safe()->except(['travelers', 'rincian_items', 'dpr_items']);

        $perdin = DB::transaction(function () use ($request, $data) {
            $perdin = Perdin::create($data);

            foreach ($request->input('travelers', []) as $i => $row) {
                $perdin->travelers()->create([
                    ...$row,
                    'urutan' => $i + 1,
                ]);
            }

            foreach ($request->input('rincian_items', []) as $i => $row) {
                $perdin->rincianItems()->create([
                    ...$row,
                    'urutan' => $i + 1,
                ]);
            }

            foreach ($request->input('dpr_items', []) as $i => $row) {
                $perdin->dprItems()->create([
                    ...$row,
                    'urutan' => $i + 1,
                ]);
            }

            return $perdin;
        });

        return response()->json([
            'message' => 'Data tersimpan.',
            'perdin' => $perdin->load('travelers', 'rincianItems', 'dprItems'),
        ]);
    }

    /**
     * Preview data dalam bentuk JSON untuk dirender Alpine.js sebagai
     * tabel HTML yang "menyerupai" Excel. Ini HANYA visual, bukan sumber
     * file resmi — file resmi tetap dari workbook Excel.
     */
    public function preview(Perdin $perdin): JsonResponse
    {
        return response()->json(
            $perdin->load('travelers', 'rincianItems', 'dprItems')
        );
    }

    public function generateExcel(Perdin $perdin)
    {
        $path = $this->excelGenerator->generate($perdin->fresh(['travelers', 'rincianItems', 'dprItems']));

        return response()->download($path)->deleteFileAfterSend(false);
    }

    public function generatePdf(Perdin $perdin)
    {
        // Pastikan Excel-nya sudah ter-generate & up to date dulu, karena PDF
        // dibuat dari workbook hasil isi, bukan dari data mentah.
        $excelPath = $perdin->generated_excel_path
            ? storage_path('app/' . $perdin->generated_excel_path)
            : $this->excelGenerator->generate($perdin->fresh(['travelers', 'rincianItems', 'dprItems']));

        $pdfPath = $this->pdfGenerator->generate($excelPath);

        $relativePdf = str($pdfPath)->after(storage_path('app/'))->toString();
        $perdin->update(['generated_pdf_path' => $relativePdf]);

        return response()->file($pdfPath, [
            'Content-Disposition' => 'inline; filename="' . basename($pdfPath) . '"',
        ]);
    }

    public function destroy(Perdin $perdin): JsonResponse
    {
        $perdin->delete();

        return response()->json(['message' => 'Data dihapus.']);
    }
}
