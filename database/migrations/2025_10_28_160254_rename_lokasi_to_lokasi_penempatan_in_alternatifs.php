<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alternatifs', function (Blueprint $table) {
            // ubah nama kolom 'lokasi' menjadi 'lokasi_penempatan'
            $table->renameColumn('lokasi', 'lokasi_penempatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternatifs', function (Blueprint $table) {
            // kembalikan kalau di-rollback
            $table->renameColumn('lokasi_penempatan', 'lokasi');
        });
    }
};
