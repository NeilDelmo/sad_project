<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class TrustTransaction extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'reference_type',
        'reference_id',
        'reason',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
