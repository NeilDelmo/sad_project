<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\VendorOffer;
use App\Models\Order;

class Product extends Model implements AuditableConract
{
    use HasFactory , AuditableTrait;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'fish_type',
        'name',
        'description',
        'image_path',
        'freshness_metric',
        'unit_price',
        'available_quantity',
        'status',
        'seasonality_factor',
        'quality_rating',
        'is_rentable',
        'rental_price_per_day',
        'rental_stock',
        'maintenance_count',
        'rental_available',
        'rental_condition',
        'equipment_status',
        'maintenance_notes',
        'total_repair_cost',
        'last_maintenance_date',
        'reserved_stock',
    ];

    protected $casts = [
        'is_rentable' => 'boolean',
        'rental_price_per_day' => 'decimal:2',
        'rental_stock' => 'integer',
        'maintenance_count' => 'integer',
        'rental_available' => 'integer',
        'total_repair_cost' => 'decimal:2',
        'last_maintenance_date' => 'datetime',
        'reserved_stock' => 'integer',
    ];

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('products.status', 'active');
    }

    /**
     * Scope a query to exclude spoiled products based on freshness decay.
     */
    public function scopeNotSpoiled($query)
    {
        return $query->where(function ($q) {
            // Shellfish (0.5 multiplier)
            $q->orWhere(function ($sub) {
                $sub->where('fish_type', 'Shellfish')
                    ->where(function ($s) {
                        $s->where('freshness_metric', 'Very Fresh')->where('created_at', '>=', now()->subHours(12))
                          ->orWhere('freshness_metric', 'Fresh')->where('created_at', '>=', now()->subHours(9))
                          ->orWhere('freshness_metric', 'Good')->where('created_at', '>=', now()->subHours(6));
                    });
            });

            // Oily Fish (0.7 multiplier)
            $q->orWhere(function ($sub) {
                $sub->where('fish_type', 'Oily Fish')
                    ->where(function ($s) {
                        $s->where('freshness_metric', 'Very Fresh')->where('created_at', '>=', now()->subHours(16)) // approx 16.8
                          ->orWhere('freshness_metric', 'Fresh')->where('created_at', '>=', now()->subHours(12)) // approx 12.6
                          ->orWhere('freshness_metric', 'Good')->where('created_at', '>=', now()->subHours(8)); // approx 8.4
                    });
            });

            // White Fish / Default (1.0 multiplier)
            $q->orWhere(function ($sub) {
                $sub->where(function($type) {
                        $type->whereNotIn('fish_type', ['Shellfish', 'Oily Fish'])
                             ->orWhereNull('fish_type');
                    })
                    ->where(function ($s) {
                        $s->where('freshness_metric', 'Very Fresh')->where('created_at', '>=', now()->subHours(24))
                          ->orWhere('freshness_metric', 'Fresh')->where('created_at', '>=', now()->subHours(18))
                          ->orWhere('freshness_metric', 'Good')->where('created_at', '>=', now()->subHours(12));
                    });
            });
        });
    }

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

    public function vendorOffers(): HasMany
    {
        return $this->hasMany(VendorOffer::class, 'product_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_id');
    }

    /**
     * Get active marketplace listing for this product.
     */
    public function activeMarketplaceListing()
    {
        return $this->hasOne(\App\Models\MarketplaceListing::class, 'product_id')
            ->where('marketplace_listings.status', 'active')
            ->latest('listing_date');
    }

    /**
     * Get rental items for this product.
     */
    public function rentalItems()
    {
        return $this->hasMany(\App\Models\RentalItem::class, 'product_id');
    }

    /**
     * Compute freshness level dynamically based on initial assessment and time decay.
     */
    public function computeFreshnessLevel(): ?string
    {
        // Use initial fisherman assessment
        $initialFreshness = $this->freshness_metric ?? 'Good';
        
        // Calculate hours since product creation
        $hoursOld = $this->created_at->diffInHours(now());
        
        // Get decay multiplier based on fish_type or fall back to category
        $typeName = $this->fish_type ?? $this->category->name ?? 'Fish';
        $decayMultiplier = config("fish.category_decay_multipliers.{$typeName}", 1.0);
        
        // Get decay thresholds for this initial freshness
        $decayThresholds = config("fish.freshness_decay_hours.{$initialFreshness}", []);
        
        // Find current freshness level based on decay (adjusted for fish type)
        foreach ($decayThresholds as $level => $baseHours) {
            $adjustedHours = $baseHours * $decayMultiplier;
            if ($hoursOld >= $adjustedHours) {
                return $level;
            }
        }
        
        // Still at initial freshness if no threshold exceeded
        return $initialFreshness;
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

    public function getIsEditLockedAttribute(): bool
    {
        $hasActiveOffers = $this->vendorOffers
            ? $this->vendorOffers->whereIn('status', ['pending', 'countered'])->isNotEmpty()
            : $this->vendorOffers()->whereIn('status', ['pending', 'countered'])->exists();

        $hasOngoingOrders = $this->orders
            ? $this->orders->whereIn('status', [
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_IN_TRANSIT,
                Order::STATUS_DELIVERED,
                Order::STATUS_REFUND_REQUESTED,
            ])->isNotEmpty()
            : $this->orders()->whereIn('status', [
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_IN_TRANSIT,
                Order::STATUS_DELIVERED,
                Order::STATUS_REFUND_REQUESTED,
            ])->exists();

        return $this->available_quantity <= 0 || $hasActiveOffers || $hasOngoingOrders;
    }
}
