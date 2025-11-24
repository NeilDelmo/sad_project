<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$website = config('services.simple_analytics.website');
$apiKey = config('services.simple_analytics.api_key');

echo "Fetching Histogram...\n";

try {
    $url = "https://simpleanalytics.com/{$website}.json";
    
    $response = Http::withHeaders([
        'Api-Key' => $apiKey,
    ])->get($url, [
        'version' => 5,
        'fields' => 'histogram',
        'start' => now()->subDays(30)->format('Y-m-d'),
        'end' => now()->format('Y-m-d'),
        'granularity' => 'day'
    ]);

    if ($response->successful()) {
        print_r($response->json());
    } else {
        echo "Error: " . $response->body();
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
