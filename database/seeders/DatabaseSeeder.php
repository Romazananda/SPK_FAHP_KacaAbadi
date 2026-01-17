<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🔹 Contoh user biasa
        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
        ]);

        // 🔹 Jalankan semua seeder yang dibutuhkan sistem SPK
        $this->call([
            AdminUserSeeder::class,           // Seeder akun admin
            KriteriaSeeder::class,            // Seeder kriteria dasar
            AlternatifSeeder::class,          // 🧱 Seeder alternatif produk
            PreferensiGabunganSeeder::class,  // 🎯 Seeder nilai preferensi ideal
            NilaiKecocokanSeeder::class,      // 📊 Nilai Kecockan
        ]);
    }
}
