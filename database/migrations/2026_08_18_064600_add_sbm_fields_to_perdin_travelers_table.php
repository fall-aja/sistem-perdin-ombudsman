<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perdin_travelers', function (Blueprint $table) {
            $table->string('sbm_provinsi')->nullable()->after('sewa_kendaraan');
            $table->string('sbm_jenis', 20)->nullable()->after('sbm_provinsi');
            $table->string('sbm_hotel_kelas', 20)->nullable()->after('sbm_jenis');
            $table->string('hari_mode', 20)->nullable()->after('sbm_hotel_kelas');
        });
    }

    public function down(): void
    {
        Schema::table('perdin_travelers', function (Blueprint $table) {
            $table->dropColumn(['sbm_provinsi', 'sbm_jenis', 'sbm_hotel_kelas', 'hari_mode']);
        });
    }
};
