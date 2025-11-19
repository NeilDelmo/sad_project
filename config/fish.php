<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fish Freshness Thresholds
    |--------------------------------------------------------------------------
    |
    | Hours thresholds for freshness decay from initial assessment.
    | Fishermen set initial freshness (Very Fresh/Fresh/Good), then it decays:
    | - Very Fresh -> Fresh -> Good -> Spoiled
    | - Fresh -> Good -> Spoiled
    | - Good -> Spoiled
    |
    */
    'freshness_decay_hours' => [
        'Very Fresh' => [
            'Fresh' => 6,      // Becomes Fresh after 6 hours
            'Good' => 12,      // Becomes Good after 12 hours  
            'Spoiled' => 24,   // Becomes Spoiled after 24 hours
        ],
        'Fresh' => [
            'Good' => 8,       // Becomes Good after 8 hours
            'Spoiled' => 18,   // Becomes Spoiled after 18 hours
        ],
        'Good' => [
            'Spoiled' => 12,   // Becomes Spoiled after 12 hours
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Category-Specific Freshness Decay Multipliers
    |--------------------------------------------------------------------------
    |
    | Different fish types spoil at different rates. These multipliers adjust
    | the decay hours based on category. Lower multiplier = faster spoilage.
    | Default multiplier is 1.0 if category not specified.
    |
    */
    'category_decay_multipliers' => [
        // Fast spoilage (shellfish, crustaceans)
        'Shellfish' => 0.5,      // 50% faster decay - spoils twice as fast
        
        // Medium-fast spoilage (oily fish)
        'Oily Fish' => 0.7,      // 30% faster decay - spoils faster than white fish
        
        // Normal spoilage (white fish, default)
        'White Fish' => 1.0,     // Standard decay rate
        'Fish' => 1.0,           // Default fallback
    ],

    /*
    |--------------------------------------------------------------------------
    | Fish Category Aliases
    |--------------------------------------------------------------------------
    |
    | Names that should be treated as fish categories across the app.
    | Used to normalize queries (e.g., marketplace filters) regardless
    | of whether categories are labeled "Fish", "Fresh Fish", etc.
    |
    */
    'category_aliases' => [
        'Fish',
        'Fresh Fish',
    ],
];
