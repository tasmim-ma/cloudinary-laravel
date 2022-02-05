<?php

namespace Tasmim\CloudinaryLaravel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CloudinaryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cloudinary-laravel')
            ->hasConfigFile('cloudinary');
    }
}
