<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_REFUND_REQUESTED = 'refund_requested';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_REFUND_DECLINED = 'refund_declined';

    protected $fillable = [
        'vendor_id',
        'fisherman_id',
        'product_id',
        'offer_id',
        'quantity',
        'unit_price',
        'total',
        'status',
        'proof_photo_path',
        'delivery_notes',
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

    public function vendor(): BelongsTo { return $this->belongsTo(User::class, 'vendor_id'); }
    public function fisherman(): BelongsTo { return $this->belongsTo(User::class, 'fisherman_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function offer(): BelongsTo { return $this->belongsTo(VendorOffer::class, 'offer_id'); }
}
