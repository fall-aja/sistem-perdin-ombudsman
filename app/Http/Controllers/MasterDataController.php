<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Perdin;
use App\Models\PerdinTraveler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Pencarian pegawai real-time (dipakai untuk autocomplete Nama/NIP).
     * Ketik minimal 1 huruf -> langsung dapat daftar pegawai yang cocok,
     * diambil dari tabel `pegawai` (hasil import DUK).
     */
    public function searchPegawai(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $hasil = Pegawai::query()
            ->where('nama', 'like', "{$q}%")
            ->orWhere('nip', 'like', "{$q}%")
            ->orderBy('nama')
            ->limit(15)
            ->get(['nip', 'nama', 'jabatan', 'penempatan']);

        return response()->json($hasil);
    }

    public function suggestions(): JsonResponse
    {
        $tujuan = PerdinTraveler::query()->whereNotNull('ke')->distinct()->pluck('ke');
        $asal = PerdinTraveler::query()->whereNotNull('dari')->distinct()->pluck('dari');
        $kotaTtd = Perdin::query()->whereNotNull('kota_tanda_tangan')->distinct()->pluck('kota_tanda_tangan');

        return response()->json([
            'kota' => $kotaTtd->merge($asal)->merge($tujuan)->filter()->unique()->values(),
        ]);
    }
}