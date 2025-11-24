<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfraction extends Model
{
    protected $fillable = [
        'user_id',
        'reporter_id',
        'verified_by',
        'reason',
        'details',
        'severity',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
