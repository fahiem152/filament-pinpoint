<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key
    |--------------------------------------------------------------------------
    |
    | Your Google Maps API key. You can get one from the Google Cloud Console.
    | Make sure to enable the following APIs:
    | - Maps JavaScript API
    | - Places API
    | - Geocoding API
    |
    */
    'api_key' => env('GOOGLE_MAPS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Map Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings for the map picker. You can override
    | these values when using the component.
    |
    */
    'default' => [
        'lat' => env('GOOGLE_MAPS_DEFAULT_LAT', -0.5050),
        'lng' => env('GOOGLE_MAPS_DEFAULT_LNG', 117.1500),
        'zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 13),
        'height' => env('GOOGLE_MAPS_DEFAULT_HEIGHT', 400),
        'radius' => env('GOOGLE_MAPS_DEFAULT_RADIUS', 500),
    ],
];
