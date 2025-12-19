{{--
    Pinpoint - Google Maps Location Picker for Filament 4

    A custom Filament form field with Google Maps integration.
    Features: Search, draggable marker, reverse geocoding, current location.

    @author Fahiem
    @version 1.0.0
    @package fahiem/filament-pinpoint
--}}
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $statePath = $getStatePath();
        $defaultLat = $getDefaultLat();
        $defaultLng = $getDefaultLng();
        $defaultZoom = $getDefaultZoom();
        $height = $getHeight();
        $isDraggable = $isDraggable();
        $isSearchable = $isSearchable();
        $latField = $getLatField();
        $lngField = $getLngField();
        $addressField = $getAddressField();
        $shortAddressField = $getShortAddressField();
        $provinceField = $getProvinceField();
        $villageField = $getVillageField();
        $cityField = $getCityField();
        $districtField = $getDistrictField();
        $postalCodeField = $getPostalCodeField();
        $countryField = $getCountryField();
        $apiKey = $getApiKey();

        $state = $getState();
        $currentLat = $state['lat'] ?? $defaultLat;
        $currentLng = $state['lng'] ?? $defaultLng;
        $currentAddress = $state['address'] ?? '';
    @endphp

    <div
        wire:ignore
        x-data="{
            map: null,
            marker: null,
            searchBox: null,
            lat: parseFloat(@js($currentLat)) || @js($defaultLat),
            lng: parseFloat(@js($currentLng)) || @js($defaultLng),
            address: @js($currentAddress),
            defaultLat: @js($defaultLat),
            defaultLng: @js($defaultLng),
            defaultZoom: @js($defaultZoom),
            isDraggable: @js($isDraggable),
            isSearchable: @js($isSearchable),
            statePath: @js($statePath),
            latField: @js($latField),
            lngField: @js($lngField),
            addressField: @js($addressField),
            shortAddressField: @js($shortAddressField),
            provinceField: @js($provinceField),
            cityField: @js($cityField),
            districtField: @js($districtField),
            postalCodeField: @js($postalCodeField),
            countryField: @js($countryField),
            villageField: @js($villageField),
            isMapLoaded: false,

            getFieldPath(fieldName) {
                if (!fieldName) return null;
                // Get parent path from statePath (e.g., 'data.items.0.location' -> 'data.items.0')
                const lastDotIndex = this.statePath.lastIndexOf('.');
                const basePath = lastDotIndex > -1 ? this.statePath.substring(0, lastDotIndex + 1) : 'data.';
                return basePath + fieldName;
            },

            init() {
                // Try to read existing lat/lng from sibling fields (for edit mode / Repeater support)
                this.loadExistingCoordinates();
                this.loadGoogleMaps();
            },

            loadExistingCoordinates() {
                const latPath = this.getFieldPath(this.latField);
                const lngPath = this.getFieldPath(this.lngField);
                const addressPath = this.getFieldPath(this.addressField);

                if (latPath && lngPath) {
                    const existingLat = $wire.get(latPath);
                    const existingLng = $wire.get(lngPath);

                    if (existingLat && existingLng) {
                        this.lat = parseFloat(existingLat);
                        this.lng = parseFloat(existingLng);
                    }
                }

                if (addressPath) {
                    const existingAddress = $wire.get(addressPath);
                    if (existingAddress) {
                        this.address = existingAddress;
                    }
                }
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
                    mapTypeControl: true,
                    streetViewControl: false,
                    fullscreenControl: true,
                });

                this.marker = new google.maps.Marker({
                    position: { lat: this.lat, lng: this.lng },
                    map: this.map,
                    draggable: this.isDraggable,
                    animation: google.maps.Animation.DROP,
                });

                if (this.isDraggable) {
                    this.marker.addListener('dragend', (event) => {
                        this.updatePosition(event.latLng.lat(), event.latLng.lng());
                    });
                }

                this.map.addListener('click', (event) => {
                    this.marker.setPosition(event.latLng);
                    this.updatePosition(event.latLng.lat(), event.latLng.lng());
                });

                if (this.isSearchable) {
                    this.initSearchBox();
                }

                this.isMapLoaded = true;
            },

            initSearchBox() {
                const input = this.$refs.searchInput;
                if (!input) return;

                this.searchBox = new google.maps.places.SearchBox(input);

                this.map.addListener('bounds_changed', () => {
                    this.searchBox.setBounds(this.map.getBounds());
                });

                this.searchBox.addListener('places_changed', () => {
                    const places = this.searchBox.getPlaces();
                    if (places.length === 0) return;

                    const place = places[0];
                    if (!place.geometry || !place.geometry.location) return;

                    const location = place.geometry.location;
                    this.marker.setPosition(location);
                    this.map.setCenter(location);
                    this.map.setZoom(17);

                    this.updatePosition(location.lat(), location.lng());
                });
            },

            updatePosition(lat, lng) {
                this.lat = parseFloat(lat.toFixed(7));
                this.lng = parseFloat(lng.toFixed(7));

                // Set ke form data Filament using dynamic path (supports Repeater)
                const latPath = this.getFieldPath(this.latField);
                const lngPath = this.getFieldPath(this.lngField);

                if (latPath) {
                    $wire.set(latPath, this.lat);
                }
                if (lngPath) {
                    $wire.set(lngPath, this.lng);
                }

                // Reverse geocoding to get address
                this.reverseGeocode(lat, lng);
            },

            reverseGeocode(lat, lng) {
                const geocoder = new google.maps.Geocoder();
                const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

                geocoder.geocode({ location: latlng }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const address = results[0].formatted_address;

                        // Update address variable (for x-model)
                        this.address = address;

                        // Update field address if set (supports Repeater)
                        const addressPath = this.getFieldPath(this.addressField);
                        if (addressPath) {
                            $wire.set(addressPath, address);
                        }

                        // Extract address components
                        const components = results[0].address_components;
                        let subpremise = '';
                        let premise = '';
                        let streetNumber = '';
                        let route = '';
                        let province = '';
                        let city = '';
                        let district = '';
                        let village = '';
                        let postalCode = '';
                        let country = '';

                        components.forEach(component => {
                            // Street Number
                            if (component.types.includes('street_number')) {
                                streetNumber = component.long_name;
                            }

                            // Subpremise
                            if (component.types.includes('subpremise')) {
                                subpremise = component.long_name;
                            }

                            // Premise
                            if (component.types.includes('premise')) {
                                premise = component.long_name;
                            }

                            // Route
                            if (component.types.includes('route')) {
                                route = component.long_name;
                            }

                            // Province
                            if (component.types.includes('administrative_area_level_1')) {
                                province = component.long_name;
                            }

                            // City/County
                            if (component.types.includes('administrative_area_level_2')) {
                                city = component.long_name;
                            }

                            // District
                            if (component.types.includes('administrative_area_level_3')) {
                                district = component.long_name;
                            }

                            // Villages/Sub-districts are usually at administrative_area_level_4 or sublocality
                            if (component.types.includes('administrative_area_level_4') ||
                                component.types.includes('sublocality_level_1')) {
                                village = component.long_name.replace(/^(Desa|Kelurahan)\s*/i, '');
                            }

                            // Postal code/Zip code
                            if (component.types.includes('postal_code')) {
                                postalCode = component.long_name;
                            }
                            
                            // Country
                            if (component.types.includes('country')) {
                                country = component.long_name;
                            }
                        });

                        // Build short address
                        let shortAddress = '';
                        if (subpremise) {
                            shortAddress += subpremise + ', ';
                        }
                        if (premise) {
                            shortAddress += premise + ', ';
                        }
                        if (route && streetNumber) {
                            shortAddress += `${route} ${streetNumber}`;
                        } else if (route) {
                            shortAddress += route;
                        } else if (streetNumber) {
                            shortAddress += streetNumber;
                        }

                        // Update short address (supports Repeater)
                        const shortAddressPath = this.getFieldPath(this.shortAddressField);
                        if (shortAddressPath) {
                            $wire.set(shortAddressPath, shortAddress || null);
                        }

                        // Update province (supports Repeater)
                        const provincePath = this.getFieldPath(this.provinceField);
                        if (provincePath) {
                            $wire.set(provincePath, province || null);
                        }

                        // Update city (supports Repeater)
                        const cityPath = this.getFieldPath(this.cityField);
                        if (cityPath) {
                            $wire.set(cityPath, city || null);
                        }

                        // Update district (supports Repeater)
                        const districtPath = this.getFieldPath(this.districtField);
                        if (districtPath) {
                            $wire.set(districtPath, district || null);
                        }

                        // Update village (supports Repeater)
                        const villagePath = this.getFieldPath(this.villageField);
                        if (villagePath) {
                            $wire.set(villagePath, village || null);
                        }

                        // Update postalCode (supports Repeater)
                        const postalCodePath = this.getFieldPath(this.postalCodeField);
                        if (postalCodePath) {
                            $wire.set(postalCodePath, postalCode || null);
                        }

                        // Update country (supports Repeater)
                        const countryPath = this.getFieldPath(this.countryField);
                        if (countryPath) {
                            $wire.set(countryPath, country || null);
                        }
                    }
                });
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
                        const location = new google.maps.LatLng(lat, lng);

                        this.marker.setPosition(location);
                        this.map.setCenter(location);
                        this.map.setZoom(17);
                        this.updatePosition(lat, lng);
                    },
                    (error) => {
                        console.error('Error getting location:', error);
                        alert('Failed to get location: ' + error.message);
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }
        }"
        x-init="init()"
        class="fi-fo-pinpoint"
    >
        {{-- Search Box --}}
        @if ($isSearchable)
            <div style="position: relative; margin-bottom: 12px;">
                <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;" class="text-gray-400 dark:text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input
                    type="text"
                    x-ref="searchInput"
                    x-model="address"
                    placeholder="{{ __('filament-pinpoint::pinpoint.search') }}"
                    style="display: block; width: 100%; padding: 10px 16px 10px 40px; font-size: 14px; border-radius: 8px; outline: none; border: 1px solid #d1d5db;"
                    class="bg-white dark:bg-gray-900 dark:!border-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:!border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                />
            </div>
        @endif

        {{-- Map Container --}}
        <div class="relative rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700">
            <div
                x-ref="map"
                style="height: {{ $height }}px; width: 100%;"
                class="bg-gray-100 dark:bg-gray-800"
            >
                <div x-show="!isMapLoaded" style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <div style="display: flex; align-items: center; gap: 8px;" class="text-gray-500 dark:text-gray-400">
                        <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('filament-pinpoint::pinpoint.loading_map') }}</span>
                    </div>
                </div>
            </div>

            {{-- Get Current Location Button --}}
            <button
                type="button"
                x-on:click="getCurrentLocation()"
                x-show="isMapLoaded"
                style="position: absolute; bottom: 75px; right: 10px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 10px; border: none; cursor: pointer;"
                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                title="{{ __('filament-pinpoint::pinpoint.use_my_location') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 21px;" class="text-primary-600 dark:text-primary-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </button>
        </div>

        {{-- Helper Text --}}
        @if ($isDraggable)
            <p style="font-size: 12px; margin-top: 8px; display: flex; align-items: center; gap: 6px;" class="text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span>{{ __('filament-pinpoint::pinpoint.instructions') }}</span>
            </p>
        @endif

    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</x-dynamic-component>
