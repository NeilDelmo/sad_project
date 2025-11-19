<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * FishermanPricingService
 * 
 * Provides fair market price predictions for fishermen to compare against vendor offers.
 * Uses the same ML model as vendor pricing but configured for fisherman's perspective.
 */
class FishermanPricingService
{
    /**
     * Calculate fair market price for a product from fisherman's perspective.
     * This helps fishermen understand if a vendor's offer is fair.
     * 
     * @param Product $product The product being sold
     * @param float $baseCost Fisherman's cost basis (optional, for profit calculation)
     * @return array ['suggested_price', 'confidence', 'comparison_data']
     */
    public function calculateFairPrice(Product $product, float $baseCost = null): array
    {
        try {
            // Get fresh product data
            $product->load('category');
            
            // Extract features for ML model
            $features = $this->extractProductFeatures($product);
            
            // Call Python ML model
            $prediction = $this->callPricingModel($features);
            
            if (!$prediction || !isset($prediction['multiplier'])) {
                return $this->getFallbackPricing($product, $baseCost);
            }
            
            // Calculate suggested fair price
            // Use product unit_price as base (fisherman's asking price)
            $basePrice = $product->unit_price ?? 100.0;
            $multiplier = $prediction['multiplier'];
            
            // For fisherman's perspective, apply more conservative multiplier
            // to ensure they get fair value (range: 0.9 - 1.3)
            $fairMultiplier = $this->adjustMultiplierForFisherman($multiplier);
            $suggestedPrice = $basePrice * $fairMultiplier;
            
            // Calculate confidence score
            $confidence = $prediction['confidence'] ?? 0.85;
            
            // Build comparison data
            $comparisonData = [
                'base_asking_price' => $basePrice,
                'fair_multiplier' => $fairMultiplier,
                'original_ml_multiplier' => $multiplier,
                'suggested_price' => round($suggestedPrice, 2),
                'confidence' => round($confidence, 4),
                'features' => $features,
                'price_range' => [
                    'low' => round($suggestedPrice * 0.9, 2),
                    'fair' => round($suggestedPrice, 2),
                    'high' => round($suggestedPrice * 1.1, 2),
                ],
            ];
            
            // Add profit calculation if base cost provided
            if ($baseCost !== null) {
                $comparisonData['profit_at_suggested'] = round($suggestedPrice - $baseCost, 2);
                $comparisonData['profit_margin'] = round((($suggestedPrice - $baseCost) / $baseCost) * 100, 2);
            }
            
            return $comparisonData;
            
        } catch (\Exception $e) {
            Log::error('FishermanPricingService: Failed to calculate fair price', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
            
            return $this->getFallbackPricing($product, $baseCost);
        }
    }
    
    /**
     * Compare vendor offer against fair market price
     * 
     * @param float $vendorOffer The price vendor is offering
     * @param array $fairPricing Fair pricing data from calculateFairPrice()
     * @return array Comparison analysis
     */
    public function compareOfferToFairPrice(float $vendorOffer, array $fairPricing): array
    {
        $suggestedPrice = $fairPricing['suggested_price'] ?? 0;
        $priceRange = $fairPricing['price_range'] ?? ['low' => 0, 'fair' => 0, 'high' => 0];
        
        $difference = $vendorOffer - $suggestedPrice;
        $percentageDiff = $suggestedPrice > 0 
            ? round(($difference / $suggestedPrice) * 100, 2) 
            : 0;
        
        // Determine offer quality
        $quality = 'fair';
        $warning = null;
        
        if ($vendorOffer < $priceRange['low']) {
            $quality = 'low';
            $warning = 'This offer is significantly below fair market price';
        } elseif ($vendorOffer < $suggestedPrice * 0.95) {
            $quality = 'below_fair';
            $warning = 'This offer is below the fair market price';
        } elseif ($vendorOffer > $priceRange['high']) {
            $quality = 'excellent';
            $warning = null;
        } elseif ($vendorOffer > $suggestedPrice * 1.05) {
            $quality = 'above_fair';
            $warning = null;
        }
        
        return [
            'vendor_offer' => $vendorOffer,
            'suggested_fair_price' => $suggestedPrice,
            'difference_amount' => round($difference, 2),
            'difference_percentage' => $percentageDiff,
            'offer_quality' => $quality,
            'warning_message' => $warning,
            'recommendation' => $this->getRecommendation($quality, $percentageDiff),
            'is_good_deal' => $vendorOffer >= $suggestedPrice * 0.95,
        ];
    }
    
    /**
     * Extract product features for ML model
     */
    private function extractProductFeatures(Product $product): array
    {
        $now = Carbon::now();
        
        // Freshness score (simplified - you can enhance this)
        $freshnessScore = 85; // Default
        if ($product->freshness_metric) {
            $freshnessScore = match(strtolower($product->freshness_metric)) {
                'very fresh', 'excellent' => 95,
                'fresh', 'good' => 85,
                'moderate' => 70,
                'low' => 50,
                default => 75,
            };
        }
        
        // Demand factor (based on category popularity - simplified)
        $demandFactor = 1.2;
        if ($product->category) {
            $categoryName = strtolower($product->category->name ?? '');
            if (str_contains($categoryName, 'tuna') || str_contains($categoryName, 'salmon')) {
                $demandFactor = 1.5;
            } elseif (str_contains($categoryName, 'shellfish') || str_contains($categoryName, 'shrimp')) {
                $demandFactor = 1.3;
            }
        }
        
        // Seasonality (simplified)
        $month = $now->month;
        $seasonalityFactor = in_array($month, [3, 4, 5, 11, 12]) ? 1.3 : 1.1;
        
        // Category ID encoding
        $categoryId = $product->category_id ?? 1;
        
        return [
            'freshness_score' => $freshnessScore,
            'available_quantity' => $product->available_quantity ?? 50,
            'demand_factor' => $demandFactor,
            'seasonality_factor' => $seasonalityFactor,
            'time_of_day' => $now->hour,
            'vendor_rating' => 4.0, // Neutral default for fisherman perspective
            'category_id' => $categoryId,
        ];
    }
    
    /**
     * Call Python ML pricing model
     */
    private function callPricingModel(array $features): ?array
    {
        $pythonPath = config('services.python_path', 'python3');
        $scriptPath = base_path('python/predict_price.py');
        
        if (!file_exists($scriptPath)) {
            Log::warning('FishermanPricingService: predict_price.py not found');
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
            Log::warning('FishermanPricingService: Invalid ML response', [
                'output' => $output,
                'error' => json_last_error_msg(),
            ]);
            return null;
        }
        
        return $result;
    }
    
    /**
     * Adjust multiplier for fisherman's perspective
     * Ensures fishermen get fair value by being more conservative
     */
    private function adjustMultiplierForFisherman(float $vendorMultiplier): float
    {
        // Fisherman should get fair price, not inflated buyer prices
        // If vendor multiplier is high (for selling to buyers), 
        // fisherman's fair price should be moderate
        
        if ($vendorMultiplier > 1.5) {
            // High retail markup - fisherman gets moderate wholesale
            return min(1.3, $vendorMultiplier * 0.75);
        } elseif ($vendorMultiplier < 1.0) {
            // Market is soft - protect fisherman from too-low prices
            return max(0.9, $vendorMultiplier * 1.1);
        }
        
        // Normal range - apply slight adjustment
        return max(0.9, min(1.3, $vendorMultiplier * 0.85));
    }
    
    /**
     * Fallback pricing when ML model fails
     */
    private function getFallbackPricing(Product $product, ?float $baseCost): array
    {
        $basePrice = $product->unit_price ?? 100.0;
        $suggestedPrice = $basePrice * 1.05; // Conservative 5% markup
        
        $data = [
            'base_asking_price' => $basePrice,
            'fair_multiplier' => 1.05,
            'suggested_price' => round($suggestedPrice, 2),
            'confidence' => 0.5,
            'fallback' => true,
            'price_range' => [
                'low' => round($suggestedPrice * 0.9, 2),
                'fair' => round($suggestedPrice, 2),
                'high' => round($suggestedPrice * 1.1, 2),
            ],
        ];
        
        if ($baseCost !== null) {
            $data['profit_at_suggested'] = round($suggestedPrice - $baseCost, 2);
            $data['profit_margin'] = round((($suggestedPrice - $baseCost) / $baseCost) * 100, 2);
        }
        
        return $data;
    }
    
    /**
     * Get recommendation text based on offer quality
     */
    private function getRecommendation(string $quality, float $percentageDiff): string
    {
        return match($quality) {
            'low' => "⚠️ This offer is significantly below market value. Consider negotiating or rejecting.",
            'below_fair' => "💭 This offer is slightly below fair price. You might want to counter-offer.",
            'fair' => "✓ This offer is close to fair market value.",
            'above_fair' => "✓ This is a good offer, above fair market price.",
            'excellent' => "🎉 This is an excellent offer! Well above market price.",
            default => "Review the offer carefully before deciding.",
        };
    }
}
