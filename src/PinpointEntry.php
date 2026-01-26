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

    protected float|Closure|null $defaultLat = null;

    protected float|Closure|null $defaultLng = null;

    protected int|Closure|null $defaultZoom = null;

    protected int|Closure|null $height = null;

    protected string|Closure|null $latField = 'lat';

    protected string|Closure|null $lngField = 'lng';

    protected string|Closure|null $radiusField = null;

    protected array|Closure|null $pins = null;

    protected bool|Closure $fitBounds = true;

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

    public function radiusField(string|Closure|null $field): static
    {
        $this->radiusField = $field;

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

    public function getRadiusField(): ?string
    {
        return $this->evaluate($this->radiusField);
    }

    public function pins(array|Closure $pins): static
    {
        $this->pins = $pins;

        return $this;
    }

    public function fitBounds(bool|Closure $fit = true): static
    {
        $this->fitBounds = $fit;

        return $this;
    }

    public function getPins(): ?array
    {
        return $this->evaluate($this->pins);
    }

    public function hasPins(): bool
    {
        $pins = $this->getPins();

        return is_array($pins) && count($pins) > 0;
    }

    public function getFitBounds(): bool
    {
        return $this->evaluate($this->fitBounds);
    }

    public function getLat(): ?float
    {
        $record = $this->getRecord();
        $latField = $this->getLatField();
        
        if (!$record || !$latField) {
            return $this->getDefaultLat();
        }

        return data_get($record, $latField) ?? $this->getDefaultLat();
    }

    public function getLng(): ?float
    {
        $record = $this->getRecord();
        $lngField = $this->getLngField();
        
        if (!$record || !$lngField) {
            return $this->getDefaultLng();
        }

        return data_get($record, $lngField) ?? $this->getDefaultLng();
    }

    public function getRadius(): ?int
    {
        $record = $this->getRecord();
        $radiusField = $this->getRadiusField();

        if (!$record || !$radiusField) {
            return null;
        }

        return data_get($record, $radiusField);
    }

    public function getApiKey(): ?string
    {
        return config('filament-pinpoint.api_key');
    }
}

