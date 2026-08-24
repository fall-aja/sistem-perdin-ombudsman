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
    ) {}

    /**
     * Simpan / update data form (dipanggil Alpine.js via fetch, tanpa reload).
     * Ini TIDAK langsung generate file — user masih bisa preview & edit dulu.
     */
    public function store(StorePerdinRequest $request): JsonResponse
    {
        $data = $request->safe()->except(['travelers', 'rincian_items', 'dpr_items']);

        // jika nama_bepergian atau untuk_pembayaran tidak dikirim,
        // ambil fallback otomatis dari Nama Pengaju, traveler pertama, dan maksud perjalanan.
        $data['nama_bepergian'] = $data['nama_bepergian'] ?: $request->input('nama_pengaju') ?: $request->input('travelers.0.nama');
        $data['untuk_pembayaran'] = $data['untuk_pembayaran'] ?: $request->input('maksud_perjalanan');

        // Jika DPR tidak diisi, gunakan Nama Pengaju / NIP Pengaju sebagai fallback
        $data['dpr_nama'] = $data['dpr_nama'] ?: $request->input('nama_pengaju');
        $data['dpr_nip'] = $data['dpr_nip'] ?: $request->input('nip_pengaju');
        $data['dpr_jabatan'] = $data['dpr_jabatan'] ?: $request->input('dpr_jabatan') ?: null;

        $perdin = DB::transaction(function () use ($request, $data) {
            $perdin = Perdin::create($data);

            foreach ($request->input('travelers', []) as $i => $row) {
                $row = collect($row)->except(['id', 'perdin_id', 'created_at', 'updated_at'])->toArray();

                $row = array_merge([
                    'nama' => '',
                    'jabatan' => null,
                    'es' => null,
                    'gol' => null,
                    'dari' => null,
                    'ke' => null,
                    'tanggal_mulai' => null,
                    'tanggal_sampai' => null,
                    'hari' => null,
                    'uang_harian' => null,
                    'penginapan' => null,
                    'represen' => null,
                    'tiket' => null,
                    'transportasi' => null,
                    'sewa_kendaraan' => null,
                ], $row);

                foreach (['hari', 'uang_harian', 'penginapan', 'represen', 'tiket', 'transportasi', 'sewa_kendaraan'] as $key) {
                    if ($row[$key] === null || $row[$key] === '') {
                        $row[$key] = $key === 'hari' ? 1 : 0;
                    }
                }

                $row['nama'] = $row['nama'] ?: '';
                $row['jabatan'] = $row['jabatan'] ?: null;
                $row['es'] = $row['es'] ?: null;
                $row['gol'] = $row['gol'] ?: null;
                $row['dari'] = $row['dari'] ?: null;
                $row['ke'] = $row['ke'] ?: null;
                $row['tanggal_mulai'] = $row['tanggal_mulai'] ?: null;
                $row['tanggal_sampai'] = $row['tanggal_sampai'] ?: null;

                $perdin->travelers()->create([
                    ...$row,
                    'urutan' => $i + 1,
                ]);
            }

            foreach ($request->input('rincian_items', []) as $i => $row) {
                $row = collect($row)->except(['id', 'perdin_id', 'created_at', 'updated_at'])->toArray();

                $perdin->rincianItems()->create([
                    'uraian'             => $row['uraian'] ?? null,
                    'keterangan_tambahan'=> $row['keterangan_tambahan'] ?? null,
                    'jumlah_satuan'      => $row['jumlah_satuan'] ?? 0,
                    'harga_satuan'       => $row['harga_satuan'] ?? 0,
                    'keterangan'         => $row['keterangan'] ?? null,
                    'urutan'             => $i + 1,
                ]);
            }

            foreach ($request->input('dpr_items', []) as $i => $row) {
                $perdin->dprItems()->create([
                    ...collect($row)->except(['id', 'perdin_id', 'created_at', 'updated_at'])->toArray(),
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

    public function ppaPdf(Perdin $perdin)
    {
        // Render a dedicated PPA blade view and convert to PDF using mPDF
        $html = view('perdin.ppa_print', ['perdin' => $perdin->load('travelers')])->render();

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
        $mpdf->WriteHTML($html);

        $tmpDir = storage_path('app/' . config('perdin.generated_path'));
        if (! file_exists($tmpDir)) mkdir($tmpDir, 0755, true);

        $filename = 'Perdin_PPA_' . ($perdin->id) . '.pdf';
        $path = $tmpDir . '/' . $filename;

        $mpdf->Output($path, \Mpdf\Output\Destination::FILE);

        return response()->download($path)->deleteFileAfterSend(false);
    }

    public function destroy(Perdin $perdin): JsonResponse
    {
        $perdin->delete();

        return response()->json(['message' => 'Data dihapus.', 'id' => $perdin->id]);
    }

    /**
     * Tampilkan halaman Recycle Bin berisi daftar Perdin yang di-soft-delete.
     */
    public function recycleIndex()
    {
        $deleted = Perdin::onlyTrashed()->with('travelers')->latest('deleted_at')->get();

        return view('perdin.recycle', ['deleted' => $deleted]);
    }

    /**
     * Hapus permanen record (force delete) dan file yang terkait.
     */
    public function forceDelete(Perdin $perdin): JsonResponse
    {
        $perdin = Perdin::withTrashed()->findOrFail($perdin->id);

        // Hapus file generated kalau ada
        if ($perdin->generated_excel_path) {
            $path = storage_path('app/' . $perdin->generated_excel_path);
            if (file_exists($path)) @unlink($path);
        }
        if ($perdin->generated_pdf_path) {
            $path = storage_path('app/' . $perdin->generated_pdf_path);
            if (file_exists($path)) @unlink($path);
        }

        $perdin->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.', 'id' => $perdin->id]);
    }

    public function restore($id): JsonResponse
    {
        $perdin = Perdin::withTrashed()->findOrFail($id);

        if (! $perdin->trashed()) {
            return response()->json(['message' => 'Data tidak dalam keadaan terhapus.'], 400);
        }

        $perdin->restore();

        return response()->json(['message' => 'Data dipulihkan.', 'perdin' => $perdin->fresh(['travelers','rincianItems','dprItems'])]);
    }
}
