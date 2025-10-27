<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;

class FishermanProfile extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'profile_id';
    public $incrementing = false;
    protected $keyType = 'string';

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

    public function user() {
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
