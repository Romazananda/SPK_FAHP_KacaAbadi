<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiKecocokanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID aktual dari tabel yang sudah ada
        $kriterias = DB::table('kriterias')->pluck('id_kriteria');
        $alternatifs = DB::table('alternatifs')->pluck('id');

        foreach ($alternatifs as $alt) {
            foreach ($kriterias as $krit) {
                $nilai = round(mt_rand(30, 100) / 100, 4);

                DB::table('nilai_kecocokan')->insert([
                    'alternatif_id' => $alt,
                    'kriteria_id' => $krit,
                    'nilai' => $nilai,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
