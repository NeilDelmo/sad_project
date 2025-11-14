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
        'seller_id',
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
