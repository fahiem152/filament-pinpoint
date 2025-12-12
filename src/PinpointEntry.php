<?php

namespace Fahiem\FilamentPinpoint;

use Closure;
use Filament\Infolists\Components\Entry;

/**
 * PinpointEntry - Google Maps Location Display for Filament 4 Infolists
 *
 * A custom Filament infolist entry that displays a read-only Google Maps view
 * with a marker showing the location from the record.
 *
 * Features:
 * - Display location on Google Maps (read-only)
 * - Show marker at specified coordinates
 * - Dark mode support
 *
 * @author Fahiem
 * @version 1.0.0
 * @license MIT
 */
class PinpointEntry extends Entry
{
    protected string $view = 'filament-pinpoint::pinpoint-entry';

    protected float|Closure $defaultLat = -0.5050;

    protected float|Closure $defaultLng = 117.1500;

    protected int|Closure $defaultZoom = 13;

    protected int|Closure $height = 400;

    protected string|Closure|null $latField = 'lat';

    protected string|Closure|null $lngField = 'lng';

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

    public function getLat(): ?float
    {
        $record = $this->getRecord();
        $latField = $this->getLatField();
        
        if (!$record || !$latField) {
            return $this->getDefaultLat();
        }

        return $record->{$latField} ?? $this->getDefaultLat();
    }

    public function getLng(): ?float
    {
        $record = $this->getRecord();
        $lngField = $this->getLngField();
        
        if (!$record || !$lngField) {
            return $this->getDefaultLng();
        }

        return $record->{$lngField} ?? $this->getDefaultLng();
    }

    public function getApiKey(): ?string
    {
        return config('filament-pinpoint.api_key');
    }
}

