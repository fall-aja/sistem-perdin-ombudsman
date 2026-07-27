<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Baris peserta -> tabel di sheet "pertanggung jawaban PERDIN" & "PPA Perdin"
        Schema::create('perdin_travelers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perdin_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(1);
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('es')->nullable();
            $table->string('gol')->nullable();
            $table->string('dari')->nullable();
            $table->string('ke')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_sampai')->nullable();
            $table->unsignedInteger('hari')->default(1);
            $table->unsignedBigInteger('uang_harian')->default(0);
            $table->unsignedBigInteger('penginapan')->default(0);
            $table->unsignedBigInteger('represen')->default(0);
            $table->unsignedBigInteger('tiket')->default(0);
            $table->unsignedBigInteger('transportasi')->default(0);
            $table->unsignedBigInteger('sewa_kendaraan')->default(0);
            $table->timestamps();
        });

        // Baris rincian biaya -> sheet "Rincian PERDIN 1"
        Schema::create('perdin_rincian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perdin_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(1);
            $table->string('uraian');
            $table->string('keterangan_tambahan')->nullable();
            $table->decimal('jumlah_satuan', 12, 2)->default(1);
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // Baris pengeluaran riil -> sheet "DPR PERDIN"
        Schema::create('perdin_dpr_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perdin_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(1);
            $table->string('uraian');
            $table->unsignedBigInteger('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perdin_dpr_items');
        Schema::dropIfExists('perdin_rincian_items');
        Schema::dropIfExists('perdin_travelers');
    }
};
