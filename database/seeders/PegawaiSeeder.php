<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/pegawai.json');

        if (! File::exists($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return;
        }

        $data = json_decode(File::get($path), true);

        $chunked = collect($data)->chunk(200);

        foreach ($chunked as $chunk) {
            $rows = $chunk->map(fn ($p) => [
                'nip'        => $p['nip'],
                'nama'       => $p['nama'],
                'jabatan'    => $p['jabatan'],
                'penempatan' => $p['penempatan'],
                'kategori'   => $p['kategori'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            Pegawai::upsert($rows, ['nip'], ['nama', 'jabatan', 'penempatan', 'kategori', 'updated_at']);
        }

        $this->command->info('Pegawai ter-import: ' . count($data));
    }
}