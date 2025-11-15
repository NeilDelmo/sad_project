<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerOrder extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
    public function vendor(): BelongsTo { return $this->belongsTo(User::class, 'vendor_id'); }
    public function listing(): BelongsTo { return $this->belongsTo(MarketplaceListing::class, 'listing_id'); }
}
