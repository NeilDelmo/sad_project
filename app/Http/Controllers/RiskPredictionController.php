<?php

namespace App\Http\Controllers;

use App\Models\RiskPredictionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiskPredictionController extends Controller
{
    public function publicMap()
    {
        // Public fishing safety map - no authentication required
        return view('fishing-safety-public');
    }

    public function showForm()
    {
        $recentLogs = RiskPredictionLog::query()
            ->latest('predicted_at')
            ->take(5)
            ->get();

        return view('riskpredict', compact('recentLogs'));
    }

    public function predict(Request $request)
    {
        $data = [
            'wind_speed_kph' => $request->wind_speed_kph,
            'wave_height_m' => $request->wave_height_m,
            'rainfall_mm' => $request->rainfall_mm,
            'tide_level_m' => $request->tide_level_m,
            'moon_phase' => $request->moon_phase,
            'visibility_km' => $request->visibility_km,
            'past_incidents_nearby' => $request->past_incidents_nearby,
            'location' => $request->location
        ];

        // Convert input to JSON
        $json = json_encode($data);

        // Define paths
        $basePath = base_path();
        $pythonVenv = "$basePath/python/venv/bin/python3";
        $pythonSystem = "/usr/bin/python3";
        $script = "$basePath/python/predict_risk.py";

        // Pick which Python to use (prefer venv)
        $python = file_exists($pythonVenv) ? $pythonVenv : $pythonSystem;

        // Escape JSON safely for shell
        $escapedJson = escapeshellarg($json);

        // Build the command with absolute paths
        $command = "$python $script $escapedJson";

        // Log for debugging (optional)
        Log::info("Running ML command: $command");

        // Execute the Python command and capture full output & errors
        $output = shell_exec("$command 2>&1");

        Log::info("Python output: " . $output);

        // Handle failures or empty output
        if (empty($output) || str_contains(strtolower($output), 'error')) {
            RiskPredictionLog::create([
                'user_id' => $request->user()?->id,
                'wind_speed_kph' => $data['wind_speed_kph'],
                'wave_height_m' => $data['wave_height_m'],
                'rainfall_mm' => $data['rainfall_mm'],
                'tide_level_m' => $data['tide_level_m'],
                'moon_phase' => $data['moon_phase'],
                'visibility_km' => $data['visibility_km'],
                'past_incidents_nearby' => $data['past_incidents_nearby'],
                'location' => $data['location'],
                'result' => 'Error',
                'raw_output' => $output,
                'predicted_at' => now(),
                'data_source' => 'manual',
            ]);

            $recentLogs = RiskPredictionLog::query()->latest('predicted_at')->take(5)->get();

            return view('riskpredict', [
                'result' => 'Error: Prediction failed. Check logs.',
                'input' => $data,
                'debug' => $output, // for debugging visibility
                'recentLogs' => $recentLogs,
            ]);
        }

        // Trim whitespace; python sometimes returns newline heavy output
        $normalizedResult = trim(preg_replace('/\s+/', ' ', $output));

        RiskPredictionLog::create([
            'user_id' => $request->user()?->id,
            'wind_speed_kph' => $data['wind_speed_kph'],
            'wave_height_m' => $data['wave_height_m'],
            'rainfall_mm' => $data['rainfall_mm'],
            'tide_level_m' => $data['tide_level_m'],
            'moon_phase' => $data['moon_phase'],
            'visibility_km' => $data['visibility_km'],
            'past_incidents_nearby' => $data['past_incidents_nearby'],
            'location' => $data['location'],
            'result' => $normalizedResult,
            'raw_output' => $output,
            'predicted_at' => now(),
            'data_source' => 'manual',
        ]);

        $recentLogs = RiskPredictionLog::query()->latest('predicted_at')->take(5)->get();

        return view('riskpredict', [
            'result' => $normalizedResult,
            'input' => $data,
            'recentLogs' => $recentLogs,
        ]);
    }

    public function history(Request $request)
    {
        $locationFilter = trim((string) $request->input('q'));
        $riskFilter = trim((string) $request->input('risk'));

        $baseQuery = RiskPredictionLog::query()
            ->filterLocation($locationFilter)
            ->filterRisk($riskFilter)
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('predicted_at', '>=', $request->input('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('predicted_at', '<=', $request->input('to'));
            });

        $logs = (clone $baseQuery)
            ->latest('predicted_at')
            ->paginate(15)
            ->withQueryString();

        $highlightLog = null;
        if (! empty($locationFilter)) {
            $highlightLog = RiskPredictionLog::query()
                ->filterLocation($locationFilter)
                ->latest('predicted_at')
                ->first();
        }

        $stats = [
            'low' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->where('risk_level', 0)
                        ->orWhere('result', 'like', '%low%')
                        ->orWhere('result', 'like', '%safe%');
                })
                ->count(),
            'medium' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->where('risk_level', 1)
                        ->orWhere('result', 'like', '%medium%')
                        ->orWhere('result', 'like', '%caution%');
                })
                ->count(),
            'high' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->where('risk_level', '>=', 2)
                        ->orWhere('result', 'like', '%high%')
                        ->orWhere('result', 'like', '%danger%')
                        ->orWhere('result', 'like', '%extreme%');
                })
                ->count(),
            'error' => (clone $baseQuery)->where('result', 'like', '%error%')->count(),
        ];

        $filters = $request->only(['q', 'risk', 'from', 'to']);

        return view('riskpredict-log', compact('logs', 'filters', 'highlightLog', 'stats'));
    }

    public function latest(Request $request)
    {
        $validated = $request->validate([
            'location' => ['required', 'string', 'max:120'],
        ]);

        $log = RiskPredictionLog::query()
            ->filterLocation($validated['location'])
            ->latest('predicted_at')
            ->first();

        if (! $log) {
            return response()->json([
                'message' => 'No prediction log found for the requested location.',
            ], 404);
        }

        return response()->json([
            'location' => $log->location,
            'result' => $log->result,
            'predicted_at' => $log->predicted_at,
            'inputs' => [
                'wind_speed_kph' => $log->wind_speed_kph,
                'wave_height_m' => $log->wave_height_m,
                'rainfall_mm' => $log->rainfall_mm,
                'tide_level_m' => $log->tide_level_m,
                'moon_phase' => $log->moon_phase,
                'visibility_km' => $log->visibility_km,
                'past_incidents_nearby' => $log->past_incidents_nearby,
            ],
        ]);
    }
}