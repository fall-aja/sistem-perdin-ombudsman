<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perdin_sheet_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('file_name')->nullable();
            $table->string('sheet_name')->nullable();
            $table->string('judul_dokumen')->nullable();
            $table->text('teks_awalan')->nullable();
            $table->text('teks_penutup')->nullable();
            $table->string('target_cell_awalan')->nullable();
            $table->string('target_cell_penutup')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perdin_sheet_templates');
    }
};