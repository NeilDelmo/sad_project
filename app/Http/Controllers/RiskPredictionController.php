<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiskPredictionController extends Controller
{
    public function showForm()
    {
        return view('riskpredict');
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
            return view('riskpredict', [
                'result' => 'Error: Prediction failed. Check logs.',
                'input' => $data,
                'debug' => $output // for debugging visibility
            ]);
        }

        // Trim whitespace and return
        return view('riskpredict', [
            'result' => trim(preg_replace('/\s+/', ' ', $output)),
            'input' => $data
        ]);
    }
}