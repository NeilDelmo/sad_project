<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories', // json array of product_category ids
        'min_quantity',
        'max_unit_price',
        'notify_channels', // json array: ["in_app", "email"]
        'notify_on', // all | matching
        'onboarding_completed_at',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'notify_channels' => 'array',
        'onboarding_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
