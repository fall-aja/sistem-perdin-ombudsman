<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perdins', function (Blueprint $table) {
            $table->id();

            // --- Header umum (dipakai sheet Pertanggungjawaban & PPA) ---
            $table->string('nomor')->nullable();
            $table->string('hal')->nullable();
            $table->text('maksud_perjalanan')->nullable();
            $table->string('surat_tugas_jabatan')->nullable();
            $table->string('nomor_st')->nullable();
            $table->date('tanggal_st')->nullable();
            $table->string('nomor_rk')->nullable();
            $table->string('pembebanan_anggaran')->nullable();
            $table->string('kota_tanda_tangan')->default('Jakarta');
            $table->date('tanggal_tanda_tangan')->nullable();

            // --- Penandatangan ---
            $table->string('nama_ppk')->nullable();
            $table->string('nip_ppk')->nullable();
            $table->string('nama_kabag_keuangan')->nullable();
            $table->string('nip_kabag_keuangan')->nullable();
            $table->string('nama_mengetahui')->nullable();
            $table->string('nama_pengaju')->nullable();
            $table->string('nip_pengaju')->nullable();

            // --- Kwitansi ---
            $table->string('tahun_anggaran')->nullable();
            $table->string('nomor_bukti_kwitansi')->nullable();
            $table->string('mak')->nullable();
            $table->string('sudah_terima_dari')->default('BENDAHARA PENGELUARAN OMBUDSMAN RI');
            $table->unsignedBigInteger('jumlah_uang_kwitansi')->nullable();
            $table->string('nama_bendahara')->nullable();
            $table->string('nip_bendahara')->nullable();

            // --- Rincian ---
            $table->string('lampiran_sppd_no')->nullable();
            $table->date('tanggal_sppd')->nullable();

            // --- DPR (Daftar Pengeluaran Riil) ---
            $table->string('dpr_nama')->nullable();
            $table->string('dpr_nip')->nullable();
            $table->string('dpr_jabatan')->nullable();
            $table->string('nomor_spd')->nullable();
            $table->date('tanggal_spd')->nullable();

            $table->enum('status', ['draft', 'generated'])->default('draft');
            $table->string('generated_excel_path')->nullable();
            $table->string('generated_pdf_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perdins');
    }
};
