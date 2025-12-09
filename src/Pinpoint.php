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

    protected float|Closure $defaultLat = -0.5050;

    protected float|Closure $defaultLng = 117.1500;

    protected int|Closure $defaultZoom = 13;

    protected int|Closure $height = 400;

    protected string|Closure|null $latField = 'lat';

    protected string|Closure|null $lngField = 'lng';

    protected string|Closure|null $addressField = null;

    protected string|Closure|null $provinceField = null;

    protected string|Closure|null $cityField = null;

    protected string|Closure|null $districtField = null;

    protected string|Closure|null $villageField = null;

    protected string|Closure|null $postalCodeField = null;

    protected bool|Closure $draggable = true;

    protected bool|Closure $searchable = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (Pinpoint $component, $record) {
            if ($record) {
                $latField = $component->getLatField();
                $lngField = $component->getLngField();
                $addressField = $component->getAddressField();

                $state = [
                    'lat' => $record->{$latField} ?? $component->getDefaultLat(),
                    'lng' => $record->{$lngField} ?? $component->getDefaultLng(),
                ];

                // Tambahkan address ke state jika field dikonfigurasi
                if ($addressField && isset($record->{$addressField})) {
                    $state['address'] = $record->{$addressField};
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

    public function addressField(string|Closure|null $field): static
    {
        $this->addressField = $field;

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

    public function getLatField(): ?string
    {
        return $this->evaluate($this->latField);
    }

    public function getLngField(): ?string
    {
        return $this->evaluate($this->lngField);
    }

    public function getAddressField(): ?string
    {
        return $this->evaluate($this->addressField);
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
