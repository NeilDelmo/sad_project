<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorOffer extends Model implements AuditableConract
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'vendor_id',
        'fisherman_id',
        'product_id',
        'offered_price',
        'quantity',
        'status',
        'fisherman_counter_price',
        'vendor_message',
        'fisherman_message',
        'expires_at',
        'responded_at',
        'suggested_price_fisherman',
        'ml_confidence_fisherman',
    ];

    protected function casts(): array
    {
        return [
            'offered_price' => 'decimal:2',
            'fisherman_counter_price' => 'decimal:2',
            'suggested_price_fisherman' => 'decimal:2',
            'ml_confidence_fisherman' => 'decimal:4',
            'quantity' => 'integer',
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function fisherman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fisherman_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'countered'])
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canRespond(): bool
    {
        return in_array($this->status, ['pending', 'countered']) && !$this->isExpired();
    }

    /**
     * Check if this offer can be fulfilled with available stock
     */
    public function canBeFulfilled(): bool
    {
        if (!$this->product) {
            return false;
        }
        return $this->product->available_quantity >= $this->quantity;
    }

    /**
     * Get the bid rank for this offer (1 = highest bidder)
     * If offers are equal, earliest bid gets better rank (first-come-first-served)
     */
    public function getBidRank(): int
    {
        // Count offers with higher price
        $higherOffers = VendorOffer::where('product_id', $this->product_id)
            ->where('status', 'pending')
            ->where('offered_price', '>', $this->offered_price)
            ->count();
        
        // Count offers with same price but earlier timestamp
        $equalEarlierOffers = VendorOffer::where('product_id', $this->product_id)
            ->where('status', 'pending')
            ->where('offered_price', '=', $this->offered_price)
            ->where('created_at', '<', $this->created_at)
            ->count();
        
        return $higherOffers + $equalEarlierOffers + 1;
    }

    /**
     * Check if vendor can modify this bid
     */
    public function canModify(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Check if vendor can withdraw this bid
     */
    public function canWithdraw(): bool
    {
        return in_array($this->status, ['pending']) && !$this->isExpired();
    }

    /**
     * Auto-reject offers when stock is insufficient
     */
    public static function autoRejectInsufficientStock(int $productId): int
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        $rejected = VendorOffer::where('product_id', $productId)
            ->where('status', 'pending')
            ->where('quantity', '>', $product->available_quantity)
            ->update([
                'status' => 'auto_rejected',
                'fisherman_message' => 'Insufficient stock remaining. Only ' . $product->available_quantity . 'kg available.',
                'responded_at' => now(),
            ]);

        return $rejected;
    }
}
