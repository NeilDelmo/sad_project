<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\User;
use App\Models\ProductCategory;

class Product extends Model implements AuditableConract
{
    use HasFactory , AuditableTrait;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'name',
        'description',
        'image_path',
        'freshness_metric',
        'unit_price',
        'available_quantity',
        'seasonality_factor',
        'quality_rating',
    ];

    public function supplier(): BelongsTo{
        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }

    public function category(): BelongsTo{
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    /**
     * Get the marketplace listings for this product.
     */
    public function marketplaceListings()
    {
        return $this->hasMany(\App\Models\MarketplaceListing::class, 'product_id');
    }

    /**
     * Get active marketplace listing for this product.
     */
    public function activeMarketplaceListing()
    {
        return $this->hasOne(\App\Models\MarketplaceListing::class, 'product_id')
            ->where('status', 'active')
            ->latest('listing_date');
    }

    /**
     * Compute freshness level dynamically based on active listing age.
     */
    public function computeFreshnessLevel(): ?string
    {
        $listing = $this->activeMarketplaceListing()->first();
        if (!$listing || !$listing->listing_date) {
            return null;
        }
        $minutes = $listing->listing_date->diffInMinutes();
        $thresholds = config('fish.freshness_threshold_minutes', []);
        foreach ($thresholds as $level => $maxMinutes) {
            if ($minutes <= $maxMinutes) {
                return $level;
            }
        }
        return 'Spoiled';
    }

    public function getFreshnessLevelAttribute(): ?string
    {
        return $this->computeFreshnessLevel();
    }

    public function getTimeOnMarketAttribute(): ?string
    {
        $listing = $this->activeMarketplaceListing()->first();
        if (!$listing || !$listing->listing_date) {
            return null;
        }
        return $listing->listing_date->diffForHumans(now(), true);
    }
}
