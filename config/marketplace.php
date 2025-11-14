<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform Commission Rate
    |--------------------------------------------------------------------------
    |
    | The percentage of each marketplace sale that goes to the platform.
    | This is deducted from the ML dynamic price.
    |
    */
    'platform_commission_rate' => env('MARKETPLACE_COMMISSION_RATE', 0.10), // 10%

    /*
    |--------------------------------------------------------------------------
    | Pricing Model Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the machine learning dynamic pricing model.
    |
    */
    'ml_pricing' => [
        'enabled' => env('ML_PRICING_ENABLED', true),
        'python_path' => env('ML_PYTHON_PATH', 'python3'),
        'script_path' => base_path('python/predict_price.py'),
        'model_path' => base_path('python/pricing_model.pkl'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Vendor Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for vendor marketplace access and inventory.
    |
    */
    'vendor' => [
        'min_inventory_for_listing' => 1,
        'auto_list_on_purchase' => env('AUTO_LIST_ON_PURCHASE', false),
    ],
];
