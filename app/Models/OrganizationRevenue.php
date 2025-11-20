<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OrganizationRevenue extends Model implements AuditableContract
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'order_id',
        'listing_id',
        'vendor_id',
        'buyer_id',
        'amount',
        'type',
        'collected_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_at' => 'datetime',
    ];

    // Revenue ledger should be append-only; exclude timestamps and immutable identifiers
    protected array $auditExclude = [
        'created_at',
        'updated_at',
    ];

    public function order() { return $this->belongsTo(CustomerOrder::class, 'order_id'); }
    public function listing() { return $this->belongsTo(MarketplaceListing::class, 'listing_id'); }
    public function vendor() { return $this->belongsTo(User::class, 'vendor_id'); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
}
