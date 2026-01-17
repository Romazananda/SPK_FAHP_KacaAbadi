<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alternatif;

class UpdateAlternatifSeeder extends Seeder
{
    public function run(): void
    {
        $alternatifs = Alternatif::all();

        foreach ($alternatifs as $alt) {
            $jenis = strtolower($alt->jenis);
            $ketebalan = (float) $alt->ketebalan;

            if (str_contains($jenis, 'bening')) {
                // kaca bening
                if ($ketebalan <= 6) {
                    $alt->tujuan_penggunaan = 'Jendela';
                    $alt->lokasi_penempatan = 'Rumah Tinggal';
                } else {
                    $alt->tujuan_penggunaan = 'Pintu';
                    $alt->lokasi_penempatan = 'Toko / Komersial';
                }
                $alt->pemotongan = 'Tanpa Pemotongan';
            } elseif (str_contains($jenis, 'tempered')) {
                // kaca tempered
                if ($ketebalan >= 8) {
                    $alt->tujuan_penggunaan = 'Tangga';
                    $alt->lokasi_penempatan = 'Gedung Bertingkat';
                } else {
                    $alt->tujuan_penggunaan = 'Jendela';
                    $alt->lokasi_penempatan = 'Toko / Komersial';
                }
                $alt->pemotongan = 'Pemotongan Sederhana (garis lurus)';
            } elseif (str_contains($jenis, 'riben')) {
                // kaca riben darkgred
                $alt->tujuan_penggunaan = $ketebalan <= 8 ? 'Lemari' : 'Cermin Dinding';
                $alt->lokasi_penempatan = 'Toko / Komersial';
                $alt->pemotongan = 'Pemotongan Lengkung / Khusus';
            } elseif (str_contains($jenis, 'cermin')) {
                // cermin biasa
                $alt->tujuan_penggunaan = 'Cermin Dinding';
                $alt->lokasi_penempatan = 'Rumah Tinggal';
                $alt->pemotongan = 'Tanpa Pemotongan';
            } else {
                // default
                $alt->tujuan_penggunaan = 'Etalase';
                $alt->lokasi_penempatan = 'Area Publik (Mall / Hotel)';
                $alt->pemotongan = 'Pemotongan Custom Kompleks (desain)';
            }

            $alt->save();
        }
    }
}
