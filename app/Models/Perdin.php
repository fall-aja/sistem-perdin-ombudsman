<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perdin extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_st'           => 'date',
        'tanggal_tanda_tangan' => 'date',
        'tanggal_sppd'         => 'date',
        'tanggal_spd'          => 'date',
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

    /**
     * Total biaya perjalanan dinas (dipakai untuk kuitansi & preview).
     */
    public function getTotalBiayaAttribute(): int
    {
        return $this->travelers->sum(fn (PerdinTraveler $t) => $t->jumlah);
    }
}
