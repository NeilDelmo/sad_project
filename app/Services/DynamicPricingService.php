<?php

namespace App\Services;

use App\Models\MarketplaceListing;

class DynamicPricingService
{
    public function __construct(private PricingService $pricingService)
    {
    }

    public function applyToListing(MarketplaceListing $listing, ?float $platformFeeRate = 0.05): MarketplaceListing
    {
        $pricing = $this->pricingService->calculateDynamicPrice(
            $listing->product,
            $listing->seller,
            [
                'listing_id' => $listing->id,
                'log_context' => 'listing_dynamic_adjustment',
                'base_price' => $listing->base_price ?? ($listing->product->unit_price ?? 100),
            ]
        );

        $dynamicPrice = $pricing['final_price'];
        $platformRate = $platformFeeRate ?? config('marketplace.platform_commission_rate', 0.05);
        $platformFee = round($dynamicPrice * $platformRate, 2);
        $finalPrice = round($dynamicPrice + $platformFee, 2);

        $listing->freshness_score = $pricing['features']['freshness_score'] ?? $listing->freshness_score;
        $listing->ml_multiplier = $pricing['effective_multiplier'] ?? $pricing['market_multiplier'] ?? 1.0;
        $listing->ml_confidence = $pricing['confidence'] ?? 0.0;
        $listing->suggested_price = $dynamicPrice;
        $listing->dynamic_price = $dynamicPrice;
        $listing->platform_fee = $platformFee;
        $listing->final_price = $finalPrice;
        $listing->demand_factor = $pricing['signals']['demand']['score'] ?? $listing->demand_factor;

        return $listing;
    }
}
