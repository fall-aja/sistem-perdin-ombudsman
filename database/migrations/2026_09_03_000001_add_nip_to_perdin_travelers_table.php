<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perdin_travelers', function (Blueprint $table) {
            $table->string('nip', 50)->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('perdin_travelers', function (Blueprint $table) {
            $table->dropColumn('nip');
        });
    }
};