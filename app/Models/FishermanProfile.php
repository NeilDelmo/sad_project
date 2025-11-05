<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FishermanProfile extends Model implements AuditableConract
{
    use HasFactory, AuditableTrait; 

    protected $fillable = [
        'user_id',
        'experience_years',
        'vessel_name',
        'vessel_type',
        'license_number',
        'credit_score',
        'total_transactions',
        'compliance_rate',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Specify which columns should receive UUIDs automatically.
     */
    public function uniqueIds(): array
    {
        return ['profile_id'];
    }
}
