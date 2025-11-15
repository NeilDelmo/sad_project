<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorOffer extends Model
{
    use HasFactory;

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
    ];

    protected function casts(): array
    {
        return [
            'offered_price' => 'decimal:2',
            'fisherman_counter_price' => 'decimal:2',
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
