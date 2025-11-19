<?php

return [
    'default' => env('BROADCAST_CONNECTION', env('BROADCAST_DRIVER', 'log')),

    'connections' => [
        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host' => env('REVERB_HOST', env('VITE_REVERB_HOST', '127.0.0.1')),
                'port' => env('REVERB_PORT', env('VITE_REVERB_PORT', 6001)),
                'scheme' => env('REVERB_SCHEME', env('VITE_REVERB_SCHEME', 'http')),
                'useTLS' => env('REVERB_SCHEME', env('VITE_REVERB_SCHEME', 'http')) === 'https',
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', env('VITE_PUSHER_APP_CLUSTER', 'mt1')),
                'host' => env('PUSHER_HOST', env('VITE_PUSHER_HOST')),
                'port' => env('PUSHER_PORT', env('VITE_PUSHER_PORT', 6001)),
                'scheme' => env('PUSHER_SCHEME', env('VITE_PUSHER_SCHEME', 'http')),
                'useTLS' => env('PUSHER_SCHEME', env('VITE_PUSHER_SCHEME', 'http')) === 'https',
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],

];
