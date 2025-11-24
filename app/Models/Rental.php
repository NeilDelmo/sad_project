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
        'discount_amount',
        'deposit_amount',
        'deposit_paid',
        'damage_fee',
        'lost_fee',
        'total_charges',
        'amount_due',
        'payment_status',
        'deposit_paid_at',
        'fully_settled_at',
        'late_fee',
        'notes',
        'admin_notes',
        'approved_by',
        'approved_at',
        'picked_up_at',
        'returned_at',
        'expires_at',
        'pickup_otp',
        'otp_generated_at',
        'otp_verified_at',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'approved_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'returned_at' => 'datetime',
        'expires_at' => 'datetime',
        'otp_generated_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'deposit_paid_at' => 'datetime',
        'fully_settled_at' => 'datetime',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
        'damage_fee' => 'decimal:2',
        'lost_fee' => 'decimal:2',
        'total_charges' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'late_fee' => 'decimal:2',
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

    public function issueReports(): HasMany
    {
        return $this->hasMany(\App\Models\RentalIssueReport::class);
    }
}
