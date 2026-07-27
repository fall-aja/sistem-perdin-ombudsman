<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerdinRincianItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function perdin(): BelongsTo
    {
        return $this->belongsTo(Perdin::class);
    }

    public function getJumlahAttribute(): int
    {
        return (int) round($this->jumlah_satuan * $this->harga_satuan);
    }
}
