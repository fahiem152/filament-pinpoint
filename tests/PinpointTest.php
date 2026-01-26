<?php

use Fahiem\FilamentPinpoint\Pinpoint;

it('can set and get radius field', function () {
    $component = Pinpoint::make('location')
        ->radiusField('radius_column');

    expect($component->getRadiusField())->toBe('radius_column');
});

it('can set and get default radius', function () {
    $component = Pinpoint::make('location')
        ->defaultRadius(1000);

    expect($component->getDefaultRadius())->toBe(1000);
});

it('can use default radius from config if not set', function () {
    config()->set('filament-pinpoint.default.radius', 800);
    
    $component = Pinpoint::make('location');

    expect($component->getDefaultRadius())->toBe(800);
});

it('can evaluate closure for radius field', function () {
    $component = Pinpoint::make('location')
        ->radiusField(fn () => 'dynamic_radius');

    expect($component->getRadiusField())->toBe('dynamic_radius');
});

it('can evaluate closure for default radius', function () {
    $component = Pinpoint::make('location')
        ->defaultRadius(fn () => 1500);

    expect($component->getDefaultRadius())->toBe(1500);
});
