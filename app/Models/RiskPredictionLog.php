<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores each ML prediction so we can build historical insights.
 */
class RiskPredictionLog extends Model
{
    use HasFactory;

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
