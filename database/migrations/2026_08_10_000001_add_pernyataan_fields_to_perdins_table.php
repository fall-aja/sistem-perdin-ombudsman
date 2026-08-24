<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            $table->boolean('pernyataan_tidak_menggunakan_kendaraan')->default(false)->after('tanggal_spd');
            $table->text('pernyataan_teks')->nullable()->after('pernyataan_tidak_menggunakan_kendaraan');
        });
    }

    public function down(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            $table->dropColumn(['pernyataan_tidak_menggunakan_kendaraan', 'pernyataan_teks']);
        });
    }
};
