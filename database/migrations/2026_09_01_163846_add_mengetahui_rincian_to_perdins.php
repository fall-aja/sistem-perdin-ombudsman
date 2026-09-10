<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('perdins', 'nama_mengetahui_rincian') && Schema::hasColumn('perdins', 'nip_mengetahui_rincian')) {
            return;
        }

        Schema::table('perdins', function (Blueprint $table) {
            // Nama & NIP yang tampil di sheet Rincian, bagian bawah
            // "Mengetahui/Menyetujui" (F57/F58 di template)
            if (! Schema::hasColumn('perdins', 'nama_mengetahui_rincian')) {
                $table->string('nama_mengetahui_rincian')->nullable()->after('nip_bendahara');
            }

            if (! Schema::hasColumn('perdins', 'nip_mengetahui_rincian')) {
                $table->string('nip_mengetahui_rincian')->nullable()->after('nama_mengetahui_rincian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            if (Schema::hasColumn('perdins', 'nip_mengetahui_rincian')) {
                $table->dropColumn('nip_mengetahui_rincian');
            }

            if (Schema::hasColumn('perdins', 'nama_mengetahui_rincian')) {
                $table->dropColumn('nama_mengetahui_rincian');
            }
        });
    }
};
