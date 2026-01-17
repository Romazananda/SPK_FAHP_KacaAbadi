<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subkriteria', function (Blueprint $table) {
            $table->string('jenis_saran')->nullable()->after('nilai');
            $table->float('min_ketebalan_saran')->nullable()->after('jenis_saran');
            $table->float('max_ketebalan_saran')->nullable()->after('min_ketebalan_saran');
        });
    }

    public function down(): void
    {
        Schema::table('subkriteria', function (Blueprint $table) {
            $table->dropColumn(['jenis_saran', 'min_ketebalan_saran', 'max_ketebalan_saran']);
        });
    }
};
