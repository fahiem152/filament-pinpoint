{{--
    Pinpoint - Leaflet.js Location Picker for Filament 4 & 5

    Free alternative to Google Maps using OpenStreetMap + Nominatim.
    Features: Search (Nominatim), draggable marker, resizable radius circle,
    reverse geocoding, current location, dark mode.

    @author Fahiem
    @package fahiem/filament-pinpoint
--}}
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $statePath        = $getStatePath();
        $defaultLat       = $getDefaultLat();
        $defaultLng       = $getDefaultLng();
        $defaultZoom      = $getDefaultZoom();
        $height           = $getHeight();
        $isDraggable      = $isDraggable();
        $isSearchable     = $isSearchable();
        $isReadOnly       = $isReadOnly();

        if ($isReadOnly) {
            $isDraggable  = false;
            $isSearchable = false;
        }
        $latField         = $getLatField();
        $lngField         = $getLngField();
        $radiusField      = $getRadiusField();
        $addressField     = $getAddressField();
        $shortAddressField = $getShortAddressField();
        $streetField      = $getStreetField();
        $streetNumberField = $getStreetNumberField();
        $provinceField    = $getProvinceField();
        $villageField     = $getVillageField();
        $cityField        = $getCityField();
        $districtField    = $getDistrictField();
        $postalCodeField  = $getPostalCodeField();
        $countryField     = $getCountryField();
        $tileUrl          = $getTileUrl();
        $tileUrlDark      = $getTileUrlDark();
        $tileAttribution  = $getTileAttribution();
        $nominatimUrl     = $getNominatimUrl();

        $state          = $getState();
        $currentLat     = $state['lat'] ?? $defaultLat;
        $currentLng     = $state['lng'] ?? $defaultLng;
        $currentRadius  = $state['radius'] ?? $getDefaultRadius();
        $currentAddress = $state['address'] ?? '';
    @endphp

    <div
        wire:ignore
        x-data="{
            map: null,
            marker: null,
            circle: null,
            radiusHandle: null,
            tileLayer: null,
            lat: parseFloat(@js($currentLat)) || @js($defaultLat),
            lng: parseFloat(@js($currentLng)) || @js($defaultLng),
            radius: parseInt(@js($currentRadius)) || 0,
            address: @js($currentAddress),
            defaultLat: @js($defaultLat),
            defaultLng: @js($defaultLng),
            defaultZoom: @js($defaultZoom),
            isDraggable: @js($isDraggable),
            isSearchable: @js($isSearchable),
            isReadOnly: @js($isReadOnly),
            statePath: @js($statePath),
            latField: @js($latField),
            lngField: @js($lngField),
            radiusField: @js($radiusField),
            addressField: @js($addressField),
            shortAddressField: @js($shortAddressField),
            streetField: @js($streetField),
            streetNumberField: @js($streetNumberField),
            provinceField: @js($provinceField),
            cityField: @js($cityField),
            districtField: @js($districtField),
            postalCodeField: @js($postalCodeField),
            countryField: @js($countryField),
            villageField: @js($villageField),
            tileUrl: @js($tileUrl),
            tileUrlDark: @js($tileUrlDark),
            tileAttribution: @js($tileAttribution),
            nominatimUrl: @js($nominatimUrl),
            isMapLoaded: false,

            // Search state
            searchResults: [],
            showDropdown: false,
            searchTimeout: null,
            isSearching: false,

            getPrimaryColor() {
                const raw = getComputedStyle(document.documentElement).getPropertyValue('--primary-500').trim();
                return raw || '#3b82f6';
            },

            getFieldPath(fieldName) {
                if (!fieldName) return null;
                const lastDotIndex = this.statePath.lastIndexOf('.');
                const basePath = lastDotIndex > -1 ? this.statePath.substring(0, lastDotIndex + 1) : 'data.';
                return basePath + fieldName;
            },

            init() {
                this.loadExistingCoordinates();
                this.loadLeaflet();
            },

            loadExistingCoordinates() {
                const latPath = this.getFieldPath(this.latField);
                const lngPath = this.getFieldPath(this.lngField);
                const radiusPath = this.getFieldPath(this.radiusField);
                const addressPath = this.getFieldPath(this.addressField);

                if (latPath && lngPath) {
                    const existingLat = $wire.get(latPath);
                    const existingLng = $wire.get(lngPath);
                    if (existingLat && existingLng) {
                        this.lat = parseFloat(existingLat);
                        this.lng = parseFloat(existingLng);
                    }
                }
                if (radiusPath) {
                    const existingRadius = $wire.get(radiusPath);
                    if (existingRadius) this.radius = parseInt(existingRadius);
                }
                if (addressPath) {
                    const existingAddress = $wire.get(addressPath);
                    if (existingAddress) this.address = existingAddress;
                }
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

            isDarkMode() {
                return document.documentElement.classList.contains('dark');
            },

            initMap() {
                const mapElement = this.$refs.map;
                if (!mapElement) return;

                this.map = L.map(mapElement, {
                    center: [this.lat, this.lng],
                    zoom: this.defaultZoom,
                    zoomControl: true,
                    attributionControl: true,
                });

                // Use dark tile URL if in dark mode and configured
                const activeTileUrl = (this.isDarkMode() && this.tileUrlDark)
                    ? this.tileUrlDark
                    : this.tileUrl;

                this.tileLayer = L.tileLayer(activeTileUrl, {
                    attribution: this.tileAttribution,
                    maxZoom: 19,
                }).addTo(this.map);

                // Marker
                this.marker = L.marker([this.lat, this.lng], {
                    draggable: this.isDraggable,
                }).addTo(this.map);

                if (this.isDraggable) {
                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.updatePosition(pos.lat, pos.lng);
                    });
                }

                // Map click
                if (!this.isReadOnly) {
                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.updatePosition(e.latlng.lat, e.latlng.lng);
                    });
                }

                // Radius circle
                if (this.radiusField) {
                    this.initRadiusCircle();
                }

                // Search
                if (this.isSearchable) {
                    this.initSearch();
                }

                this.isMapLoaded = true;
            },

            initRadiusCircle() {
                const r = this.radius || 500;
                const circleColor = this.getPrimaryColor();
                this.circle = L.circle([this.lat, this.lng], {
                    radius: r,
                    color: circleColor,
                    fillColor: circleColor,
                    fillOpacity: 0.2,
                    weight: 2,
                }).addTo(this.map);

                if (!this.isReadOnly) {
                    // Drag handle on the east edge of the circle
                    const handlePos = this.getCircleEdgeLatLng();
                    this.radiusHandle = L.marker(handlePos, {
                        draggable: true,
                        icon: L.divIcon({
                            className: 'pinpoint-radius-handle',
                            html: '<div></div>',
                            iconSize: [14, 14],
                            iconAnchor: [7, 7],
                        }),
                    }).addTo(this.map);

                    this.radiusHandle.on('drag', (e) => {
                        const center = L.latLng(this.lat, this.lng);
                        const newRadius = Math.round(center.distanceTo(e.latlng));
                        this.radius = newRadius;
                        this.circle.setRadius(newRadius);
                        this.updateRadiusState();
                    });

                    this.radiusHandle.on('dragend', () => {
                        // Snap handle back to east edge after drag
                        this.radiusHandle.setLatLng(this.getCircleEdgeLatLng());
                    });
                }
            },

            getCircleEdgeLatLng() {
                // Place handle on the east edge of the circle
                const r = this.radius || 500;
                const earthRadius = 6371000;
                const lat = this.lat * Math.PI / 180;
                const dLng = (r / earthRadius) / Math.cos(lat);
                return L.latLng(this.lat, this.lng + (dLng * 180 / Math.PI));
            },

            initSearch() {
                // Search is handled via Alpine.js x-on:input — no extra library needed
            },

            onSearchInput(query) {
                this.showDropdown = false;
                clearTimeout(this.searchTimeout);

                if (!query || query.length < 3) {
                    this.searchResults = [];
                    return;
                }

                this.searchTimeout = setTimeout(() => {
                    this.fetchSearchResults(query);
                }, 500);
            },

            async fetchSearchResults(query) {
                this.isSearching = true;
                try {
                    const params = new URLSearchParams({
                        q: query,
                        format: 'json',
                        limit: 5,
                        addressdetails: 1,
                    });
                    const res = await fetch(`${this.nominatimUrl}/search?${params}`, {
                        headers: { 'Accept-Language': document.documentElement.lang || 'id,en' },
                    });
                    const data = await res.json();
                    this.searchResults = data;
                    this.showDropdown = data.length > 0;
                } catch (e) {
                    console.error('[Pinpoint] Nominatim search error:', e);
                } finally {
                    this.isSearching = false;
                }
            },

            selectSearchResult(result) {
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

                this.marker.setLatLng([lat, lng]);
                this.map.setView([lat, lng], 17);
                this.address = result.display_name;
                this.showDropdown = false;
                this.searchResults = [];

                this.updatePosition(lat, lng);
            },

            updatePosition(lat, lng) {
                this.lat = parseFloat(lat.toFixed(7));
                this.lng = parseFloat(lng.toFixed(7));

                if (this.circle) {
                    this.circle.setLatLng([this.lat, this.lng]);
                    if (this.radiusHandle) {
                        this.radiusHandle.setLatLng(this.getCircleEdgeLatLng());
                    }
                }

                const latPath = this.getFieldPath(this.latField);
                const lngPath = this.getFieldPath(this.lngField);
                if (latPath) $wire.set(latPath, this.lat);
                if (lngPath) $wire.set(lngPath, this.lng);

                this.reverseGeocode(lat, lng);
            },

            updateRadiusState() {
                const radiusPath = this.getFieldPath(this.radiusField);
                if (radiusPath) $wire.set(radiusPath, this.radius);
            },

            async reverseGeocode(lat, lng) {
                try {
                    const params = new URLSearchParams({
                        lat: lat,
                        lon: lng,
                        format: 'json',
                        addressdetails: 1,
                        zoom: 18,
                    });
                    const res = await fetch(`${this.nominatimUrl}/reverse?${params}`, {
                        headers: { 'Accept-Language': document.documentElement.lang || 'id,en' },
                    });
                    const data = await res.json();

                    if (!data || data.error) return;

                    const addr = data.address || {};
                    const displayName = data.display_name || '';

                    this.address = displayName;

                    const addressPath = this.getFieldPath(this.addressField);
                    if (addressPath) $wire.set(addressPath, displayName);

                    // Map Nominatim address fields
                    const street       = addr.road || addr.pedestrian || addr.footway || addr.path || '';
                    const streetNumber = addr.house_number || '';
                    const province     = addr.state || addr.province || '';
                    const city         = addr.city || addr.regency || addr.county || addr.municipality || addr.town || '';
                    const district     = addr.city_district || addr.district || addr.suburb || addr.borough || '';
                    const village      = addr.village || addr.neighbourhood || addr.quarter || addr.residential || '';
                    const postalCode   = addr.postcode || '';
                    const country      = addr.country || '';

                    let shortAddress = '';
                    if (street && streetNumber) shortAddress = `${street} ${streetNumber}`;
                    else if (street) shortAddress = street;

                    const fieldMap = {
                        shortAddressField: shortAddress || null,
                        streetField:       street || null,
                        streetNumberField: streetNumber || null,
                        provinceField:     province || null,
                        cityField:         city || null,
                        districtField:     district || null,
                        villageField:      village || null,
                        postalCodeField:   postalCode || null,
                        countryField:      country || null,
                    };

                    for (const [field, value] of Object.entries(fieldMap)) {
                        const path = this.getFieldPath(this[field]);
                        if (path) $wire.set(path, value);
                    }
                } catch (e) {
                    console.error('[Pinpoint] Nominatim reverse geocode error:', e);
                }
            },

            getCurrentLocation() {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by this browser');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        this.marker.setLatLng([lat, lng]);
                        this.map.setView([lat, lng], 17);

                        if (this.circle) {
                            this.circle.setLatLng([lat, lng]);
                            this.radiusHandle.setLatLng(this.getCircleEdgeLatLng());
                        }

                        this.updatePosition(lat, lng);
                    },
                    (error) => {
                        console.error('[Pinpoint] Geolocation error:', error);
                        alert('Failed to get location: ' + error.message);
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },
        }"
        x-init="init()"
        class="fi-fo-pinpoint"
    >
        {{-- Search Box --}}
        @if ($isSearchable)
            <div style="position: relative; margin-bottom: 12px;">
                <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 1;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; color: var(--gray-400, #9ca3af);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <div x-show="isSearching" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); z-index: 1; pointer-events: none;">
                    <svg style="animation: spin 1s linear infinite; width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="pinpoint-icon">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    x-ref="searchInput"
                    x-model="address"
                    @input="onSearchInput($event.target.value)"
                    @blur="setTimeout(() => showDropdown = false, 200)"
                    @focus="searchResults.length > 0 && (showDropdown = true)"
                    placeholder="{{ __('filament-pinpoint::pinpoint.search') }}"
                    class="pinpoint-search-input"
                />

                {{-- Search Results Dropdown --}}
                <div
                    x-show="showDropdown && searchResults.length > 0"
                    x-cloak
                    class="pinpoint-search-dropdown"
                >
                    <template x-for="(result, index) in searchResults" :key="index">
                        <button
                            type="button"
                            @mousedown.prevent="selectSearchResult(result)"
                            class="pinpoint-search-item"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 14px; height: 14px; flex-shrink: 0; margin-top: 2px; color: var(--gray-400, #9ca3af);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <span class="pinpoint-search-item-text" x-text="result.display_name"></span>
                        </button>
                    </template>
                </div>
            </div>
        @endif

        {{-- Map Container --}}
        <div class="pinpoint-map-border" style="position: relative; border-radius: 8px; border-width: 1px; border-style: solid; overflow: clip;">
            <div
                x-ref="map"
                style="height: {{ $height }}px; width: 100%;"
                class="pinpoint-map-bg"
            >
                <div x-show="!isMapLoaded" style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <div style="display: flex; align-items: center; gap: 8px;" class="pinpoint-muted">
                        <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('filament-pinpoint::pinpoint.loading_map') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Helper Text --}}
        @if ($isDraggable)
            <p style="font-size: 12px; margin-top: 8px; display: flex; align-items: center; gap: 6px;" class="pinpoint-muted">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span>{{ __('filament-pinpoint::pinpoint.instructions') }}</span>
            </p>
        @endif
        @if ($radiusField && !$isReadOnly)
            <p style="font-size: 12px; margin-top: 4px; display: flex; align-items: center; gap: 6px;" class="pinpoint-muted">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span>{{ __('filament-pinpoint::pinpoint.radius_instructions') }}</span>
            </p>
        @endif

        {{-- Get Current Location Button --}}
        @if (!$isReadOnly)
        <button
            type="button"
            x-show="isMapLoaded"
            @click="getCurrentLocation()"
            class="pinpoint-location-btn"
            title="{{ __('filament-pinpoint::pinpoint.use_my_location') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px; flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            <span>{{ __('filament-pinpoint::pinpoint.use_my_location') }}</span>
        </button>
        @endif
    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* Leaflet map z-index fix inside Filament modals */
        .fi-fo-pinpoint .leaflet-pane,
        .fi-fo-pinpoint .leaflet-control {
            z-index: 1 !important;
        }
        .fi-fo-pinpoint .leaflet-top,
        .fi-fo-pinpoint .leaflet-bottom {
            z-index: 2 !important;
        }

        /* Radius drag handle */
        .pinpoint-radius-handle div {
            width: 14px;
            height: 14px;
            background: white;
            border: 2px solid var(--primary-500, #3b82f6);
            border-radius: 50%;
            cursor: ew-resize;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        /* Theme-aware utility classes (Tailwind classes don't compile in plugin views) */
        .fi-fo-pinpoint .pinpoint-map-border { border-color: var(--gray-300, #d1d5db); }
        .dark .fi-fo-pinpoint .pinpoint-map-border { border-color: var(--gray-700, #374151); }
        .fi-fo-pinpoint .pinpoint-map-bg { background-color: var(--gray-100, #f3f4f6); }
        .dark .fi-fo-pinpoint .pinpoint-map-bg { background-color: var(--gray-800, #1f2937); }
        .fi-fo-pinpoint .pinpoint-muted { color: var(--gray-500, #6b7280); }
        .dark .fi-fo-pinpoint .pinpoint-muted { color: var(--gray-400, #9ca3af); }
        .fi-fo-pinpoint .pinpoint-icon { color: var(--gray-400, #9ca3af); }
        .dark .fi-fo-pinpoint .pinpoint-icon { color: var(--gray-500, #6b7280); }

        /* Search Input */
        .fi-fo-pinpoint .pinpoint-search-input {
            display: block;
            width: 100%;
            padding: 10px 16px 10px 40px;
            font-size: 14px;
            border-radius: 8px;
            outline: none;
            background-color: white;
            border: 1px solid var(--gray-300, #d1d5db);
            color: var(--gray-900, #111827);
        }
        .fi-fo-pinpoint .pinpoint-search-input::placeholder { color: var(--gray-400, #9ca3af); }
        .fi-fo-pinpoint .pinpoint-search-input:focus {
            border-color: var(--primary-500, #3b82f6);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary-500, #3b82f6) 20%, transparent);
        }
        .dark .fi-fo-pinpoint .pinpoint-search-input {
            background-color: var(--gray-800, #1f2937);
            border-color: var(--gray-700, #374151);
            color: white;
        }
        .dark .fi-fo-pinpoint .pinpoint-search-input::placeholder { color: var(--gray-500, #6b7280); }

        /* Search Dropdown */
        .fi-fo-pinpoint .pinpoint-search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            border-radius: 8px;
            max-height: 240px;
            overflow-y: auto;
            z-index: 9999;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.18);
            background-color: white;
            border: 1px solid var(--gray-200, #e5e7eb);
        }
        .dark .fi-fo-pinpoint .pinpoint-search-dropdown {
            background-color: var(--gray-800, #1f2937);
            border-color: var(--gray-600, #4b5563);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.4);
        }

        /* Search Dropdown Items */
        .fi-fo-pinpoint .pinpoint-search-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            width: 100%;
            padding: 10px 14px;
            text-align: left;
            border: none;
            cursor: pointer;
            background-color: white;
        }
        .fi-fo-pinpoint .pinpoint-search-item:hover { background-color: var(--gray-100, #f3f4f6); }
        .dark .fi-fo-pinpoint .pinpoint-search-item { background-color: var(--gray-800, #1f2937); }
        .dark .fi-fo-pinpoint .pinpoint-search-item:hover { background-color: var(--gray-700, #374151); }

        .fi-fo-pinpoint .pinpoint-search-item-text {
            font-size: 13px;
            line-height: 1.4;
            color: var(--gray-800, #1f2937);
        }
        .dark .fi-fo-pinpoint .pinpoint-search-item-text { color: var(--gray-200, #e5e7eb); }

        /* Use My Location Button */
        .fi-fo-pinpoint .pinpoint-location-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 8px 14px;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.25rem;
            color: var(--gray-700, #374151);
            background-color: white;
            border: 1px solid var(--gray-300, #d1d5db);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        }
        .fi-fo-pinpoint .pinpoint-location-btn:hover {
            background-color: var(--gray-100, #f3f4f6);
            border-color: var(--gray-400, #9ca3af);
            color: var(--gray-900, #111827);
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1);
        }
        .fi-fo-pinpoint .pinpoint-location-btn:active  { transform: scale(0.98); background-color: var(--gray-200, #e5e7eb); }
        .fi-fo-pinpoint .pinpoint-location-btn:focus   { outline: 2px solid var(--gray-500, #6b7280); outline-offset: 2px; }
        .dark .fi-fo-pinpoint .pinpoint-location-btn   { color: var(--gray-200, #e5e7eb); background-color: var(--gray-700, #374151); border-color: var(--gray-600, #4b5563); }
        .dark .fi-fo-pinpoint .pinpoint-location-btn:hover { background-color: var(--gray-600, #4b5563); border-color: var(--gray-500, #6b7280); color: var(--gray-50, #f9fafb); }
    </style>
</x-dynamic-component>
