<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria_bobots', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('kriteria_id');
    $table->decimal('l', 8, 4);
    $table->decimal('m', 8, 4);
    $table->decimal('u', 8, 4);
    $table->decimal('defuzzifikasi', 8, 4)->nullable();
    $table->decimal('prioritas', 8, 4)->nullable();
    $table->timestamps();

    $table->foreign('kriteria_id')
          ->references('id_kriteria')
          ->on('kriterias')
          ->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria_bobots');
    }
};
