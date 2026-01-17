<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlternatifSeeder extends Seeder
{
    public function run(): void
    {
        $tujuan = ['Jendela', 'Pintu', 'Partisi', 'Etalase', 'Fasad', 'Dekorasi', 'Tangga'];
        $lokasi = ['Rumah Tinggal', 'Kantor', 'Toko', 'Mall', 'Gedung Bertingkat'];
        $jenis = ['Bening', 'Riben', 'Darkgred', 'Es', 'Teh (Persol)'];
        $finishing = ['Polos', 'Tempered', 'Sandblast', 'Printing', 'Bending Tempered'];
        $pemotongan = ['Tanpa Custom', 'Potong Sederhana', 'Potong Khusus (Custom Desain)'];
        $pengiriman = ['Ambil di Toko', 'Pengiriman Dalam Kota', 'Pengiriman Luar Kota'];

        // Kosongkan tabel sebelum seed (opsional)
        DB::table('alternatifs')->truncate();

        for ($i = 1; $i <= 50; $i++) {
            $tujuanUse = $tujuan[array_rand($tujuan)];
            $lokasiUse = $lokasi[array_rand($lokasi)];
            $jenisUse = $jenis[array_rand($jenis)];
            $finishUse = $finishing[array_rand($finishing)];
            $tebal = [3, 5, 6, 8, 10, 12][array_rand([3, 5, 6, 8, 10, 12])];
            $harga = match ($finishUse) {
                'Polos' => 70000,
                'Tempered' => 280000,
                'Sandblast' => 140000,
                'Printing' => 210000,
                'Bending Tempered' => 420000,
                default => 100000,
            };
            $ukuranList = [
                '91.5x198', '122x152.5', '122x183', '132x152.5', '101.5x203',
                '244x183', '305x213.5', '132x183', '152x203'
            ];
            $ukuran = $ukuranList[array_rand($ukuranList)];

            DB::table('alternatifs')->insert([
                'nama' => "Alternatif {$i}",
                'tujuan_penggunaan' => $tujuanUse,
                'lokasi_penempatan' => $lokasiUse,
                'jenis' => $jenisUse,
                'ukuran' => $ukuran,
                'jumlah_unit' => rand(1, 20),
                'pemotongan' => $pemotongan[array_rand($pemotongan)],
                'pengiriman' => $pengiriman[array_rand($pengiriman)],
                'ketebalan' => $tebal,
                'finishing' => $finishUse,
                'harga' => $harga,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
