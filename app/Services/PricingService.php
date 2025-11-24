<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingService
{
    public function __construct(
        private MarketSignalService $marketSignals,
        private PricingPredictionLogger $predictionLogger,
    ) {
    }

    public function calculateDynamicPrice(Product $product, ?User $vendor = null, array $options = []): array
    {
        $product->loadMissing('category');
        $signals = $this->marketSignals->forProduct($product, $options);
        $features = $this->extractPricingFeatures($product, $vendor, $signals);
        $basePrice = $options['base_price'] ?? $this->deriveBasePrice($product, $signals);
        $portfolioFactor = $vendor ? $this->calculateVendorPortfolioFactor($vendor) : 1.0;

        $startedAt = microtime(true);
        $prediction = $this->callPricingModel($features);
        $runtimeMs = (int) ((microtime(true) - $startedAt) * 1000);

        if (!$prediction || !isset($prediction['multiplier'])) {
            $result = $this->buildFallbackPricing($basePrice, $portfolioFactor, $signals, $features, $runtimeMs);
            if (!($options['skip_logging'] ?? false)) {
                $this->logPredictionResult($product, $result, $options);
            }
            return $result;
        }

        $modelMultiplier = (float) $prediction['multiplier'];
        $confidence = $prediction['confidence'] ?? 0.85;
        $marketMultiplier = $this->adjustMultiplierForMarket($modelMultiplier, $signals);
        $effectiveMultiplier = $marketMultiplier * $portfolioFactor;
        $marketPrice = round($basePrice * $marketMultiplier, 2);
        $finalPrice = round($basePrice * $effectiveMultiplier, 2);

        $result = [
            'base_price' => round($basePrice, 2),
            'model_multiplier' => round($modelMultiplier, 3),
            'market_multiplier' => round($marketMultiplier, 3),
            'effective_multiplier' => round($effectiveMultiplier, 3),
            'portfolio_factor' => round($portfolioFactor, 3),
            'market_price' => $marketPrice,
            'final_price' => $finalPrice,
            'confidence' => round($confidence, 4),
            'signals' => $signals,
            'features' => $features,
            'price_range' => [
                'low' => round($finalPrice * 0.96, 2),
                'fair' => $finalPrice,
                'high' => round($finalPrice * 1.08, 2),
            ],
            'fallback' => false,
            'runtime_ms' => $runtimeMs,
        ];

        $result['log_payload'] = [
            'features' => $features,
            'signals' => $signals,
            'multiplier' => $result['effective_multiplier'],
            'confidence' => $result['confidence'],
            'used_fallback' => false,
            'runtime_ms' => $runtimeMs,
            'extra' => [
                'base_price' => $basePrice,
                'market_price' => $marketPrice,
                'portfolio_factor' => $portfolioFactor,
            ],
        ];

        if (!($options['skip_logging'] ?? false)) {
            $this->logPredictionResult($product, $result, $options);
        }

        return $result;
    }

    private function extractPricingFeatures(Product $product, ?User $vendor = null, array $signals = []): array
    {
        $now = Carbon::now();
        $createdAt = $product->created_at ? Carbon::parse($product->created_at) : $now;
        $hoursOld = $createdAt->diffInHours($now);
        $freshnessScore = max(0, 100 - ($hoursOld * 2));

        $demandFactor = round((float) ($signals['demand']['score'] ?? 1.0), 3);
        $seasonality = in_array($now->month, [3, 4, 5, 11, 12], true) ? 1.2 : 1.0;
        $availableQuantity = max(1, (int) ($product->available_quantity ?? $product->quantity ?? 10));
        $vendorRating = $vendor ? $this->getVendorRating($vendor) : 4.0;

        $featureSet = [
            'freshness_score' => $freshnessScore,
            'available_quantity' => $availableQuantity,
            'demand_factor' => $demandFactor,
            'seasonality_factor' => $seasonality,
            'time_of_day' => $now->hour,
            'vendor_rating' => $vendorRating,
            'category_id' => (int) ($product->category_id ?? 1),
        ];

        if ($vendor) {
            $featureSet['vendor_total_items'] = $this->getVendorTotalInventoryItems($vendor);
            $featureSet['vendor_total_quantity'] = $this->getVendorTotalInventoryQuantity($vendor);
        }

        return $featureSet;
    }

    private function getVendorRating(User $vendor): float
    {
        return 4.0;
    }

    private function calculateVendorPortfolioFactor(User $vendor): float
    {
        $totals = DB::table('vendor_inventory')
            ->selectRaw('COUNT(*) as items, COALESCE(SUM(quantity),0) as qty')
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['in_stock', 'listed'])
            ->first();

        $items = (int) ($totals->items ?? 0);
        $qty = (int) ($totals->qty ?? 0);

        $itemAdj = 0.0;
        if ($items >= 50) $itemAdj = -0.03;
        elseif ($items >= 20) $itemAdj = -0.015;
        elseif ($items <= 3) $itemAdj = 0.02;

        $qtyAdj = 0.0;
        if ($qty >= 1000) $qtyAdj = -0.02;
        elseif ($qty >= 300) $qtyAdj = -0.01;
        elseif ($qty <= 20) $qtyAdj = 0.01;

        $factor = 1.0 + $itemAdj + $qtyAdj;
        return max(0.97, min(1.03, round($factor, 4)));
    }

    private function getVendorTotalInventoryItems(User $vendor): int
    {
        return (int) DB::table('vendor_inventory')
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['in_stock', 'listed'])
            ->count();
    }

    private function getVendorTotalInventoryQuantity(User $vendor): int
    {
        return (int) (DB::table('vendor_inventory')
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['in_stock', 'listed'])
            ->sum('quantity') ?? 0);
    }

    private function callPricingModel(array $features): ?array
    {
        $pythonPath = config('services.python_path', 'python3');
        $scriptPath = base_path('python/predict_price.py');

        if (!file_exists($scriptPath)) {
            Log::warning('PricingService: predict_price.py not found');
            return null;
        }

        $command = sprintf(
            '%s %s %s %s %s %s %s %s %s 2>&1',
            escapeshellcmd($pythonPath),
            escapeshellarg($scriptPath),
            escapeshellarg($features['freshness_score']),
            escapeshellarg($features['available_quantity']),
            escapeshellarg($features['demand_factor']),
            escapeshellarg($features['seasonality_factor']),
            escapeshellarg($features['time_of_day']),
            escapeshellarg($features['vendor_rating']),
            escapeshellarg($features['category_id'])
        );

        $output = shell_exec($command);

        if (!$output) {
            return null;
        }

        $result = json_decode(trim($output), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($result['multiplier'])) {
            Log::warning('PricingService: Invalid ML response', [
                'output' => $output,
                'error' => json_last_error_msg(),
            ]);
            return null;
        }

        return $result;
    }

    private function adjustMultiplierForMarket(float $modelMultiplier, array $signals): float
    {
        $multiplier = max(0.75, min(1.6, $modelMultiplier));
        $demand = (float) ($signals['demand']['score'] ?? 1.0);
        $supply = (float) ($signals['supply']['pressure'] ?? 1.0);
        $acceptance = (float) ($signals['wholesale']['acceptance_rate'] ?? 0.5);

        $marketTension = max(0.85, min(1.35, $demand * (0.9 + $acceptance)));
        $inventoryRelief = max(0.75, min(1.25, 2 - $supply));

        $adjusted = $multiplier * $marketTension * $inventoryRelief;

        return max(0.85, min(1.4, round($adjusted, 3)));
    }

    private function buildFallbackPricing(
        float $basePrice,
        float $portfolioFactor,
        array $signals,
        array $features,
        ?int $runtimeMs = null
    ): array {
        $demand = (float) ($signals['demand']['score'] ?? 1.0);
        $supply = (float) ($signals['supply']['pressure'] ?? 1.0);
        $acceptance = (float) ($signals['wholesale']['acceptance_rate'] ?? 0.5);

        $marketMultiplier = max(0.8, min(1.35, $demand * (0.9 + $acceptance) * (2 - $supply)));
        $effectiveMultiplier = $marketMultiplier * $portfolioFactor;
        $finalPrice = round($basePrice * $effectiveMultiplier, 2);

        $result = [
            'base_price' => round($basePrice, 2),
            'market_multiplier' => round($marketMultiplier, 3),
            'effective_multiplier' => round($effectiveMultiplier, 3),
            'portfolio_factor' => round($portfolioFactor, 3),
            'market_price' => round($basePrice * $marketMultiplier, 2),
            'final_price' => $finalPrice,
            'confidence' => 0.55,
            'signals' => $signals,
            'features' => $features,
            'price_range' => [
                'low' => round($finalPrice * 0.95, 2),
                'fair' => $finalPrice,
                'high' => round($finalPrice * 1.05, 2),
            ],
            'fallback' => true,
            'runtime_ms' => $runtimeMs,
        ];

        $result['log_payload'] = [
            'features' => $features,
            'signals' => $signals,
            'multiplier' => $result['effective_multiplier'],
            'confidence' => $result['confidence'],
            'used_fallback' => true,
            'runtime_ms' => $runtimeMs,
            'extra' => [
                'base_price' => $basePrice,
            ],
        ];

        return $result;
    }

    private function deriveBasePrice(Product $product, array $signals): float
    {
        $candidates = array_filter([
            $product->unit_price ?? null,
            $signals['retail']['median'] ?? null,
            $signals['wholesale']['median_price'] ?? null,
        ], fn ($value) => $value !== null && $value > 0);

        if (empty($candidates)) {
            return round((float) ($product->unit_price ?? 100.0), 2);
        }

        return round(max(20.0, array_sum($candidates) / count($candidates)), 2);
    }

    private function logPredictionResult(Product $product, array $result, array $options = []): void
    {
        $payload = $result['log_payload'] ?? null;
        if (!$payload) {
            return;
        }

        try {
            $this->predictionLogger->log([
                'context' => $options['log_context'] ?? 'vendor_pricing',
                'product_id' => $product->id,
                'listing_id' => $options['listing_id'] ?? null,
                'multiplier' => $payload['multiplier'] ?? null,
                'confidence' => $payload['confidence'] ?? null,
                'used_fallback' => $payload['used_fallback'] ?? false,
                'runtime_ms' => $payload['runtime_ms'] ?? ($result['runtime_ms'] ?? null),
                'features' => $payload['features'] ?? null,
                'signals' => $payload['signals'] ?? null,
                'extra' => ($payload['extra'] ?? []) + [
                    'portfolio_factor' => $result['portfolio_factor'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('PricingService: Failed to log prediction', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
