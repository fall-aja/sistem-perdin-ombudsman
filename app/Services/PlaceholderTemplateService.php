<?php

namespace App\Services;

use App\Models\Perdin;

class PlaceholderTemplateService
{
    /**
     * Daftar placeholder yang tersedia untuk ditampilkan di UI "Pengaturan
     * Template" — key adalah nama placeholder (tanpa kurung kurawal),
     * value adalah label deskriptif untuk ditampilkan ke user.
     */
    public static function availablePlaceholders(): array
    {
        return [
            'nomor'               => 'Nomor Surat',
            'hal'                 => 'Hal',
            'maksud_perjalanan'   => 'Maksud Perjalanan',
            'surat_tugas'         => 'Jabatan Surat Tugas',
            'nomor_surat_tugas'   => 'Nomor Surat Tugas',
            'tanggal_surat_tugas' => 'Tanggal Surat Tugas',
            'nomor_rencana_kerja' => 'Nomor Rencana Kerja',
            'pembebanan_anggaran' => 'Pembebanan Anggaran',
            'kota'                => 'Kota Tanda Tangan',
            'tanggal'             => 'Tanggal Tanda Tangan',
            'nama_pejabat'        => 'Nama PPK',
            'nip_pejabat'         => 'NIP PPK',
            'nama_bendahara'      => 'Nama Bendahara',
            'nip_bendahara'       => 'NIP Bendahara',
            'nomor_spd'           => 'Nomor SPD',
            'tanggal_spd'         => 'Tanggal SPD',
            'dpr_nama'            => 'Nama (DPR)',
            'dpr_jabatan'         => 'Jabatan (DPR)',
        ];
    }

    /**
     * Ganti semua {placeholder} pada $template dengan nilai dari $perdin.
     * Placeholder yang tidak dikenali dibiarkan apa adanya (tidak dihapus)
     * supaya user tahu ada typo, bukan hilang diam-diam.
     */
    public function render(?string $template, Perdin $perdin): string
    {
        if (! $template) {
            return '';
        }

        $values = [
            'nomor'               => $perdin->nomor,
            'hal'                 => $perdin->hal,
            'maksud_perjalanan'   => $perdin->maksud_perjalanan,
            'surat_tugas'         => $perdin->surat_tugas_jabatan,
            'nomor_surat_tugas'   => $perdin->nomor_st,
            'tanggal_surat_tugas' => optional($perdin->tanggal_st)->translatedFormat('d F Y'),
            'nomor_rencana_kerja' => $perdin->nomor_rk,
            'pembebanan_anggaran' => $perdin->pembebanan_anggaran,
            'kota'                => $perdin->kota_tanda_tangan,
            'tanggal'             => optional($perdin->tanggal_tanda_tangan ?? now())->translatedFormat('d F Y'),
            'nama_pejabat'        => $perdin->nama_ppk,
            'nip_pejabat'         => $perdin->nip_ppk,
            'nama_bendahara'      => $perdin->nama_bendahara,
            'nip_bendahara'       => $perdin->nip_bendahara,
            'nomor_spd'           => $perdin->nomor_spd,
            'tanggal_spd'         => optional($perdin->tanggal_spd)->translatedFormat('d F Y'),
            'dpr_nama'            => $perdin->dpr_nama,
            'dpr_jabatan'         => $perdin->dpr_jabatan,
        ];

        return preg_replace_callback('/\{([a-z_]+)\}/', function ($match) use ($values) {
            return array_key_exists($match[1], $values) ? (string) $values[$match[1]] : $match[0];
        }, $template);
    }
}