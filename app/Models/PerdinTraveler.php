<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerdinTraveler extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai'  => 'date',
        'tanggal_sampai' => 'date',
    ];

    public function perdin(): BelongsTo
    {
        return $this->belongsTo(Perdin::class);
    }

    /**
     * Jumlah per baris:
     * - Uang Harian & Penginapan diinput sebagai TARIF (per hari / per malam),
     *   bukan total. Uang Harian dikalikan jumlah hari dinas.
     * - Penginapan dikalikan jumlah MALAM (hari - 1), karena hari terakhir
     *   biasanya pulang, tidak menginap lagi.
     * - Represen, Tiket, Transportasi, Sewa Kendaraan tetap dianggap total
     *   (bukan tarif harian).
     */
    public function getJumlahAttribute(): int
    {
        $hari = max((int) $this->hari, 1);
        $malam = max($hari - 1, 0);

        $uangHarianTotal = $this->uang_harian * $hari;
        $penginapanTotal = $this->penginapan * $malam;

        return (int) ($uangHarianTotal + $penginapanTotal + $this->represen
            + $this->tiket + $this->transportasi + $this->sewa_kendaraan);
    }
}