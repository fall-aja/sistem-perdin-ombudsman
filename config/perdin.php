<?php

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

        'fields' => [
            'nomor'                 => 'C3',
            'hal'                   => 'C4',
            'maksud_perjalanan'     => 'E10',
            'surat_tugas_jabatan'   => 'E12',
            'nomor_st'              => 'E13',
            'tanggal_st'            => 'E14',
            'nomor_rk'              => 'E15',
            'pembebanan_anggaran'   => 'E16',

            // Blok tanda tangan (Disetujui / Diverifikasi / Mengetahui / Diajukan)
            'kota_tanggal_ttd_1'    => 'A28',
            'kota_tanggal_ttd_2'    => 'G28',
            'kota_tanggal_ttd_3'    => 'M28',
            'kota_tanggal_ttd_4'    => 'P28',
            'nama_ppk'              => 'A42',
            'nip_ppk'               => 'A43',
            'nama_kabag_keuangan'   => 'G42',
            'nip_kabag_keuangan'    => 'G43',
            'nama_mengetahui'       => 'M42',
            'nama_pengaju'          => 'P42',
        ],

        'prefix_fields' => ['hal'],

        'table' => [
            'start_row' => 21,
            'style_row' => 21,
            'total_row' => 23, // baris "Jumlah"
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

            'kota_tanggal_ttd_1'    => 'A28',
            'kota_tanggal_ttd_2'    => 'F28',
            'kota_tanggal_ttd_3'    => 'M28',
            'kota_tanggal_ttd_4'    => 'P28',
            'nama_ppk'              => 'A42',
            'nip_ppk'               => 'A43',
            'nama_kabag_keuangan'   => 'F42',
            'nip_kabag_keuangan'    => 'F43',
            'nama_mengetahui'       => 'M42',
            'nama_pengaju'          => 'P42',
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
            'kota_tanggal_ttd'    => 'G24',
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
            'nama_bendahara'   => 'A43',
            'nip_bendahara'    => 'A44',
            'nama_bepergian'   => 'G43',
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
            'kota_tanggal_ttd' => 'G45',
            'nama_ppk'         => 'C52',
            'nip_ppk'          => 'B53',
            'nama_bepergian'   => 'G52',
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
            'maksud_perjalanan' => 'B17', // "dalam <maksud perjalanan>"
        ],

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
