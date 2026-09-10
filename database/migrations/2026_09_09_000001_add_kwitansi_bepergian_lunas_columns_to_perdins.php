<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            // Kwitansi sekarang punya kota & tanggal sendiri buat baris
            // "Yang bepergian" dan "Dibayar lunas" — dipisah dari kolom
            // kota_tanda_tangan/tanggal_tanda_tangan umum yang dipakai
            // sheet Pertanggungjawaban/PPA/Rincian/DPR.
            if (! Schema::hasColumn('perdins', 'kota_bepergian')) {
                $table->string('kota_bepergian')->default('Jakarta')->after('nip_bendahara');
            }

            if (! Schema::hasColumn('perdins', 'tanggal_bepergian')) {
                $table->date('tanggal_bepergian')->nullable()->after('kota_bepergian');
            }

            if (! Schema::hasColumn('perdins', 'kota_lunas')) {
                $table->string('kota_lunas')->default('Jakarta')->after('tanggal_bepergian');
            }

            if (! Schema::hasColumn('perdins', 'tanggal_lunas')) {
                $table->date('tanggal_lunas')->nullable()->after('kota_lunas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            $columns = [];

            foreach (['kota_bepergian', 'tanggal_bepergian', 'kota_lunas', 'tanggal_lunas'] as $col) {
                if (Schema::hasColumn('perdins', $col)) {
                    $columns[] = $col;
                }
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};