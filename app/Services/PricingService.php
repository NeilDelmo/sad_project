<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingService
{
    /**
     * Path to the Python pricing prediction script
     */
    private const PYTHON_SCRIPT = '/home/neil/projects/sad_project/python/predict_price.py';
    
    /**
     * Calculate dynamic price for a product using ML model.
     *
     * @param Product $product The product to price
     * @param User|null $vendor The vendor listing the product (optional)
     * @return array ['base_price', 'multiplier', 'final_price', 'confidence']
     */
    public function calculateDynamicPrice(Product $product, ?User $vendor = null): array
    {
        // Extract features for ML model
        $features = $this->extractPricingFeatures($product, $vendor);
        
        // Call Python ML model
        $mlResult = $this->callPricingModel($features);
        
        // Calculate final price
        $basePrice = $product->unit_price;
        $multiplier = $mlResult['multiplier'] ?? 1.0;
        $finalPrice = round($basePrice * $multiplier, 2);
        
        return [
            'base_price' => $basePrice,
            'multiplier' => $multiplier,
            'final_price' => $finalPrice,
            'confidence' => $mlResult['confidence'] ?? 0.85,
            'features' => $features,
        ];
    }
    
    /**
     * Extract features required by the pricing ML model.
     *
     * @param Product $product
     * @param User|null $vendor
     * @return array
     */
    private function extractPricingFeatures(Product $product, ?User $vendor = null): array
    {
        // Freshness score (0-100, based on created_at timestamp)
        $hoursOld = Carbon::parse($product->created_at)->diffInHours(Carbon::now());
        $freshnessScore = max(0, 100 - ($hoursOld * 2)); // Decays 2 points per hour
        
        // Demand factor (calculate from recent marketplace activity)
        $demandFactor = $this->calculateDemandFactor($product);
        
        // Vendor rating (1-5, default 3.5 if no vendor)
        $vendorRating = $vendor ? $this->getVendorRating($vendor) : 3.5;
        
        // Time of day (0-23)
        $timeOfDay = (int) Carbon::now()->format('H');
        
        return [
            'freshness_score' => round($freshnessScore, 2),
            'available_quantity' => $product->available_quantity ?? 10,
            'demand_factor' => $demandFactor,
            'seasonality_factor' => $product->seasonality_factor ?? 1.0,
            'time_of_day' => $timeOfDay,
            'vendor_rating' => $vendorRating,
            'category_id' => $product->category_id ?? 1,
        ];
    }
    
    /**
     * Calculate demand factor based on recent marketplace activity.
     *
     * @param Product $product
     * @return float Demand factor (0.3 to 2.0)
     */
    private function calculateDemandFactor(Product $product): float
    {
        // Count active listings for this product category in last 24 hours
        $categoryListings = DB::table('marketplace_listings')
            ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
            ->where('products.category_id', $product->category_id)
            ->where('marketplace_listings.status', 'active')
            ->where('marketplace_listings.listing_date', '>=', Carbon::now()->subHours(24))
            ->count();
        
        // Simple demand heuristic: fewer listings = higher demand
        // Normalize to 0.3-2.0 range
        if ($categoryListings == 0) {
            return 1.5; // High demand (no competition)
        } elseif ($categoryListings < 5) {
            return 1.2;
        } elseif ($categoryListings < 15) {
            return 1.0;
        } else {
            return 0.7; // Low demand (high competition)
        }
    }
    
    /**
     * Get vendor rating (placeholder - implement with actual review system).
     *
     * @param User $vendor
     * @return float Rating 1.0-5.0
     */
    private function getVendorRating(User $vendor): float
    {
        // TODO: Implement actual vendor rating from reviews/transactions
        // For now, return a default value
        return 4.0;
    }
    
    /**
     * Call the Python ML model to predict price multiplier.
     *
     * @param array $features
     * @return array ['multiplier', 'confidence']
     */
    private function callPricingModel(array $features): array
    {
        // Prepare command with feature values
        $command = sprintf(
            'python3 %s %s %d %s %s %d %s %d 2>&1',
            escapeshellarg(self::PYTHON_SCRIPT),
            escapeshellarg($features['freshness_score']),
            (int) $features['available_quantity'],
            escapeshellarg($features['demand_factor']),
            escapeshellarg($features['seasonality_factor']),
            (int) $features['time_of_day'],
            escapeshellarg($features['vendor_rating']),
            (int) $features['category_id']
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            Log::warning('Pricing model execution failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode,
            ]);
            
            // Fallback to default multiplier
            return ['multiplier' => 1.0, 'confidence' => 0.0];
        }
        
        $result = json_decode(implode("\n", $output), true);
        
        if (!$result || !isset($result['multiplier'])) {
            Log::warning('Invalid pricing model output', ['output' => $output]);
            return ['multiplier' => 1.0, 'confidence' => 0.0];
        }
        
        return $result;
    }
}
