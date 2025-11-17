<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class RentalItemPhoto extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected array $auditExclude = [
        'path',
        'updated_at',
    ];

    protected $fillable = [
        'rental_item_id',
        'path',
    ];

    public function rentalItem()
    {
        return $this->belongsTo(RentalItem::class);
    }
}
