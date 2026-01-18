<?php

use Fahiem\FilamentPinpoint\PinpointEntry;

it('can set and get radius field for entry', function () {
    $entry = PinpointEntry::make('location')
        ->radiusField('radius_column');

    expect($entry->getRadiusField())->toBe('radius_column');
});

it('can get radius value from record in entry', function () {
    $record = ['radius_column' => 300];
    
    $entry = PinpointEntry::make('location')
        ->radiusField('radius_column')
        ->model($record);

    expect($entry->getRadius())->toBe(300);
});

it('returns null radius if field is not configured', function () {
    $record = ['radius' => 300];
    
    $entry = PinpointEntry::make('location')
        ->model($record);

    expect($entry->getRadius())->toBeNull();
});

it('can evaluate closure for radius field in entry', function () {
    $entry = PinpointEntry::make('location')
        ->radiusField(fn () => 'dynamic_radius');

    expect($entry->getRadiusField())->toBe('dynamic_radius');
});
