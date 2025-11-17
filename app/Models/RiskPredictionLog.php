<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Stores each ML prediction so we can build historical insights.
 */
class RiskPredictionLog extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected array $auditExclude = [
        'raw_output',
        'override_reasons',
        'environmental_flags',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'wind_speed_kph',
        'wave_height_m',
        'rainfall_mm',
        'tide_level_m',
        'moon_phase',
        'visibility_km',
        'past_incidents_nearby',
        'location',
        'result',
        'raw_output',
        'predicted_at',
        'latitude',
        'longitude',
        'risk_level',
        'confidence',
        'override_reasons',
        'environmental_flags',
        'data_source',
    ];

    protected $casts = [
        'wind_speed_kph' => 'float',
        'wave_height_m' => 'float',
        'rainfall_mm' => 'float',
        'tide_level_m' => 'float',
        'moon_phase' => 'integer',
        'visibility_km' => 'float',
        'past_incidents_nearby' => 'integer',
        'predicted_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'risk_level' => 'integer',
        'confidence' => 'float',
        'override_reasons' => 'array',
        'environmental_flags' => 'array',
    ];

    public function scopeFilterLocation($query, ?string $location): void
    {
        if (! empty($location)) {
            $query->where('location', 'like', '%' . $location . '%');
        }
    }

    public function scopeFilterRisk($query, ?string $risk): void
    {
        if (! empty($risk)) {
            $query->where('result', 'like', '%' . $risk . '%');
        }
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
