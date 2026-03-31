<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Map Provider
    |--------------------------------------------------------------------------
    |
    | Choose which map provider to use: 'google' or 'leaflet'.
    | - 'google' requires a Google Maps API key (default, most accurate)
    | - 'leaflet' uses OpenStreetMap + Nominatim, completely free
    |
    */
    'provider' => env('PINPOINT_PROVIDER', 'google'),

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
    | Only required when provider = 'google'.
    |
    */
    'api_key' => env('GOOGLE_MAPS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Leaflet Settings
    |--------------------------------------------------------------------------
    |
    | Settings used when provider = 'leaflet'.
    |
    | tile_url       - Tile server URL. Default: OpenStreetMap (free).
    |                  Other free options:
    |                  CartoDB Light:  https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png
    |                  CartoDB Dark:   https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png
    |
    | tile_url_dark  - Optional dark mode tile URL. If set, will be used when
    |                  Filament is in dark mode. Leave null to always use tile_url.
    |
    | tile_attribution - Attribution text (required by OpenStreetMap ToS).
    |
    | nominatim_url  - Nominatim API base URL for search & reverse geocoding.
    |                  For production heavy-usage, consider self-hosting or
    |                  using a commercial provider (MapTiler, etc).
    |
    */
    'leaflet' => [
        'tile_url'         => env('LEAFLET_TILE_URL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
        'tile_url_dark'    => env('LEAFLET_TILE_URL_DARK', null),
        'tile_attribution' => env('LEAFLET_TILE_ATTRIBUTION', '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'),
        'nominatim_url'    => env('NOMINATIM_URL', 'https://nominatim.openstreetmap.org'),
    ],

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
        'lat'    => env('GOOGLE_MAPS_DEFAULT_LAT', -0.5050),
        'lng'    => env('GOOGLE_MAPS_DEFAULT_LNG', 117.1500),
        'zoom'   => env('GOOGLE_MAPS_DEFAULT_ZOOM', 13),
        'height' => env('GOOGLE_MAPS_DEFAULT_HEIGHT', 400),
        'radius' => env('GOOGLE_MAPS_DEFAULT_RADIUS', 500),
    ],
];
