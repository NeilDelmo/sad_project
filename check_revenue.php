<?php

use App\Models\OrganizationRevenue;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = OrganizationRevenue::count();
echo "OrganizationRevenue count: " . $count . "\n";

if ($count > 0) {
    $first = OrganizationRevenue::first();
    echo "First record: " . json_encode($first) . "\n";
    
    $daily = OrganizationRevenue::selectRaw('DATE(collected_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day', 'desc')
            ->limit(30)
            ->get();
    echo "Daily data: " . json_encode($daily) . "\n";
}
