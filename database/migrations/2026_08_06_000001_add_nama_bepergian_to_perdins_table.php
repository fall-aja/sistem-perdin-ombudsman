<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            $table->string('nama_bepergian')->nullable()->after('tanggal_sppd');
            $table->text('untuk_pembayaran')->nullable()->after('jumlah_uang_kwitansi');
        });
    }

    public function down(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            $table->dropColumn(['nama_bepergian', 'untuk_pembayaran']);
        });
    }
};
