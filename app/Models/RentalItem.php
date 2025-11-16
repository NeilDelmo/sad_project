<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class RentalItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'rental_id',
        'product_id',
        'quantity',
        'good_count',
        'fair_count',
        'damaged_count',
        'lost_count',
        'price_per_day',
        'subtotal',
        'condition_out',
        'condition_in',
        'condition_in_photo',
        'damage_notes',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function photos()
    {
        return $this->hasMany(RentalItemPhoto::class);
    }

    public function isDamaged(): bool
    {
        return $this->condition_in && $this->condition_in !== $this->condition_out;
    }
}
