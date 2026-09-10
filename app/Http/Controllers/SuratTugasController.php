<?php

namespace App\Http\Controllers;

use App\Models\SuratTugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratTugasController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor' => ['required', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'mak' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $file = $request->file('file');
        $validated['file_path'] = $file->store('surat-tugas', 'public');
        $validated['file_name'] = $file->getClientOriginalName();
        unset($validated['file']);

        SuratTugas::create($validated);

        return back()->with('message', 'Surat tugas berhasil disimpan.');
    }

    public function show(SuratTugas $suratTugas): BinaryFileResponse
    {
        if (! Storage::disk('public')->exists($suratTugas->file_path)) {
            abort(404, 'File surat tugas tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('public')->path($suratTugas->file_path),
            $suratTugas->file_name
        );
    }

    public function destroy(SuratTugas $suratTugas): RedirectResponse
    {
        Storage::disk('public')->delete($suratTugas->file_path);
        $suratTugas->delete();

        return back()->with('message', 'Riwayat surat tugas dihapus.');
    }
}