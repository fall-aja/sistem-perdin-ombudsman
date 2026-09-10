<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perdin extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tanggal_st' => 'date',
        'tanggal_tanda_tangan' => 'date',
        'tanggal_sppd' => 'date',
        'tanggal_spd' => 'date',
        'tanggal_bepergian' => 'date',
        'tanggal_lunas' => 'date',
        'pernyataan_tidak_menggunakan_kendaraan' => 'boolean',
    ];

    public function travelers(): HasMany
    {
        return $this->hasMany(PerdinTraveler::class)->orderBy('urutan');
    }

    public function rincianItems(): HasMany
    {
        return $this->hasMany(PerdinRincianItem::class)->orderBy('urutan');
    }

    public function dprItems(): HasMany
    {
        return $this->hasMany(PerdinDprItem::class)->orderBy('urutan');
    }

    public function getTotalBiayaAttribute(): float
    {
        return (float) $this->travelers->sum(fn ($traveler) => (float) $traveler->jumlah);
    }
}
if (false) {

/**
 * KONFIGURASI MAPPING FIELD -> CELL EXCEL
 * ========================================
 * File ini adalah SATU-SATUNYA tempat yang boleh diubah kalau posisi cell
 * di template berubah. JANGAN hardcode alamat cell di Controller/Service.
 *
 * Struktur per sheet:
 *  - 'sheet'        nama worksheet PERSIS seperti di Template PJ.xlsx
 *  - 'fields'       field tunggal => cell tujuan (mis. 'E10')
 *  - 'prefix_fields' field yang harus ditulis sebagai ": <isi>" pada cell
 *                    yang sama (contoh: "Hal" ditulis di cell C4 sebagai
 *                    ": Pertanggungjawaban Perjalanan Dinas")
 *  - 'protected_formula_cells' cell yang SUDAH berisi formula pada template
 *                    (mis. terbilang otomatis) -> tidak boleh pernah ditimpa
 *  - 'table'        definisi tabel dinamis (baris bisa ditambah user)
 *       'start_row'   baris pertama data
 *       'style_row'   baris acuan gaya/border saat insert baris baru
 *       'total_row'   baris total (formula-nya dipertahankan)
 *       'columns'     map nama_kolom => huruf kolom Excel
 */

return [

    // Nama file master. JANGAN pernah ditimpa. Sistem selalu COPY dulu.
    'template_path' => 'templates/Template_PJ.xlsx',

    // Folder hasil generate (di dalam storage/app)
    'generated_path' => 'generated',

    // -----------------------------------------------------------------
    // 1. PERTANGGUNGJAWABAN PERDIN
    // -----------------------------------------------------------------
    'pertanggung_jawaban' => [
        'sheet' => 'pertanggung jawaban PERDIN',

        // CATATAN: sheet ini 1 baris lebih "pendek" daripada PPA Perdin di
        // blok Maksud/Surat Tugas (E10-E15, bukan E10-E16) dan di blok
        // tanda tangan (baris 25 & 39, bukan 26 & 40) — makanya alamat
        // cell-nya BEDA dari sheet PPA meskipun isi datanya sama persis.
        'fields' => [
            'nomor'                 => 'C3',
            'hal'                   => 'C4',
            'maksud_perjalanan'     => 'E10',
            'surat_tugas_jabatan'   => 'E11',
            'nomor_st'              => 'E12',
            'tanggal_st'            => 'E13',
            'nomor_rk'              => 'E14',
            'pembebanan_anggaran'   => 'E15',

            // Blok tanda tangan (Disetujui / Diverifikasi / Mengetahui / Diajukan)
            'kota_tanggal_ttd_1'    => 'A25',
            'kota_tanggal_ttd_2'    => 'G25',
            'kota_tanggal_ttd_3'    => 'M25',
            'kota_tanggal_ttd_4'    => 'P25',
            'nama_ppk'              => 'A39',
            'nip_ppk'               => 'A40',
            'nama_kabag_keuangan'   => 'G39',
            'nip_kabag_keuangan'    => 'G40',
            'nama_mengetahui'       => 'M39',
            'nama_pengaju'          => 'P39',
        ],

        'prefix_fields' => ['hal'],

        'table' => [
            'start_row' => 20,
            'style_row' => 20,
            'total_row' => 22, // baris "Jumlah"
            'columns' => [
                'no'             => 'A',
                'nama'           => 'B',
                'jabatan'        => 'E',
                'es'             => 'H',
                'gol'            => 'I',
                'dari'           => 'J',
                'ke'             => 'K',
                'tanggal_mulai'  => 'L',
                'tanggal_sampai' => 'M',
                'hari'           => 'N',
                'uang_harian'    => 'O',
                'penginapan'     => 'P',
                'represen'       => 'Q',
                'tiket'          => 'R',
                'transportasi'   => 'S',
                'sewa_kendaraan' => 'T',
                'jumlah'         => 'U',
            ],
        ],
    ],

    // -----------------------------------------------------------------
    // 2. PPA PERDIN (Permintaan & Pembebanan Anggaran)
    // -----------------------------------------------------------------
    'ppa' => [
        'sheet' => 'PPA Perdin',

        'fields' => [
            'nomor'                 => 'C3',
            'hal'                   => 'C4',
            'maksud_perjalanan'     => 'E10',
            'surat_tugas_jabatan'   => 'E12',
            'nomor_st'              => 'E13',
            'tanggal_st'            => 'E14',
            'nomor_rk'              => 'E15',
            'pembebanan_anggaran'   => 'E16',

            'kota_tanggal_ttd_1'    => 'A26',
            'kota_tanggal_ttd_2'    => 'F26',
            'kota_tanggal_ttd_3'    => 'M26',
            'kota_tanggal_ttd_4'    => 'P26',
            'nama_ppk'              => 'A40',
            'nip_ppk'               => 'A41',
            'nama_kabag_keuangan'   => 'F40',
            'nip_kabag_keuangan'    => 'F41',
            'nama_mengetahui'       => 'M40',
            'nama_pengaju'          => 'P40',

            // Kotak "Catatan: Kolom yang mengajukan sesuai Jabatan
            // (Sesuai dgn SBU TA 2026)" — dulu belum ada di mapping ini
            // jadi walaupun sudah diisi di web, gak pernah nyampe ke Excel.
            'nama_pengaju_ppa'      => 'A56',
            'nip_pengaju_ppa'       => 'A57',
        ],

        'prefix_fields' => ['hal'],

        'table' => [
            'start_row' => 21,
            'style_row' => 21,
            'total_row' => 23,
            'columns' => [
                'no'             => 'A',
                'nama'           => 'B',
                'jabatan'        => 'E',
                'es'             => 'H',
                'gol'            => 'I',
                'dari'           => 'J',
                'ke'             => 'K',
                'tanggal_mulai'  => 'L',
                'tanggal_sampai' => 'M',
                'hari'           => 'N',
                'uang_harian'    => 'O',
                'penginapan'     => 'P',
                'represen'       => 'Q',
                'tiket'          => 'R',
                'transportasi'   => 'S',
                'sewa_kendaraan' => 'T',
                'jumlah'         => 'U',
            ],
        ],
    ],

    // -----------------------------------------------------------------
    // 3. KWITANSI (Kwit PERDIN 1)
    // -----------------------------------------------------------------
    'kwitansi' => [
        'sheet' => 'Kwit PERDIN 1',

        'fields' => [
            'tahun_anggaran'      => 'J4',
            'nomor_bukti'         => 'J5',
            'mak'                 => 'J6',
            'sudah_terima_dari'   => 'D11',
            'jumlah_uang'         => 'D13',
            'terbilang'           => 'D14', // auto dari angka_ke_terbilang()
            'untuk_pembayaran'    => 'D17',
            // "Jakarta, ... " di baris Yang bepergian (G24) dan "Dibayar
            // lunas, Tgl ..." (H37) sekarang dua field terpisah, supaya
            // tanggal bepergian & tanggal lunas bisa beda-beda.
            'kota_tanggal_bepergian' => 'G24',
            'kota_tanggal_lunas'     => 'H37',
            'nama_yang_bepergian' => 'G34',
            'nama_ppk'            => 'A47',
            'nip_ppk'             => 'A48',
            'nama_bendahara'      => 'H47',
            'nip_bendahara'       => 'H48',
        ],
    ],

    // -----------------------------------------------------------------
    // 4. RINCIAN (Rincian PERDIN 1)
    // -----------------------------------------------------------------
    'rincian' => [
        'sheet' => 'Rincian PERDIN 1',

        'fields' => [
            'lampiran_sppd_no' => 'E10',
            'tanggal_sppd'     => 'E11',
            'kota_tanggal_ttd' => 'G32',
            // A42/G42 sudah berisi teks label "Nama" di template — nilainya
            // harus MENIMPA cell itu juga (bukan cell di bawahnya / A43,
            // G43), kalau tidak nama yang diisi malah nongol di bawah
            // tulisan "Nama" bukan menggantikannya.
            'nama_bendahara'   => 'A42',
            'nip_bendahara'    => 'A43',
            'nama_bepergian'   => 'G42',
            'nama_mengetahui_rincian' => 'F56',
            'nip_mengetahui_rincian'  => 'F57',
        ],

        // Cell ini SUDAH berisi formula terbilang & jumlah pada template.
        // JANGAN PERNAH ditimpa dengan angka statis, biarkan Excel yang hitung.
        'protected_formula_cells' => ['F29', 'E30', 'F47', 'F48', 'F49'],

        'table' => [
            'start_row' => 15,
            'style_row' => 15,
            'total_row' => 29,
            'columns' => [
                'no'                  => 'A',
                'uraian'              => 'B',
                'keterangan_tambahan' => 'C',
                'jumlah_satuan'       => 'D',
                'harga_satuan'        => 'E',
                'jumlah'              => 'F',
                'keterangan'          => 'G',
            ],
        ],
    ],

    // -----------------------------------------------------------------
    // 5. DPR PERDIN (Daftar Pengeluaran Riil)
    // -----------------------------------------------------------------
    'dpr' => [
        'sheet' => 'DPR PERDIN',

        'fields' => [
            'nama'             => 'F11',
            'nip'              => 'F12',
            'jabatan'          => 'F13',
            'tanggal_spd_text' => 'B15', // "...tanggal 30 juni 2026"
            'nomor_spd'        => 'B16',
            // "Yang bepergian," + kota/tanggal-nya sekarang sejajar dengan
            // blok "Mengetahui/Menyetujui" (G42/G43), BUKAN G44/G45 seperti
            // sebelumnya — makanya dulu keliatan turun 2 baris di hasil
            // cetak. Lihat juga perbaikan Template_PJ.xlsx (G42 dibersihkan
            // dari formula eksternal rusak, teks "Yang bepergian" dipindah
            // dari G44 ke G42).
            'kota_tanggal_ttd' => 'G43',
            // C49/B50 di template SUDAH berisi teks label "Nama"/"NIP" —
            // nilainya harus MENIMPA cell itu juga (pola yang sama dipakai
            // di sheet Rincian), BUKAN ditulis 3 baris di bawahnya (C52/B53)
            // seperti sebelumnya — itu penyebab Nama/NIP PPK kelihatan
            // nyasar ke bawah garis tanda tangan yang salah.
            'nama_ppk'         => 'C49',
            'nip_ppk'          => 'B50',
            // G49 sebenarnya sudah auto-formula "=F11" di template, tapi
            // karena disimpan dengan setPreCalculateFormulas(false), Excel
            // bisa nampilin 0/blank sebelum di-recalculate manual. Jadi
            // kita timpa langsung dengan nilai final, sama seperti pola
            // total/terbilang lain di proyek ini.
            'nama_bepergian'   => 'G49',
        ],

        'protected_formula_cells' => ['F35'], // terbilang otomatis

        'table' => [
            'start_row' => 23,
            'style_row' => 23,
            'total_row' => 34,
            'columns' => [
                'no'     => 'B',
                'uraian' => 'C',
                'jumlah' => 'G',
            ],
        ],
    ],

    // -----------------------------------------------------------------
    // 6. PERNYATAAN (Surat Pernyataan Tidak Menggunakan Kendaraan Dinas)
    // -----------------------------------------------------------------
    'pernyataan' => [
        'sheet' => 'pernyataan',

        'fields' => [
            // B15 adalah cell khusus (merge B15:F15) buat teks "Melakukan
            // Perjalanan Dinas ke ..." — sebelumnya salah ditulis ke B17
            // (dulu nyasar 2 baris di bawah posisi aslinya).
            'maksud_perjalanan' => 'B15',
        ],

        // Kotak tabel di Template_PJ CUMA didesain buat 2 baris (baris 12 &
        // 13) — nomor 1 & 2 sudah dicetak di template, tanpa border/style
        // di baris berikutnya. Makanya di fillPernyataan() peserta dibatasi
        // maksimal 2 orang: kalau ditambah lebih, datanya ditulis ke baris
        // tanpa format tabel dan mepet/nabrak teks B15. Sengaja TIDAK dikasih
        // 'total_row' supaya tidak ada auto-insert baris baru di sini.
        'max_rows' => 2,

        'table' => [
            'start_row' => 12,
            'style_row' => 12,
            'columns' => [
                'no'      => 'B',
                'nama'    => 'C',
                'jabatan' => 'E',
                // kolom F "Tanda Tangan" sengaja dikosongkan, diisi manual
            ],
        ],
    ],

];
}