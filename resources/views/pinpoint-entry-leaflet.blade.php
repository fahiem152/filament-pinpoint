{{--
    Pinpoint Entry - Leaflet.js Location Display for Filament 4 & 5 Infolists

    Free alternative to Google Maps using OpenStreetMap tiles.
    Features: single/multiple markers, SVG colored pins, popups, fitBounds.

    @author Fahiem
    @package fahiem/filament-pinpoint
--}}
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $defaultLat      = $getDefaultLat();
        $defaultLng      = $getDefaultLng();
        $defaultZoom     = $getDefaultZoom();
        $height          = $getHeight();
        $lat             = $getLat();
        $lng             = $getLng();
        $radius          = $getRadius();
        $pins            = $getPins();
        $hasPins         = $hasPins();
        $fitBounds       = $getFitBounds();
        $tileUrl         = $getTileUrl();
        $tileUrlDark     = $getTileUrlDark();
        $tileAttribution = $getTileAttribution();
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
            tileUrl: @js($tileUrl),
            tileUrlDark: @js($tileUrlDark),
            tileAttribution: @js($tileAttribution),
            isMapLoaded: false,

            pinColors: {
                'red':    '#ef4444',
                'blue':   '#3b82f6',
                'green':  '#22c55e',
                'yellow': '#eab308',
                'purple': '#a855f7',
                'pink':   '#ec4899',
                'orange': '#f97316',
                'ltblue': '#22d3ee',
            },

            isDarkMode() {
                return document.documentElement.classList.contains('dark');
            },

            init() {
                this.loadLeaflet();
            },

            loadLeaflet() {
                if (window.L) {
                    this.$nextTick(() => this.initMap());
                    return;
                }

                // Register listener — fires once when Leaflet is ready
                document.addEventListener('pinpoint:leaflet:loaded', () => this.initMap(), { once: true });

                // Only one component actually loads the script
                if (window.leafletLoading) return;
                window.leafletLoading = true;

                if (!document.getElementById('leaflet-css')) {
                    const link = document.createElement('link');
                    link.id = 'leaflet-css';
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(link);
                }

                const script = document.createElement('script');
                script.id = 'leaflet-js';
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = () => {
                    window.leafletLoading = false;
                    document.dispatchEvent(new CustomEvent('pinpoint:leaflet:loaded'));
                };
                document.head.appendChild(script);
            },

            initMap() {
                const mapElement = this.$refs.map;
                if (!mapElement) return;

                this.map = L.map(mapElement, {
                    center: [this.lat, this.lng],
                    zoom: this.defaultZoom,
                    zoomControl: true,
                    scrollWheelZoom: true,
                    attributionControl: true,
                });

                const activeTileUrl = (this.isDarkMode() && this.tileUrlDark)
                    ? this.tileUrlDark
                    : this.tileUrl;

                L.tileLayer(activeTileUrl, {
                    attribution: this.tileAttribution,
                    maxZoom: 19,
                }).addTo(this.map);

                if (this.hasPins && this.pins.length > 0) {
                    this.initMultipleMarkers();
                } else {
                    this.initSingleMarker();
                }

                this.isMapLoaded = true;
            },

            makePinIcon(color) {
                const hex = this.pinColors[color] || this.pinColors['red'];
                const svg = `<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 36' width='24' height='36'>`
                    + `<path d='M12 0C5.4 0 0 5.4 0 12c0 9 12 24 12 24s12-15 12-24C24 5.4 18.6 0 12 0z' fill='${hex}' stroke='white' stroke-width='1.5'/>`
                    + `<circle cx='12' cy='12' r='5' fill='white' opacity='0.85'/>`
                    + `</svg>`;
                return L.divIcon({
                    className: '',
                    html: svg,
                    iconSize: [24, 36],
                    iconAnchor: [12, 36],
                    popupAnchor: [0, -38],
                });
            },

            makeCustomIcon(url) {
                return L.icon({
                    iconUrl: url,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -34],
                });
            },

            initSingleMarker() {
                this.marker = L.marker([this.lat, this.lng], {
                    draggable: false,
                    icon: this.makePinIcon('red'),
                }).addTo(this.map);

                if (this.radius) {
                    this.circle = L.circle([this.lat, this.lng], {
                        radius: this.radius,
                        color: '#4285F4',
                        fillColor: '#4285F4',
                        fillOpacity: 0.2,
                        weight: 2,
                    }).addTo(this.map);
                }
            },

            initMultipleMarkers() {
                const bounds = L.latLngBounds([]);

                this.pins.forEach((pin) => {
                    const lat = parseFloat(pin.lat);
                    const lng = parseFloat(pin.lng);
                    if (isNaN(lat) || isNaN(lng)) return;

                    let icon;
                    if (pin.icon) {
                        icon = this.makeCustomIcon(pin.icon);
                    } else if (pin.color) {
                        icon = this.makePinIcon(pin.color);
                    } else {
                        icon = this.makePinIcon('red');
                    }

                    const m = L.marker([lat, lng], { draggable: false, icon }).addTo(this.map);

                    const popupContent = pin.info
                        || (pin.label ? `<div style='padding:4px 8px;font-size:14px;'>${pin.label}</div>` : null);

                    if (popupContent) {
                        m.bindPopup(popupContent);
                        m.on('click', () => m.openPopup());
                    }

                    this.markers.push(m);
                    bounds.extend([lat, lng]);
                });

                if (this.fitBounds && this.markers.length > 0) {
                    this.map.fitBounds(bounds, { padding: [32, 32] });
                    if (this.markers.length === 1) {
                        this.map.setZoom(this.defaultZoom);
                    }
                }
            },
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

    <style>
        /* Leaflet z-index fix inside Filament panels */
        .fi-in-pinpoint-entry .leaflet-pane,
        .fi-in-pinpoint-entry .leaflet-control { z-index: 1 !important; }
        .fi-in-pinpoint-entry .leaflet-top,
        .fi-in-pinpoint-entry .leaflet-bottom  { z-index: 2 !important; }
    </style>
</x-dynamic-component>
