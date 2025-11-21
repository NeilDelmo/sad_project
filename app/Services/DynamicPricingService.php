<?php

namespace App\Services;

use App\Models\MarketplaceListing;
use Symfony\Component\Process\Process;

class DynamicPricingService
{
    /**
     * Compute and apply dynamic pricing for a listing using the Python model.
     * Populates ml_multiplier, ml_confidence, suggested_price, dynamic_price, final_price.
     */
    public function applyToListing(MarketplaceListing $listing, ?float $platformFeeRate = 0.05): MarketplaceListing
    {
        $product = $listing->product;

        // Feature extraction with safe defaults
        $freshnessScore = $this->computeFreshnessScore($product?->freshness_metric, $product?->created_at);
        $availableQty   = (int) ($product?->available_quantity ?? 0);
        $demandFactor   = (float) ($listing->demand_factor ?? 1.0);
        $seasonality    = (float) ($product?->seasonality_factor ?? 1.0);
        $timeOfDay      = now()->format('G'); // 0-23
        $vendorRating   = (float) ($listing->seller?->vendorProfile?->rating ?? 4.0);
        $categoryId     = (int) ($product?->category_id ?? 0);

        // Call Python predictor
        $prediction = $this->predictMultiplier(
            $freshnessScore,
            $availableQty,
            $demandFactor,
            $seasonality,
            (int) $timeOfDay,
            $vendorRating,
            $categoryId
        );

        $multiplier = $prediction['multiplier'] ?? 1.0;
        $confidence = $prediction['confidence'] ?? 0.0;

        $basePrice = (float) ($listing->base_price ?? 0.0);
        $dynamicPrice = round($basePrice * $multiplier, 2);

        // Platform fee and final price calculation (simple model)
        $platformFee = round($dynamicPrice * ($platformFeeRate ?? 0.05), 2);
        $finalPrice  = round($dynamicPrice + $platformFee, 2);

        $listing->freshness_score = (int) $freshnessScore;
        $listing->ml_multiplier   = $multiplier;
        $listing->ml_confidence  = $confidence;
        $listing->suggested_price = $dynamicPrice;
        $listing->dynamic_price   = $dynamicPrice;
        $listing->platform_fee    = $platformFee;
        $listing->final_price     = $finalPrice;

        return $listing;
    }

    /**
     * Derive a numeric freshness score (0-100) from initial label and age.
     * Heuristic: Very Fresh=100, Fresh=90, Good=80, then decay ~2 points/hour
     * adjusted by fish-type multiplier from config/fish.php.
     */
    public function computeFreshnessScore(?string $initial, $createdAt): int
    {
        $base = match ($initial) {
            'Very Fresh' => 100,
            'Fresh'      => 90,
            'Good'       => 80,
            default      => 75,
        };

        $hours = 0;
        if ($createdAt) {
            $hours = $createdAt->diffInHours(now());
        }

        // Default decay 2 points per hour
        $decayPerHour = 2.0;
        $score = max(0, (int) round($base - ($hours * $decayPerHour)));
        return min(100, $score);
    }

    /**
     * Call the Python script to predict multiplier and confidence.
     */
    public function predictMultiplier(
        float $freshnessScore,
        int $availableQuantity,
        float $demandFactor,
        float $seasonalityFactor,
        int $timeOfDay,
        float $vendorRating,
        int $categoryId
    ): array {
        $pythonPath = base_path('python/predict_price.py');

        $args = [
            'python3',
            $pythonPath,
            (string) $freshnessScore,
            (string) $availableQuantity,
            (string) $demandFactor,
            (string) $seasonalityFactor,
            (string) $timeOfDay,
            (string) $vendorRating,
            (string) $categoryId,
        ];

        $process = new Process($args, base_path('python'));
        $process->setTimeout(20);
        $process->run();

        if (!$process->isSuccessful()) {
            // Try fallback to 'python' if python3 not available
            $args[0] = 'python';
            $process = new Process($args, base_path('python'));
            $process->setTimeout(20);
            $process->run();
        }

        if ($process->isSuccessful()) {
            $out = trim($process->getOutput());
            $json = json_decode($out, true);
            if (is_array($json) && isset($json['multiplier'])) {
                return $json;
            }
        }

        // Fallback if Python call failed
        return ['multiplier' => 1.0, 'confidence' => 0.0];
    }
}
