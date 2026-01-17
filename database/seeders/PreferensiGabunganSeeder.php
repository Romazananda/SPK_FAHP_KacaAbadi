<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreferensiKriteria;

class PreferensiGabunganSeeder extends Seeder
{
    public function run(): void
{
    $data = [
        // === Tujuan Penggunaan ===
        ['kriteria' => 'Tujuan Penggunaan', 'tujuan' => 'Jendela', 'lokasi' => 'Rumah Tinggal', 'jenis_kaca' => 'Bening', 'finishing' => 'Polos', 'ketebalan_min' => 3, 'ketebalan_maks' => 5, 'nilai_kecocokan' => 9],
        ['kriteria' => 'Tujuan Penggunaan', 'tujuan' => 'Jendela', 'lokasi' => 'Kantor', 'jenis_kaca' => 'Riben Darkgred', 'finishing' => 'Tempered', 'ketebalan_min' => 6, 'ketebalan_maks' => 10, 'nilai_kecocokan' => 8],
        ['kriteria' => 'Tujuan Penggunaan', 'tujuan' => 'Partisi', 'lokasi' => 'Rumah Tinggal', 'jenis_kaca' => 'Bening', 'finishing' => 'Sandblast', 'ketebalan_min' => 3, 'ketebalan_maks' => 6, 'nilai_kecocokan' => 8],
        ['kriteria' => 'Tujuan Penggunaan', 'tujuan' => 'Tangga', 'lokasi' => 'Mall', 'jenis_kaca' => 'Bening', 'finishing' => 'Tempered', 'ketebalan_min' => 8, 'ketebalan_maks' => 12, 'nilai_kecocokan' => 8],

        // === Ukuran ===
        ['kriteria' => 'Ukuran', 'tujuan' => null, 'lokasi' => null, 'jenis_kaca' => 'Bening', 'finishing' => 'Polos', 'ketebalan_min' => 3, 'ketebalan_maks' => 5, 'nilai_kecocokan' => 8],
        ['kriteria' => 'Ukuran', 'tujuan' => null, 'lokasi' => null, 'jenis_kaca' => 'Riben', 'finishing' => 'Tempered', 'ketebalan_min' => 6, 'ketebalan_maks' => 10, 'nilai_kecocokan' => 7],

        // === Lokasi Penempatan ===
        ['kriteria' => 'Lokasi Penempatan', 'tujuan' => null, 'lokasi' => 'Rumah Tinggal', 'jenis_kaca' => 'Bening', 'finishing' => 'Polos', 'ketebalan_min' => 3, 'ketebalan_maks' => 5, 'nilai_kecocokan' => 9],
        ['kriteria' => 'Lokasi Penempatan', 'tujuan' => null, 'lokasi' => 'Kantor', 'jenis_kaca' => 'Riben', 'finishing' => 'Tempered', 'ketebalan_min' => 6, 'ketebalan_maks' => 8, 'nilai_kecocokan' => 8],

        // === Jumlah Unit ===
        ['kriteria' => 'Jumlah Unit', 'tujuan' => null, 'lokasi' => null, 'jenis_kaca' => null, 'finishing' => null, 'ketebalan_min' => null, 'ketebalan_maks' => null, 'nilai_kecocokan' => 7],

        // === Pemotongan ===
        ['kriteria' => 'Pemotongan', 'tujuan' => null, 'lokasi' => null, 'jenis_kaca' => null, 'finishing' => null, 'ketebalan_min' => null, 'ketebalan_maks' => null, 'nilai_kecocokan' => 8],

        // === Pengiriman ===
        ['kriteria' => 'Pengiriman', 'tujuan' => null, 'lokasi' => null, 'jenis_kaca' => null, 'finishing' => null, 'ketebalan_min' => null, 'ketebalan_maks' => null, 'nilai_kecocokan' => 9],
    ];

    foreach ($data as $item) {
        \App\Models\PreferensiKriteria::create($item);
    }
}
}
