<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use App\Models\Penilaian;

class PenilaianSeeder extends Seeder
{
    public function run()
    {
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();

        foreach ($alternatifs as $alt) {
            foreach ($kriterias as $k) {
                // ambil subkriteria acak dari kriteria itu
                $sub = Subkriteria::where('id_kriteria', $k->id_kriteria)->inRandomOrder()->first();

                Penilaian::create([
                    'id_alternatif' => $alt->id,
                    'id_kriteria' => $k->id_kriteria,
                    'id_subkriteria' => $sub->id_subkriteria,
                    'nilai' => $sub->nilai,
                ]);
            }
        }
    }
}
