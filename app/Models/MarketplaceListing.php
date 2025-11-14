<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_inventory_id',
        'seller_id',
        'base_price',
        'ml_multiplier',
        'dynamic_price',
        'platform_fee',
        'vendor_profit',
        'final_price',
        'ml_confidence',
        'asking_price',
        'suggested_price',
        'demand_factor',
        'freshness_score',
        'listing_date',
        'status',
        'freshness_level',
        'unlisted_at',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'ml_multiplier' => 'decimal:2',
            'dynamic_price' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'vendor_profit' => 'decimal:2',
            'final_price' => 'decimal:2',
            'ml_confidence' => 'decimal:4',
            'asking_price' => 'decimal:2',
            'suggested_price' => 'decimal:2',
            'demand_factor' => 'decimal:2',
            'freshness_score' => 'integer',
            'listing_date' => 'datetime',
            'unlisted_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the listing.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the seller (user) that owns the listing.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the vendor inventory item.
     */
    public function vendorInventory(): BelongsTo
    {
        return $this->belongsTo(VendorInventory::class);
    }

    /**
     * Scope for active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('unlisted_at');
    }

    /**
     * Get time on market as human-readable string.
     */
    public function getTimeOnMarketAttribute(): ?string
    {
        if (!$this->listing_date) {
            return null;
        }
        return $this->listing_date->diffForHumans(now(), true);
    }
}
