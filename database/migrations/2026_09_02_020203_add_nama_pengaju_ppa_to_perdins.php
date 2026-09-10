<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('perdins', 'nama_pengaju_ppa') && Schema::hasColumn('perdins', 'nip_pengaju_ppa')) {
            return;
        }

        Schema::table('perdins', function (Blueprint $table) {
            // Nama & NIP khusus sheet PPA, bagian "Catatan: Kolom yang
            // mengajukan sesuai Jabatan" (A56/A57 di template PPA)
            if (! Schema::hasColumn('perdins', 'nama_pengaju_ppa')) {
                $table->string('nama_pengaju_ppa')->nullable()->after('nama_pengaju');
            }

            if (! Schema::hasColumn('perdins', 'nip_pengaju_ppa')) {
                $table->string('nip_pengaju_ppa')->nullable()->after('nama_pengaju_ppa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            if (Schema::hasColumn('perdins', 'nip_pengaju_ppa')) {
                $table->dropColumn('nip_pengaju_ppa');
            }

            if (Schema::hasColumn('perdins', 'nama_pengaju_ppa')) {
                $table->dropColumn('nama_pengaju_ppa');
            }
        });
    }
};