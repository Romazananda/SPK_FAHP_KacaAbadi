<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subkriteria', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('nilai');

            $table->unsignedBigInteger('added_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('added_by');

            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subkriteria', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn(['status', 'added_by', 'approved_at']);
        });
    }
};

