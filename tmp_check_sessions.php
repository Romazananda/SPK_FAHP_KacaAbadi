<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo 'has sessions table? ' . (Schema::hasTable('sessions') ? 'yes' : 'no') . "\n";
if (Schema::hasTable('sessions')) {
    $count = DB::table('sessions')->count();
    echo "sessions rows: $count\n";
}
