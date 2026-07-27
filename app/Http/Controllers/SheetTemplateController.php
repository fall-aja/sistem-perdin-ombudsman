<?php

namespace App\Http\Controllers;

use App\Models\PerdinSheetTemplate;
use App\Services\PlaceholderTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SheetTemplateController extends Controller
{
    public function index()
    {
        $templates = PerdinSheetTemplate::orderByRaw(
            "FIELD(key, 'pertanggung_jawaban','ppa','kwitansi','rincian','dpr','pernyataan')"
        )->get();

        return view('template-settings', [
            'templates' => $templates,
            'placeholders' => PlaceholderTemplateService::availablePlaceholders(),
        ]);
    }

    public function update(Request $request, PerdinSheetTemplate $perdinSheetTemplate): JsonResponse
    {
        $data = $request->validate([
            'file_name'      => ['nullable', 'string', 'max:150'],
            'judul_dokumen'  => ['nullable', 'string', 'max:200'],
            'teks_awalan'    => ['nullable', 'string', 'max:2000'],
            'teks_penutup'   => ['nullable', 'string', 'max:2000'],
        ]);

        $perdinSheetTemplate->update($data);

        return response()->json([
            'message' => 'Template tersimpan.',
            'template' => $perdinSheetTemplate,
        ]);
    }
}