<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class CustomerOrder extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected array $auditExclude = [
        'proof_photo_path',
        'refund_proof_path',
        'updated_at',
    ];

    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_REFUND_REQUESTED = 'refund_requested';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_REFUND_DECLINED = 'refund_declined';

    protected $fillable = [
        'buyer_id',
        'vendor_id',
        'listing_id',
        'quantity',
        'unit_price',
        'total',
        'status',
        'proof_photo_path',
        'delivered_at',
        'received_at',
        'refund_reason',
        'refund_notes',
        'refund_at',
        'refund_proof_path',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'received_at' => 'datetime',
        'refund_at' => 'datetime',
    ];

    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
    public function vendor(): BelongsTo { return $this->belongsTo(User::class, 'vendor_id'); }
    public function listing(): BelongsTo { return $this->belongsTo(MarketplaceListing::class, 'listing_id'); }
    
    /**
     * Get formatted order number (e.g., AB123)
     */
    public function getFormattedOrderNumberAttribute(): string
    {
        $letters = chr(65 + ($this->id % 26)) . chr(65 + (intval($this->id / 26) % 26));
        $numbers = str_pad($this->id % 1000, 3, '0', STR_PAD_LEFT);
        return $letters . $numbers;
    }
    
    /**
     * Check if refund request window is still open (3 hours from delivery)
     */
    public function isRefundWindowOpen(): bool
    {
        if (!$this->delivered_at) {
            return false;
        }
        
        $hoursSinceDelivery = now()->diffInHours($this->delivered_at);
        return $hoursSinceDelivery < 3;
    }
    
    /**
     * Get minutes remaining in refund window
     */
    public function refundWindowMinutesRemaining(): ?int
    {
        if (!$this->delivered_at || !$this->isRefundWindowOpen()) {
            return null;
        }
        
        $minutesSinceDelivery = now()->diffInMinutes($this->delivered_at);
        $remaining = (3 * 60) - $minutesSinceDelivery;
        return max(0, $remaining);
    }
}
