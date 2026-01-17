<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferensi_kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('kriteria'); // misal: tujuan penggunaan, lokasi penempatan
            $table->string('tujuan')->nullable(); // contoh: jendela, pintu, partisi
            $table->string('lokasi')->nullable(); // contoh: rumah tinggal, kantor
            $table->string('jenis_kaca')->nullable(); // bening, reflektif, dsb
            $table->string('finishing')->nullable(); // polos, tempered, laminated
            $table->float('ketebalan_min')->nullable();
            $table->float('ketebalan_maks')->nullable();
            $table->integer('nilai_kecocokan')->default(5); // skala 1–9
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferensi_kriteria');
    }
};
