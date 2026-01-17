<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kriterias')->insert([
    ['nama_kriteria' => 'Ukuran', 'tipe' => 'numerik', 'bobot' => 0.2, 'created_at' => now(), 'updated_at' => now()],
    ['nama_kriteria' => 'Tujuan', 'tipe' => 'kategorikal', 'bobot' => 0.15, 'created_at' => now(), 'updated_at' => now()],
    ['nama_kriteria' => 'Lokasi', 'tipe' => 'kategorikal', 'bobot' => 0.1, 'created_at' => now(), 'updated_at' => now()],
    ['nama_kriteria' => 'Jumlah Unit', 'tipe' => 'numerik', 'bobot' => 0.25, 'created_at' => now(), 'updated_at' => now()],
    ['nama_kriteria' => 'Pemotongan', 'tipe' => 'kategorikal', 'bobot' => 0.15, 'created_at' => now(), 'updated_at' => now()],
    ['nama_kriteria' => 'Pengiriman', 'tipe' => 'kategorikal', 'bobot' => 0.15, 'created_at' => now(), 'updated_at' => now()],
]);

    }
}
