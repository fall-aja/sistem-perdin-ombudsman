<?php

namespace App\Services;

class TerbilangService
{
    protected static array $angka = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
        'sepuluh', 'sebelas',
    ];

    /**
     * Konversi angka ke terbilang Bahasa Indonesia, misal 370000 -> "tiga ratus tujuh puluh ribu".
     * Dipakai untuk mengisi cell "Terbilang" pada Kwitansi & DPR (bukan menimpa formula
     * Excel yang sudah ada di sheet Rincian / DPR — lihat protected_formula_cells).
     */
    public static function make(int $angka): string
    {
        $angka = abs($angka);

        if ($angka < 12) {
            return trim(self::$angka[$angka]);
        }

        if ($angka < 20) {
            return trim(self::make($angka - 10) . ' belas');
        }

        if ($angka < 100) {
            return trim(self::make((int) ($angka / 10)) . ' puluh ' . self::make($angka % 10));
        }

        if ($angka < 200) {
            return trim('seratus ' . self::make($angka - 100));
        }

        if ($angka < 1000) {
            return trim(self::make((int) ($angka / 100)) . ' ratus ' . self::make($angka % 100));
        }

        if ($angka < 2000) {
            return trim('seribu ' . self::make($angka - 1000));
        }

        if ($angka < 1000000) {
            return trim(self::make((int) ($angka / 1000)) . ' ribu ' . self::make($angka % 1000));
        }

        if ($angka < 1000000000) {
            return trim(self::make((int) ($angka / 1000000)) . ' juta ' . self::make($angka % 1000000));
        }

        if ($angka < 1000000000000) {
            return trim(self::make((int) ($angka / 1000000000)) . ' milyar ' . self::make($angka % 1000000000));
        }

        return trim(self::make((int) ($angka / 1000000000000)) . ' trilyun ' . self::make($angka % 1000000000000));
    }

    public static function rupiah(int $angka): string
    {
        if ($angka === 0) {
            return 'nol rupiah';
        }

        $prefix = $angka < 0 ? 'minus ' : '';

        return $prefix . preg_replace('/\s+/', ' ', self::make($angka)) . ' rupiah';
    }
}
