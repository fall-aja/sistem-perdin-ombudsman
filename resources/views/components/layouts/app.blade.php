<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem PERDIN — ORI</title>
    <link rel="icon" href="/images/ori_square.png" type="image/png">
    <link rel="apple-touch-icon" href="/images/ori_square.png">

    {{-- Font: Source Serif 4 untuk judul dokumen (kesan surat/dokumen resmi),
         Public Sans untuk UI & isian form (dipakai USWDS, dirancang untuk
         layanan pemerintah — legible di ukuran kecil, padat data). --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">

    {{-- Tailwind via CDN untuk kemudahan setup awal.
         Untuk produksi, ganti dengan build Vite (lihat README langkah 7). --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Public Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['"Source Serif 4"', 'ui-serif', 'Georgia', 'serif'],
                    },
                    colors: {
                        paper: '#f6f4ee',
                        ink: '#1b2332',
                        navy: { 50: '#eaf1f7', 100: '#cfe0ee', 300: '#5c93bf', 500: '#0b4f8a', 600: '#0a4478', 700: '#0a3a66', 900: '#0f2438' },
                        amber: { 50: '#faf3e4', 100: '#f0dfb9', 300: '#dcac57', 500: '#c68a2e', 600: '#a9741f' },
                        maroon: { 50: '#fbeaec', 500: '#a32638', 600: '#8a1f2f' },
                        line: '#ddd6c8',
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        html, body {
            font-size: 17px;
            font-family: 'Public Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            line-height: 1.5;
        }

        /* ============================================================
           Komponen isian form Sistem PERDIN — didefinisikan sekali di
           sini lalu dipakai lewat class .pd-* di seluruh form/preview,
           supaya konsisten dan gampang diubah dari 1 tempat.
           ============================================================ */

        .pd-label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .01em;
            color: #6b6455;
        }

        .pd-input,
        .pd-select,
        .pd-textarea {
            display: block;
            width: 100%;
            margin-top: .3rem;
            padding: .5rem .75rem;
            font-size: .875rem;
            color: #1b2332;
            background: #fff;
            border: 1px solid #ddd6c8;
            border-radius: .5rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .pd-select { background: #fff; }

        .pd-input::placeholder,
        .pd-textarea::placeholder { color: #a39c8a; }

        .pd-input:focus,
        .pd-select:focus,
        .pd-textarea:focus {
            outline: none;
            border-color: #0b4f8a;
            box-shadow: 0 0 0 3px rgba(11, 79, 138, .13);
        }

        .pd-input:disabled { background: #f3f1ea; color: #9c9585; cursor: not-allowed; }

        /* Blok yang nilainya hasil hitungan otomatis (tarif SBM) — dikasih
           tint amber supaya kelihatan beda dari isian manual biasa. */
        .pd-input-computed {
            border-color: #dcac57;
            background: #faf3e4;
        }

        /* Header tiap section dokumen (mis. "PERTANGGUNGJAWABAN PERDIN") */
        .pd-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .8rem 1.1rem;
            background: #0f2438;
            border-radius: .75rem;
            position: relative;
            overflow: hidden;
        }

        .pd-section-header::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, #5c93bf, #c68a2e);
        }

        .pd-section-title {
            font-family: 'Source Serif 4', serif;
            font-size: .95rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: .01em;
        }

        .pd-section-desc {
            font-size: .72rem;
            color: #a9b8c9;
            margin-top: .1rem;
        }

        .pd-section-wrap {
            padding: 1rem;
            background: #fbfaf6;
            border: 1px solid #e7e2d4;
            border-radius: 1.25rem;
        }

        .pd-subheading {
            font-family: 'Source Serif 4', serif;
            font-size: .95rem;
            font-weight: 600;
            color: #1b2332;
            padding-top: .75rem;
            border-top: 1px solid #e7e2d4;
        }

        /* Kartu untuk 1 entri berulang (peserta / rincian / DPR) — garis
           kiri amber jadi penanda "ini 1 baris data yang bisa ditambah". */
        .pd-item-card {
            position: relative;
            padding: 1rem 1rem 1rem 1.25rem;
            border: 1px solid #e7e2d4;
            border-left: 3px solid #c68a2e;
            border-radius: .6rem;
            background: #fdfcf9;
        }

        .pd-remove-btn {
            position: absolute;
            top: .75rem;
            right: .9rem;
            font-size: .72rem;
            font-weight: 500;
            color: #a32638;
        }

        .pd-remove-btn:hover { text-decoration: underline; }

        .pd-add-btn {
            font-size: .85rem;
            font-weight: 600;
            color: #0a4478;
        }

        .pd-add-btn:hover { text-decoration: underline; }

        .pd-dot-error {
            width: .5rem; height: .5rem; border-radius: 999px;
            background: #a32638; flex-shrink: 0;
        }

        .pd-dropdown {
            position: absolute; z-index: 20; margin-top: .25rem; width: 100%;
            background: #fff; border: 1px solid #e7e2d4; border-radius: .6rem;
            box-shadow: 0 10px 30px -12px rgba(15, 36, 56, .25);
            max-height: 14rem; overflow-y: auto;
        }

        .pd-dropdown-item {
            display: block; width: 100%; text-align: left;
            padding: .5rem .75rem; font-size: .875rem;
            border-bottom: 1px solid #f1eee5;
        }

        .pd-dropdown-item:last-child { border-bottom: 0; }
        .pd-dropdown-item:hover { background: #faf3e4; }

        /* Sidebar nav aktif — navy brand, bukan biru generik */
        .pd-nav-active {
            background: #0b4f8a;
            border-color: rgba(92, 147, 191, .5);
            color: #fff;
        }

        .pd-nav-idle:hover { background: rgba(255, 255, 255, .06); }

        /* Tabel simulasi dokumen (preview) — nuansa kertas resmi */
        .pd-doc-table { width: 100%; font-size: .75rem; border: 1px solid #ddd6c8; border-collapse: collapse; }
        .pd-doc-table th { background: #0f2438; color: #fff; font-weight: 600; padding: .35rem; text-align: left; }
        .pd-doc-table td { padding: .4rem .5rem; border-bottom: 1px solid #eae6da; }
        .pd-doc-table td.pd-doc-label { font-weight: 500; background: #f6f4ee; width: 33%; }
        .pd-doc-table tr:nth-child(even) td { background: #fbfaf6; }
        .pd-doc-table tr.pd-doc-total td { font-weight: 600; background: #f0ebda; }

        /* ============================================================
           Chrome halaman (sidebar, kartu pembungkus, tombol aksi) —
           dulu masih pakai warna Tailwind default (slate/blue/emerald),
           sekarang dipindah ke token brand yang sama dengan .pd-* di atas
           supaya sidebar & isi form berasa 1 kesatuan, bukan 2 style beda.
           ============================================================ */

        /* Sidebar navy sama seperti pd-section-header, biar chrome & header
           dokumen berasa satu warna brand. */
        .pd-sidebar { background: #0f2438; border-color: rgba(255, 255, 255, .08); }
        .pd-sidebar-brand { border-color: rgba(255, 255, 255, .08); }
        .pd-sidebar-nav { border-color: rgba(255, 255, 255, .08); background: rgba(255, 255, 255, .03); }
        .pd-sidebar-eyebrow { color: rgba(169, 184, 201, .55); }
        .pd-sidebar-history { border-color: rgba(255, 255, 255, .08); }
        .pd-sidebar-history-item { color: #dce4ec; }
        .pd-sidebar-history-item:hover { background: rgba(255, 255, 255, .06); }
        .pd-sidebar-history-date { color: rgba(169, 184, 201, .55); }
        .pd-sidebar-footer { border-color: rgba(255, 255, 255, .08); }

        /* Tombol aksi — 1 warna aksen (amber) buat aksi "hasil akhir"
           (generate dokumen), navy buat aksi utama (simpan), sisanya
           quiet/outline. Sebelumnya tiap tombol warna sendiri-sendiri
           (slate/blue/emerald/red) dan berebutan perhatian. */
        .pd-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem 1.1rem; border-radius: .55rem;
            font-size: .85rem; font-weight: 600; border: 1px solid transparent;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }
        .pd-btn:disabled { opacity: .45; cursor: not-allowed; }
        .pd-btn-primary { background: #0b4f8a; color: #fff; }
        .pd-btn-primary:hover:not(:disabled) { background: #0a4478; }
        .pd-btn-outline { background: transparent; border-color: #c9d6e2; color: #0a3a66; }
        .pd-btn-outline:hover:not(:disabled) { background: #eaf1f7; }
        .pd-btn-accent { background: #c68a2e; color: #fff; }
        .pd-btn-accent:hover:not(:disabled) { background: #a9741f; }
        .pd-btn-ghost { background: transparent; border: 1px solid #ddd6c8; color: #6b6455; }
        .pd-btn-ghost:hover:not(:disabled) { background: #f6f4ee; }

        /* Kartu putih pembungkus form/preview — dulu rounded-xl polos,
           sekarang senada sama pd-item-card/pd-section-wrap (border warm,
           bukan abu Tailwind default). */
        .pd-surface { background: #fff; border: 1px solid #e7e2d4; border-radius: 1rem; box-shadow: 0 1px 2px rgba(15, 36, 56, .04); }
    </style>
</head>

<body class="bg-paper text-ink antialiased">
    {{ $slot }}
</body>

</html>