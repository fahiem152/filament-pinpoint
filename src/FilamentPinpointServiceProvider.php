<?php

namespace Fahiem\FilamentPinpoint;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPinpointServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-pinpoint';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }
}
