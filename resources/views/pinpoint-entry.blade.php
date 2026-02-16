{{--
    Pinpoint Entry - Google Maps Location Display for Filament 4 & 5 Infolists
    @author Fahiem
    @version 1.2.0
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
        $radius = $getRadius();
        $apiKey = $getApiKey();
        $pins = $getPins();
        $hasPins = $hasPins();
        $fitBounds = $getFitBounds();
    @endphp

    <div
        x-data="{
            map: null,
            marker: null,
            circle: null,
            markers: [],
            lat: parseFloat(@js($lat)) || @js($defaultLat),
            lng: parseFloat(@js($lng)) || @js($defaultLng),
            radius: parseFloat(@js($radius)) || null,
            defaultZoom: @js($defaultZoom),
            pins: @js($pins ?? []),
            hasPins: @js($hasPins),
            fitBounds: @js($fitBounds),
            isMapLoaded: false,

            markerColors: {
                'red': 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                'blue': 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                'green': 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                'yellow': 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                'purple': 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png',
                'pink': 'http://maps.google.com/mapfiles/ms/icons/pink-dot.png',
                'orange': 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png',
                'ltblue': 'http://maps.google.com/mapfiles/ms/icons/ltblue-dot.png',
            },

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
                    console.error('Google Maps API key is not configured.');
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

                if (this.hasPins && this.pins.length > 0) {
                    this.initMultipleMarkers();
                } else {
                    this.initSingleMarker();
                }

                this.isMapLoaded = true;
            },

            initSingleMarker() {
                this.marker = new google.maps.Marker({
                    position: { lat: this.lat, lng: this.lng },
                    map: this.map,
                    draggable: false,
                    animation: google.maps.Animation.DROP,
                });

                if (this.radius) {
                    this.circle = new google.maps.Circle({
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.35,
                        map: this.map,
                        center: { lat: this.lat, lng: this.lng },
                        radius: this.radius,
                        editable: false,
                        draggable: false
                    });
                }
            },

            initMultipleMarkers() {
                const bounds = new google.maps.LatLngBounds();

                this.pins.forEach((pin, index) => {
                    const lat = parseFloat(pin.lat);
                    const lng = parseFloat(pin.lng);

                    if (isNaN(lat) || isNaN(lng)) return;

                    const markerOptions = {
                        position: { lat: lat, lng: lng },
                        map: this.map,
                        draggable: false,
                        title: pin.label || null,
                    };

                    // Custom icon URL (highest priority)
                    if (pin.icon) {
                        markerOptions.icon = pin.icon;
                    }
                    // Predefined color
                    else if (pin.color && this.markerColors[pin.color]) {
                        markerOptions.icon = this.markerColors[pin.color];
                    }

                    const marker = new google.maps.Marker(markerOptions);

                    // Custom info window content or default label
                    const infoContent = pin.info || (pin.label ? `<div style='padding: 4px 8px; font-size: 14px;'>${pin.label}</div>` : null);

                    if (infoContent) {
                        const infoWindow = new google.maps.InfoWindow({
                            content: infoContent
                        });

                        marker.addListener('click', () => {
                            infoWindow.open(this.map, marker);
                        });
                    }

                    this.markers.push(marker);
                    bounds.extend(marker.getPosition());
                });

                if (this.fitBounds && this.markers.length > 0) {
                    this.map.fitBounds(bounds);

                    if (this.markers.length === 1) {
                        this.map.setZoom(this.defaultZoom);
                    }
                }
            }
        }"
        x-init="init()"
        class="fi-in-pinpoint-entry"
    >
        <div class="relative rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700">
            <div x-ref="map" style="height: {{ $height }}px; width: 100%;" class="bg-gray-100 dark:bg-gray-800">
                <div x-show="!isMapLoaded" style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <div style="display: flex; align-items: center; gap: 8px;" class="text-gray-500 dark:text-gray-400">
                        <svg class="animate-spin" style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('filament-pinpoint::pinpoint.loading_map') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
