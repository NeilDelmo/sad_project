<?php

namespace App\Http\Controllers;

use App\Models\RiskPredictionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FishingSafetyController extends Controller
{
    // Use host.docker.internal to access host machine from Docker container
    private $flaskApiUrl = 'http://host.docker.internal:5000';

    /**
     * Check fishing safety for a location
     */
    public function checkSafety(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        try {
            $response = Http::timeout(30)->post("{$this->flaskApiUrl}/api/fishing-safety", [
                'lat' => $request->lat,
                'lon' => $request->lon,
            ]);

            if ($response->successful()) {
                $payload = $response->json();
                $this->storePredictionLog($request, $payload);

                return response()->json($payload);
            }

            return response()->json([
                'error' => 'Failed to fetch safety data from prediction service'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to connect to prediction service: ' . $e->getMessage()
            ], 503);
        }
    }

    /**
     * Check multiple locations
     */
    public function checkBatch(Request $request)
    {
        $request->validate([
            'locations' => 'required|array',
            'locations.*.lat' => 'required|numeric|between:-90,90',
            'locations.*.lon' => 'required|numeric|between:-180,180',
        ]);

        try {
            $response = Http::timeout(60)->post("{$this->flaskApiUrl}/api/fishing-safety/batch", [
                'locations' => $request->locations,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch batch safety data'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to connect to prediction service: ' . $e->getMessage()
            ], 503);
        }
    }

    /**
     * Get weather map data
     */
    public function weatherMap(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        try {
            $response = Http::timeout(15)->post("{$this->flaskApiUrl}/api/weather-map", [
                'lat' => $request->lat,
                'lon' => $request->lon,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch weather map data'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to connect to prediction service: ' . $e->getMessage()
            ], 503);
        }
    }

    public function recordOutcome(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
            'outcome' => 'required|string|in:Safe,Caution,Dangerous',
            'api_verdict' => 'nullable|string|max:64',
            'features' => 'required|array',
            'notes' => 'nullable|string|max:500',
        ]);

        $features = $request->input('features');

        if (! is_array($features)) {
            return response()->json([
                'error' => 'Invalid features payload',
            ], 422);
        }

        $payload = [
            'lat' => (float) $request->lat,
            'lon' => (float) $request->lon,
            'outcome' => $request->input('outcome'),
            'api_verdict' => $request->input('api_verdict'),
            'features' => $features,
        ];

        $notes = trim((string) $request->input('notes', ''));
        if ($notes !== '') {
            $payload['notes'] = $notes;
        }

        try {
            $response = Http::timeout(20)->post("{$this->flaskApiUrl}/api/record-trip-outcome", $payload);

            if ($response->successful()) {
                return response()->json($response->json(), 201);
            }

            $errorMessage = $response->json('error') ?? 'Failed to record trip outcome';

            return response()->json([
                'error' => $errorMessage,
            ], $response->status() ?: 500);
        } catch (\Throwable $e) {
            Log::warning('Trip outcome logging failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Unable to record outcome: ' . $e->getMessage(),
            ], 503);
        }
    }

    public function history(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
            'radius_km' => 'sometimes|numeric|min:1|max:100',
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);

        $lat = (float) $request->lat;
        $lon = (float) $request->lon;
        $radius = min(max($request->float('radius_km', 8.0), 1.0), 100.0);
        $limit = min(max((int) $request->input('limit', 20), 1), 50);

        $latDelta = $radius / 111.0;
        $lonScale = cos(deg2rad($lat));
        $lonScale = abs($lonScale) < 0.01 ? 0.01 : $lonScale;
        $lonDelta = $radius / (111.0 * $lonScale);

        $candidateLogs = RiskPredictionLog::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lon - $lonDelta, $lon + $lonDelta])
            ->orderByDesc('predicted_at')
            ->limit($limit * 3)
            ->get();

        $logs = $candidateLogs
            ->map(function (RiskPredictionLog $log) use ($lat, $lon) {
                $log->distance_km = $this->calculateDistanceKm($lat, $lon, (float) $log->latitude, (float) $log->longitude);

                return $log;
            })
            ->filter(function (RiskPredictionLog $log) use ($radius) {
                return isset($log->distance_km) && $log->distance_km <= $radius;
            })
            ->values()
            ->take($limit);

        $counts = [
            'safe' => 0,
            'caution' => 0,
            'dangerous' => 0,
        ];

        foreach ($logs as $log) {
            $label = Str::of((string) $log->result)->lower();

            if ($label->contains('danger')) {
                $counts['dangerous']++;
            } elseif ($label->contains('caution')) {
                $counts['caution']++;
            } elseif ($label->contains('safe')) {
                $counts['safe']++;
            }
        }

        $averageConfidence = $logs->filter(fn ($log) => ! is_null($log->confidence))->avg('confidence');
        $lastDangerous = $logs->first(function (RiskPredictionLog $log) {
            if ((int) $log->risk_level === 2) {
                return true;
            }

            return Str::of((string) $log->result)->lower()->contains('danger');
        });

        return response()->json([
            'summary' => [
                'total' => $logs->count(),
                'counts' => $counts,
                'average_confidence_percent' => $averageConfidence ? round($averageConfidence * 100, 1) : null,
                'last_dangerous_at' => optional($lastDangerous?->predicted_at)->toIso8601String(),
            ],
            'logs' => $logs->map(function (RiskPredictionLog $log) {
                return [
                    'id' => $log->id,
                    'location' => $log->location,
                    'latitude' => $log->latitude,
                    'longitude' => $log->longitude,
                    'verdict' => $log->result,
                    'risk_level' => $log->risk_level,
                    'confidence' => $log->confidence,
                    'predicted_at' => optional($log->predicted_at)->toIso8601String(),
                    'distance_km' => isset($log->distance_km) ? round((float) $log->distance_km, 2) : null,
                    'wind_speed_kph' => $log->wind_speed_kph,
                    'wave_height_m' => $log->wave_height_m,
                    'rainfall_mm' => $log->rainfall_mm,
                    'tide_level_m' => $log->tide_level_m,
                    'visibility_km' => $log->visibility_km,
                    'override_reasons' => $log->override_reasons ?? [],
                    'environmental_flags' => $log->environmental_flags ?? [],
                    'data_source' => $log->data_source,
                ];
            }),
            'radius_used' => $radius,
        ]);
    }

    protected function storePredictionLog(Request $request, array $payload): void
    {
        try {
            $conditions = Arr::get($payload, 'weather_conditions');

            if (! $conditions) {
                return;
            }

            $historical = Arr::get($payload, 'historical_data', []);
            $environmental = Arr::get($payload, 'environmental_context', []);

            $moonPhase = Arr::get($conditions, 'moon_phase', 0);
            $moonPhase = is_numeric($moonPhase) ? max(0, min(100, (int) round($moonPhase * 100))) : 0;

            $pastIncidents = Arr::get($historical, 'past_incidents_nearby', 0);
            $pastIncidents = is_numeric($pastIncidents) ? max(0, (int) round($pastIncidents)) : 0;

            $confidence = Arr::get($payload, 'safety_assessment.confidence');
            $overrideReasons = Arr::get($payload, 'safety_assessment.override_reasons', []);
            $flags = Arr::get($environmental, 'flags', []);

            RiskPredictionLog::create([
                'user_id' => $request->user()?->id,
                'wind_speed_kph' => round((float) Arr::get($conditions, 'wind_speed_kph', 0), 2),
                'wave_height_m' => round((float) Arr::get($conditions, 'wave_height_m', 0), 2),
                'rainfall_mm' => round((float) Arr::get($conditions, 'rainfall_mm', 0), 2),
                'tide_level_m' => round((float) Arr::get($conditions, 'tide_level_m', 0), 2),
                'moon_phase' => $moonPhase,
                'visibility_km' => round((float) Arr::get($conditions, 'visibility_km', 0), 2),
                'past_incidents_nearby' => $pastIncidents,
                'location' => Arr::get($payload, 'location.name') ?: sprintf('%.4f, %.4f', $request->lat, $request->lon),
                'result' => Arr::get($payload, 'safety_assessment.verdict', 'Unknown'),
                'raw_output' => json_encode($payload),
                'predicted_at' => now(),
                'latitude' => round((float) $request->lat, 6),
                'longitude' => round((float) $request->lon, 6),
                'risk_level' => Arr::get($payload, 'safety_assessment.risk_level'),
                'confidence' => is_numeric($confidence) ? round((float) $confidence, 4) : null,
                'override_reasons' => $overrideReasons ?: null,
                'environmental_flags' => $flags ?: null,
                'data_source' => 'map-api',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to store fishing safety log', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function calculateDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(min(1, sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2))));

        return 6371 * $angle;
    }

    /**
     * Health check - proxies to Flask API
     */
    public function health()
    {
        try {
            $response = Http::timeout(5)->get("{$this->flaskApiUrl}/api/health");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Flask API not responding'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Unable to connect to Flask API: ' . $e->getMessage()
            ], 503);
        }
    }

    /**
     * Setup check - proxies to Flask API
     */
    public function setupCheck()
    {
        try {
            $response = Http::timeout(5)->get("{$this->flaskApiUrl}/api/setup-check");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Flask API not responding'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to connect to Flask API: ' . $e->getMessage()
            ], 503);
        }
    }
}
