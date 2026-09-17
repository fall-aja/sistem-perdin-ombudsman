<div align="center">

<img src="public/images/ori_square.png" alt="Logo Ombudsman RI" width="140"/>

# 🧳 Sistem PERDIN — Ombudsman RI

### *Platform Terintegrasi Manajemen Dokumen Perjalanan Dinas*

**Otomatisasi Pembuatan Dokumen PPA, Kwitansi, Rincian, DPR & Pernyataan**

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-Ready-8BC0D0?style=flat&logo=alpine.js&logoColor=white)
![Tailwind](https://img.shields.io/badge/TailwindCSS-Styling-06B6D4?style=flat&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-Private-red?style=flat)

</div>

---

## 📑 Daftar Isi

| No | Modul | Fokus Bahasan |
|----|-------|----------------|
| 01 | [🎯 Ikhtisar](#-ikhtisar) | Latar belakang, tujuan, dan value proposition sistem |
| 02 | [💎 Fitur Unggulan](#-fitur-unggulan) | Enam jenis dokumen, sinkronisasi otomatis, employee picker |
| 03 | [🧩 Arsitektur Sistem](#-arsitektur-sistem) | Alur generate dokumen, sinkronisasi antar jenis dokumen |
| 04 | [🛠️ Tech Stack](#️-tech-stack) | Framework, library, dan tools yang digunakan |
| 05 | [📂 Struktur Proyek](#-struktur-proyek) | Blueprint folder dan peran tiap komponen |
| 06 | [⚡ Instalasi Lokal](#-instalasi-lokal) | Setup via Laragon, konfigurasi `.env`, migrasi database |
| 07 | [🔑 Data Sensitif](#-data-sensitif) | Catatan tentang data pegawai dan status repository |
| 08 | [🛡️ Catatan Keamanan](#️-catatan-keamanan) | Pengelolaan kredensial dan data internal |

---

## 🎯 Ikhtisar

Sistem PERDIN dibangun untuk menggantikan proses manual pembuatan dokumen perjalanan dinas (yang sebelumnya berbasis PHP native + Excel manual) di lingkungan Ombudsman Republik Indonesia. Sistem ini mengotomatisasi pembuatan enam jenis dokumen perjalanan dinas sekaligus menjaga konsistensi data antar dokumen.

## 💎 Fitur Unggulan

- **Enam Jenis Dokumen Terintegrasi** — Pertanggungjawaban, PPA, Kwitansi, Rincian, DPR, dan Pernyataan
- **Sinkronisasi Otomatis** — perubahan data di PPA otomatis tersinkron ke Pertanggungjawaban, Rincian, dan DPR
- **Generate Excel & PDF** — output multi-sheet dari template master (`Template_PJ.xlsx`) menggunakan PhpSpreadsheet
- **Employee Picker** — pencarian dan pemilihan pegawai terintegrasi dengan data pegawai internal
- **Perhitungan Otomatis** — Uang Harian dan Penginapan dihitung berdasarkan tarif × jumlah hari
- **Terbilang Otomatis** — konversi nominal angka ke format terbilang Bahasa Indonesia
- **Manajemen Surat Tugas**

## 🧩 Arsitektur Sistem

Alur singkat pembuatan dokumen:

1. User mengisi form PPA (jenis dokumen sumber utama)
2. `ExcelGeneratorService` memetakan data ke sel-sel Excel sesuai `config/perdin.php`
3. Data yang sama otomatis disebar ke dokumen terkait (Pertanggungjawaban, Rincian, DPR)
4. Dokumen di-generate sebagai file Excel (dan/atau PDF) dari `Template_PJ.xlsx`

## 🛠️ Tech Stack

| Kategori | Teknologi |
|----------|-----------|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend | Blade, Alpine.js, Tailwind CSS |
| Database | MySQL |
| Generator Dokumen | PhpSpreadsheet |
| Environment | Laragon (lokal) |

## 📂 Struktur Proyek

```
app/
├── Http/Controllers/     # PerdinController, DashboardController, SuratTugasController
├── Models/               # Perdin, SuratTugas, Pegawai
└── Services/             # ExcelGeneratorService, PdfGeneratorService, TemplateService
config/
└── perdin.php            # Mapping sel Excel per jenis dokumen
database/
├── migrations/
└── seeders/              # PegawaiSeeder (data pegawai)
resources/views/
└── components/perdin/    # form.blade.php, pegawai-picker.blade.php
```

## ⚡ Instalasi Lokal

```bash
# Clone repository
git clone https://github.com/fall-aja/sistem-perdin-ombudsman.git
cd sistem-perdin-ombudsman

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi koneksi database di file .env, lalu migrasi + seed
php artisan migrate --seed

# Build asset frontend
npm run build

# Jalankan server lokal
php artisan serve
```

## 🔑 Data Sensitif

Seeder `PegawaiSeeder` memuat data pegawai asli (NIP, nama, jabatan, penempatan) dari `database/seeders/data/pegawai.json` untuk keperluan fitur pencarian pegawai.

## 🛡️ Catatan Keamanan

- Repository ini **wajib bersifat Private** karena memuat data pegawai internal
- Jangan pernah meng-commit file `.env` asli (gunakan `.env.example` sebagai referensi)
- Akses ke repository hanya diberikan melalui undangan collaborator, bukan dengan mengubah visibility menjadi publik

---

<div align="center">

Dikembangkan untuk kebutuhan internal **Ombudsman Republik Indonesia**

</div>