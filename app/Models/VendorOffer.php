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
}
