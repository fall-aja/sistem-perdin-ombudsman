<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    protected $fillable = [
        'nomor',
        'tanggal',
        'mak',
        'keterangan',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
