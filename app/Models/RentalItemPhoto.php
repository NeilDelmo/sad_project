<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalItemPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_item_id',
        'path',
    ];

    public function rentalItem()
    {
        return $this->belongsTo(RentalItem::class);
    }
}
