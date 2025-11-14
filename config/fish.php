<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fish Freshness Thresholds
    |--------------------------------------------------------------------------
    |
    | Minutes thresholds for each freshness stage (inclusive upper bounds).
    | Products exceeding the last threshold are considered 'Spoiled'.
    | Adjust per species/category later if needed.
    |
    */
    'freshness_threshold_minutes' => [
        'Fresh' => 360,    // <= 6 hours
        'Good' => 720,     // <= 12 hours
        'Aging' => 1200,   // <= 20 hours
        'Stale' => 1680,   // <= 28 hours
        // Beyond 28 hours = Spoiled
    ],
];
