<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id('id_penilaian');
            
            // Relasi ke tabel alternatif
            $table->unsignedBigInteger('id_alternatif');
            $table->foreign('id_alternatif')
                  ->references('id')
                  ->on('alternatifs')
                  ->onDelete('cascade');
            
            // Relasi ke tabel kriteria
            $table->unsignedBigInteger('id_kriteria');
            $table->foreign('id_kriteria')
                  ->references('id_kriteria')
                  ->on('kriterias')
                  ->onDelete('cascade');

            // Relasi ke tabel subkriteria
            $table->unsignedBigInteger('id_subkriteria')->nullable();
            $table->foreign('id_subkriteria')
                  ->references('id_subkriteria')
                  ->on('subkriteria')
                  ->onDelete('set null');

            // Nilai kecocokan (0–1)
            $table->float('nilai')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
