<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingPredictionLog extends Model
{
    protected $fillable = [
        'context',
        'product_id',
        'offer_id',
        'listing_id',
        'multiplier',
        'confidence',
        'used_fallback',
        'runtime_ms',
        'features',
        'signals',
        'extra',
        'error_message',
    ];

    protected $casts = [
        'features' => 'array',
        'signals' => 'array',
        'extra' => 'array',
        'used_fallback' => 'boolean',
        'multiplier' => 'decimal:3',
        'confidence' => 'decimal:4',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(VendorOffer::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(MarketplaceListing::class);
    }
}
