<?php

namespace Database\Seeders;

use App\Models\PerdinSheetTemplate;
use Illuminate\Database\Seeder;

class PerdinSheetTemplateSeeder extends Seeder
{
    /**
     * Nilai default ini SAMA dengan teks yang tadinya hardcode di
     * ExcelGeneratorService. Setelah seeding, teks-teks ini bisa diedit
     * bebas lewat halaman "Pengaturan Template" tanpa perlu ubah kode.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'pertanggung_jawaban',
                'label' => 'Pertanggung Jawaban PERDIN',
                'file_name' => 'Pertanggung Jawaban PERDIN',
                'sheet_name' => 'pertanggung jawaban PERDIN',
                'judul_dokumen' => 'PERTANGGUNGJAWABAN PERJALANAN DINAS',
                'teks_awalan' => 'Hal',
                'teks_penutup' => null,
                'target_cell_awalan' => 'C4 (Hal)',
                'target_cell_penutup' => null,
            ],
            [
                'key' => 'ppa',
                'label' => 'PPA Perdin',
                'file_name' => 'PPA Perdin',
                'sheet_name' => 'PPA Perdin',
                'judul_dokumen' => 'PERMINTAAN DAN PEMBEBANAN ANGGARAN',
                'teks_awalan' => 'Permohonan Pembebanan Anggaran',
                'teks_penutup' => null,
                'target_cell_awalan' => 'C4 (Hal)',
                'target_cell_penutup' => null,
            ],
            [
                'key' => 'kwitansi',
                'label' => 'Kwitansi Perdin',
                'file_name' => 'Kwit PERDIN 1',
                'sheet_name' => 'Kwit PERDIN 1',
                'judul_dokumen' => 'KWITANSI',
                'teks_awalan' => '{maksud_perjalanan}',
                'teks_penutup' => null,
                'target_cell_awalan' => 'D17 (Untuk Pembayaran)',
                'target_cell_penutup' => null,
            ],
            [
                'key' => 'rincian',
                'label' => 'Rincian Perdin',
                'file_name' => 'Rincian PERDIN 1',
                'sheet_name' => 'Rincian PERDIN 1',
                'judul_dokumen' => 'RINCIAN PERJALANAN DINAS',
                'teks_awalan' => null,
                'teks_penutup' => null,
                'target_cell_awalan' => null,
                'target_cell_penutup' => null,
            ],
            [
                'key' => 'dpr',
                'label' => 'DPR Perdin',
                'file_name' => 'DPR PERDIN',
                'sheet_name' => 'DPR PERDIN',
                'judul_dokumen' => 'DAFTAR PENGELUARAN RIIL',
                'teks_awalan' => 'Berdasarkan Surat Perjalanan Dinas (SPD) tanggal {tanggal_spd}',
                'teks_penutup' => 'Nomor : {nomor_spd}, dengan ini kami menyatakan dengan sesungguhnya bahwa:',
                'target_cell_awalan' => 'B15',
                'target_cell_penutup' => 'B16',
            ],
            [
                'key' => 'pernyataan',
                'label' => 'Pernyataan',
                'file_name' => 'pernyataan',
                'sheet_name' => 'pernyataan',
                'judul_dokumen' => 'SURAT PERNYATAAN',
                'teks_awalan' => 'dalam {maksud_perjalanan}',
                'teks_penutup' => null,
                'target_cell_awalan' => 'B17',
                'target_cell_penutup' => null,
            ],
        ];

        foreach ($templates as $tpl) {
            PerdinSheetTemplate::updateOrCreate(['key' => $tpl['key']], $tpl);
        }
    }
}