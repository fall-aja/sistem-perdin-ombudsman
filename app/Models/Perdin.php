<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perdin extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tanggal_st' => 'date',
        'tanggal_tanda_tangan' => 'date',
        'tanggal_sppd' => 'date',
        'tanggal_spd' => 'date',
        'tanggal_bepergian' => 'date',
        'tanggal_lunas' => 'date',
        'pernyataan_tidak_menggunakan_kendaraan' => 'boolean',
    ];

    public function travelers(): HasMany
    {
        return $this->hasMany(PerdinTraveler::class)->orderBy('urutan');
    }

    public function rincianItems(): HasMany
    {
        return $this->hasMany(PerdinRincianItem::class)->orderBy('urutan');
    }

    public function dprItems(): HasMany
    {
        return $this->hasMany(PerdinDprItem::class)->orderBy('urutan');
    }

    public function getTotalBiayaAttribute(): float
    {
        return (float) $this->travelers->sum(fn ($traveler) => (float) $traveler->jumlah);
    }
}