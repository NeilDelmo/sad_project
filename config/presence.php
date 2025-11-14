<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Online Presence Window
    |--------------------------------------------------------------------------
    |
    | Number of minutes a user is considered "online" after their last activity.
    | Default: 5 minutes
    |
    */
    'window_minutes' => env('PRESENCE_WINDOW_MINUTES', 5),
];
