{{--
    Pinpoint Entry - Google Maps Location Display for Filament 4 Infolists

    A read-only map display for infolists showing location with a marker.
    Features: Static marker, no interaction, dark mode support.

    @author Fahiem
    @version 1.0.0
    @package fahiem/filament-pinpoint
--}}
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $defaultLat = $getDefaultLat();
        $defaultLng = $getDefaultLng();
        $defaultZoom = $getDefaultZoom();
        $height = $getHeight();
        $lat = $getLat();
        $lng = $getLng();
        $apiKey = $getApiKey();
    @endphp

    <div x-data="{
        map: null,
        marker: null,
        lat: parseFloat(@js($lat)) || @js($defaultLat),
        lng: parseFloat(@js($lng)) || @js($defaultLng),
        defaultZoom: @js($defaultZoom),
        isMapLoaded: false,
    
        init() {
            this.loadGoogleMaps();
        },
    
        loadGoogleMaps() {
            if (window.google && window.google.maps) {
                this.initMap();
                return;
            }
    
            if (window.googleMapsLoading) {
                window.googleMapsCallbacks = window.googleMapsCallbacks || [];
                window.googleMapsCallbacks.push(() => this.initMap());
                return;
            }
    
            window.googleMapsLoading = true;
            window.googleMapsCallbacks = [];
    
            const apiKey = '{{ $apiKey }}';
            if (!apiKey) {
                console.error('Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env file.');
                return;
            }
    
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=googleMapsCallback`;
            script.async = true;
            script.defer = true;
    
            window.googleMapsCallback = () => {
                window.googleMapsLoading = false;
                this.initMap();
                window.googleMapsCallbacks.forEach(cb => cb());
                window.googleMapsCallbacks = [];
            };
    
            document.head.appendChild(script);
        },
    
        initMap() {
            const mapElement = this.$refs.map;
            if (!mapElement) return;
    
            this.map = new google.maps.Map(mapElement, {
                center: { lat: this.lat, lng: this.lng },
                zoom: this.defaultZoom,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                zoomControl: true,
                draggable: true,
                scrollwheel: true,
                disableDoubleClickZoom: false,
                gestureHandling: 'auto'
            });
    
            this.marker = new google.maps.Marker({
                position: { lat: this.lat, lng: this.lng },
                map: this.map,
                draggable: false,
                animation: google.maps.Animation.DROP,
            });
    
            this.isMapLoaded = true;
        }
    }" x-init="init()" class="fi-in-pinpoint-entry">
        {{-- Map Container --}}
        <div class="relative rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700">
            <div x-ref="map" style="height: {{ $height }}px; width: 100%;" class="bg-gray-100 dark:bg-gray-800">
                <div x-show="!isMapLoaded"
                    style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <div style="display: flex; align-items: center; gap: 8px;" class="text-gray-500 dark:text-gray-400">
                        <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px;"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>{{ __('filament-pinpoint::pinpoint.loading_map') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</x-dynamic-component>
