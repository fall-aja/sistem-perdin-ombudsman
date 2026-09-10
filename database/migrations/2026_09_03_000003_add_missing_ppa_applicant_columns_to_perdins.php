<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            if (! Schema::hasColumn('perdins', 'nama_pengaju_ppa')) {
                $table->string('nama_pengaju_ppa')->nullable()->after('nip_pengaju');
            }

            if (! Schema::hasColumn('perdins', 'nip_pengaju_ppa')) {
                $table->string('nip_pengaju_ppa')->nullable()->after('nama_pengaju_ppa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('perdins', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('perdins', 'nama_pengaju_ppa')) {
                $columns[] = 'nama_pengaju_ppa';
            }

            if (Schema::hasColumn('perdins', 'nip_pengaju_ppa')) {
                $columns[] = 'nip_pengaju_ppa';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};