<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the framework kernel so we can use app()/DB/Eloquent
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Perdin;

$p = Perdin::with(['travelers','rincianItems','dprItems'])->latest()->first();

if (! $p) {
    echo "NO_PERDIN\n";
    exit(0);
}

$svc = $app->make(App\Services\ExcelGeneratorService::class);

try {
    $out = $svc->generate($p);
    echo "GENERATED:" . $out . PHP_EOL;
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
