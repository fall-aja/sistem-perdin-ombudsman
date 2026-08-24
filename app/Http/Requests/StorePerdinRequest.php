<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StorePerdinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // tanpa login sesuai spec (dashboard langsung terbuka)
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'message' => 'Validasi gagal. Periksa field yang diberi tanda merah.',
            'errors' => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }

    public function rules(): array
    {
        return [
            // Header
            'nomor'                 => ['nullable', 'string', 'max:100'],
            'maksud_perjalanan'     => ['required', 'string'],
            'surat_tugas_jabatan'   => ['nullable', 'string', 'max:150'],
            'nomor_st'              => ['nullable', 'string', 'max:100'],
            'tanggal_st'            => ['nullable', 'date'],
            'nomor_rk'              => ['nullable', 'string', 'max:150'],
            'pembebanan_anggaran'   => ['nullable', 'string', 'max:150'],
            'kota_tanda_tangan'     => ['nullable', 'string', 'max:50'],
            'tanggal_tanda_tangan'  => ['nullable', 'date_format:Y-m'],

            // Penandatangan
            'nama_ppk'              => ['nullable', 'string', 'max:150'],
            'nip_ppk'               => ['nullable', 'string', 'max:50'],
            'nama_kabag_keuangan'   => ['nullable', 'string', 'max:150'],
            'nip_kabag_keuangan'    => ['nullable', 'string', 'max:50'],
            'nama_mengetahui'       => ['nullable', 'string', 'max:150'],
            'nama_pengaju'          => ['nullable', 'string', 'max:150'],

            // Kwitansi
            'tahun_anggaran'        => ['nullable', 'string', 'max:4'],
            'nomor_bukti_kwitansi'  => ['nullable', 'string', 'max:50'],
            'mak'                   => ['nullable', 'string', 'max:100'],
            'sudah_terima_dari'     => ['nullable', 'string', 'max:200'],
            'jumlah_uang_kwitansi'  => ['nullable', 'integer', 'min:0'],
            'untuk_pembayaran'      => ['nullable', 'string', 'max:500'],
            'nama_bendahara'        => ['nullable', 'string', 'max:150'],
            'nama_bepergian'        => ['nullable', 'string', 'max:150'],
            'nip_bendahara'         => ['nullable', 'string', 'max:50'],

            // Rincian
            'lampiran_sppd_no'      => ['nullable', 'string', 'max:100'],
            'tanggal_sppd'          => ['nullable', 'date'],

            // DPR
            'dpr_nama'              => ['nullable', 'string', 'max:150'],
            'dpr_nip'               => ['nullable', 'string', 'max:50'],
            'dpr_jabatan'           => ['nullable', 'string', 'max:150'],
            'nomor_spd'             => ['nullable', 'string', 'max:100'],
            'tanggal_spd'           => ['nullable', 'date'],

            // Pernyataan
            'pernyataan_tidak_menggunakan_kendaraan' => ['nullable', 'boolean'],
            'pernyataan_teks'      => ['nullable', 'string', 'max:1000'],

            // Peserta (tabel dinamis)
            'travelers'                       => ['required', 'array', 'min:1'],
            'travelers.*.nama'                => ['required', 'string', 'max:150'],
            'travelers.*.jabatan'              => ['nullable', 'string', 'max:150'],
            'travelers.*.es'                   => ['nullable', 'string', 'max:20'],
            'travelers.*.gol'                  => ['nullable', 'string', 'max:20'],
            'travelers.*.dari'                 => ['nullable', 'string', 'max:100'],
            'travelers.*.ke'                   => ['nullable', 'string', 'max:150'],
            'travelers.*.tanggal_mulai'        => ['nullable', 'date'],
            'travelers.*.tanggal_sampai'       => ['nullable', 'date'],
            'travelers.*.hari'                 => ['nullable', 'integer', 'min:1'],
            'travelers.*.uang_harian'          => ['nullable', 'integer', 'min:0'],
            'travelers.*.penginapan'           => ['nullable', 'integer', 'min:0'],
            'travelers.*.represen'             => ['nullable', 'integer', 'min:0'],
            'travelers.*.tiket'                => ['nullable', 'integer', 'min:0'],
            'travelers.*.transportasi'         => ['nullable', 'integer', 'min:0'],
            'travelers.*.sewa_kendaraan'       => ['nullable', 'integer', 'min:0'],

            // Rincian items (tabel dinamis, opsional)
            'rincian_items'                          => ['nullable', 'array'],
            'rincian_items.*.uraian'                 => ['required_with:rincian_items', 'string', 'max:150'],
            'rincian_items.*.keterangan_tambahan'    => ['nullable', 'string', 'max:150'],
            'rincian_items.*.jumlah_satuan'          => ['nullable', 'numeric', 'min:0'],
            'rincian_items.*.harga_satuan'           => ['nullable', 'integer', 'min:0'],
            'rincian_items.*.keterangan'             => ['nullable', 'string', 'max:150'],

            // DPR items (tabel dinamis, opsional)
            'dpr_items'                     => ['nullable', 'array'],
            'dpr_items.*.uraian'            => ['required_with:dpr_items', 'string', 'max:200'],
            'dpr_items.*.jumlah'            => ['nullable', 'integer', 'min:0'],
        ];
    }
}
