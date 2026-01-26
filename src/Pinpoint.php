<?php

namespace Fahiem\FilamentPinpoint;

use Closure;
use Filament\Forms\Components\Field;

/**
 * Pinpoint - Google Maps Location Picker for Filament 4
 *
 * A custom Filament form field that provides an interactive Google Maps picker
 * with search functionality, draggable markers, and reverse geocoding.
 *
 * Features:
 * - Search location using Google Places Autocomplete
 * - Click on map to set marker
 * - Drag marker to adjust location
 * - Get current device location
 * - Auto-fill address fields via reverse geocoding
 *
 * @author Fahiem
 * @version 1.0.0
 * @license MIT
 */
class Pinpoint extends Field
{
    protected string $view = 'filament-pinpoint::pinpoint';

    protected float|Closure|null $defaultLat = null;

    protected float|Closure|null $defaultLng = null;

    protected int|Closure|null $defaultZoom = null;

    protected int|Closure|null $height = null;

    protected int|Closure|null $defaultRadius = null;

    protected string|Closure|null $latField = 'lat';

    protected string|Closure|null $lngField = 'lng';
    
    protected string|Closure|null $radiusField = null;

    protected string|Closure|null $addressField = null;

    protected string|Closure|null $shortAddressField = null;

    protected string|Closure|null $provinceField = null;

    protected string|Closure|null $cityField = null;

    protected string|Closure|null $districtField = null;

    protected string|Closure|null $villageField = null;

    protected string|Closure|null $postalCodeField = null;

    protected string|Closure|null $countryField = null;

    protected bool|Closure $draggable = true;

    protected bool|Closure $searchable = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (Pinpoint $component, $record) {
            if ($record) {
                $latField = $component->getLatField();
                $lngField = $component->getLngField();
                $radiusField = $component->getRadiusField();
                $addressField = $component->getAddressField();

                $state = [
                    'lat' => data_get($record, $latField) ?? $component->getDefaultLat(),
                    'lng' => data_get($record, $lngField) ?? $component->getDefaultLng(),
                ];

                if ($radiusField && ($radius = data_get($record, $radiusField))) {
                    $state['radius'] = $radius;
                }

                // Tambahkan address ke state jika field dikonfigurasi
                if ($addressField && ($address = data_get($record, $addressField))) {
                    $state['address'] = $address;
                }

                $component->state($state);
            }
        });

        $this->dehydrateStateUsing(function ($state) {
            return $state;
        });
    }

    public function defaultLocation(float $lat, float $lng): static
    {
        $this->defaultLat = $lat;
        $this->defaultLng = $lng;

        return $this;
    }

    public function defaultZoom(int $zoom): static
    {
        $this->defaultZoom = $zoom;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function defaultRadius(int|Closure|null $radius): static
    {
        $this->defaultRadius = $radius;

        return $this;
    }

    public function latField(string|Closure|null $field): static
    {
        $this->latField = $field;

        return $this;
    }

    public function lngField(string|Closure|null $field): static
    {
        $this->lngField = $field;

        return $this;
    }

    public function radiusField(string|Closure|null $field): static
    {
        $this->radiusField = $field;

        return $this;
    }

    public function addressField(string|Closure|null $field): static
    {
        $this->addressField = $field;

        return $this;
    }

    public function shortAddressField(string|Closure|null $field): static
    {
        $this->shortAddressField = $field;

        return $this;
    }

    public function provinceField(string|Closure|null $field): static
    {
        $this->provinceField = $field;

        return $this;
    }
    
    public function cityField(string|Closure|null $field): static
    {
        $this->cityField = $field;

        return $this;
    }
    
    public function districtField(string|Closure|null $field): static
    {
        $this->districtField = $field;

        return $this;
    }

    public function villageField(string|Closure|null $field): static
    {
        $this->villageField = $field;

        return $this;
    }

    public function postalCodeField(string|Closure|null $field): static
    {
        $this->postalCodeField = $field;

        return $this;
    }

    public function countryField(string|Closure|null $field): static
    {
        $this->countryField = $field;

        return $this;
    }

    public function draggable(bool|Closure $draggable = true): static
    {
        $this->draggable = $draggable;

        return $this;
    }

    public function searchable(bool|Closure $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getDefaultLat(): float
    {
        return $this->evaluate($this->defaultLat) ?? config('filament-pinpoint.default.lat', -0.5050);
    }

    public function getDefaultLng(): float
    {
        return $this->evaluate($this->defaultLng) ?? config('filament-pinpoint.default.lng', 117.1500);
    }

    public function getDefaultZoom(): int
    {
        return $this->evaluate($this->defaultZoom) ?? config('filament-pinpoint.default.zoom', 13);
    }

    public function getHeight(): int
    {
        return $this->evaluate($this->height) ?? config('filament-pinpoint.default.height', 400);
    }

    public function getDefaultRadius(): ?int
    {
        return $this->evaluate($this->defaultRadius) ?? config('filament-pinpoint.default.radius', 500);
    }

    public function getLatField(): ?string
    {
        return $this->evaluate($this->latField);
    }

    public function getLngField(): ?string
    {
        return $this->evaluate($this->lngField);
    }

    public function getRadiusField(): ?string
    {
        return $this->evaluate($this->radiusField);
    }

    public function getAddressField(): ?string
    {
        return $this->evaluate($this->addressField);
    }

    public function getShortAddressField(): ?string
    {
        return $this->evaluate($this->shortAddressField);
    }

    public function getProvinceField(): ?string
    {
        return $this->evaluate($this->provinceField);
    }

    public function getCityField(): ?string
    {
        return $this->evaluate($this->cityField);
    }
    
    public function getDistrictField(): ?string
    {
        return $this->evaluate($this->districtField);
    }

    public function getVillageField(): ?string
    {
        return $this->evaluate($this->villageField);
    }

    public function getPostalCodeField(): ?string
    {
        return $this->evaluate($this->postalCodeField);
    }

    public function getCountryField(): ?string
    {
        return $this->evaluate($this->countryField);
    }

    public function isDraggable(): bool
    {
        return $this->evaluate($this->draggable);
    }

    public function isSearchable(): bool
    {
        return $this->evaluate($this->searchable);
    }

    public function getApiKey(): ?string
    {
        return config('filament-pinpoint.api_key');
    }
}
