<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\User;

$fisherman = User::where('user_type', 'fisherman')->first();

if (!$fisherman) {
    echo "No fisherman found.\n";
    exit;
}

echo "Fisherman ID: " . $fisherman->id . "\n";

$products = Product::where('supplier_id', $fisherman->id)->get();

echo "Total Products: " . $products->count() . "\n";

foreach ($products as $product) {
    echo "ID: {$product->id} | Name: {$product->name} | Created: {$product->created_at} | Status: {$product->status} | Freshness: {$product->freshness_metric} | Calc Freshness: {$product->freshness_level}\n";
}

echo "\nChecking scopeNotSpoiled for Vendor View:\n";
$visibleToVendor = Product::active()->notSpoiled()->count();
echo "Visible to Vendor: $visibleToVendor\n";
