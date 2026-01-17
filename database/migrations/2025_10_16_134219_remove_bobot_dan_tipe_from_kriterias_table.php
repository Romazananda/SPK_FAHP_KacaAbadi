<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            if (Schema::hasColumn('kriterias', 'bobot')) {
                $table->dropColumn('bobot');
            }
            if (Schema::hasColumn('kriterias', 'tipe')) {
                $table->dropColumn('tipe');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->decimal('bobot', 8, 4)->default(0)->after('nama_kriteria');
            $table->enum('tipe', ['numerik', 'kategorikal'])->after('bobot');
        });
    }
};
