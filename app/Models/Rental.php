<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Rental extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'status',
        'rental_date',
        'return_date',
        'actual_return_date',
        'total_price',
        'deposit_amount',
        'notes',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'approved_at' => 'datetime',
        'total_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isOverdue(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        return now()->isAfter($this->return_date);
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->rental_date->diffInDays($this->return_date) + 1;
    }
}
