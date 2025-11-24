<?php

namespace App\Services;

use App\Models\PricingPredictionLog;

class PricingPredictionLogger
{
    public function log(array $attributes): PricingPredictionLog
    {
        return PricingPredictionLog::create([
            'context' => $attributes['context'] ?? 'unknown',
            'product_id' => $attributes['product_id'] ?? null,
            'offer_id' => $attributes['offer_id'] ?? null,
            'listing_id' => $attributes['listing_id'] ?? null,
            'multiplier' => $attributes['multiplier'] ?? null,
            'confidence' => $attributes['confidence'] ?? null,
            'used_fallback' => (bool) ($attributes['used_fallback'] ?? false),
            'runtime_ms' => $attributes['runtime_ms'] ?? null,
            'features' => $attributes['features'] ?? null,
            'signals' => $attributes['signals'] ?? null,
            'extra' => $attributes['extra'] ?? null,
            'error_message' => $attributes['error_message'] ?? null,
        ]);
    }
}
