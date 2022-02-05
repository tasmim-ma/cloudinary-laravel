<?php

namespace Tasmim\CloudinaryLaravel;

use Illuminate\Http\UploadedFile;
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

    public function bootingPackage(): void
    {
        $this->bootMacros();
    }

    public function packageRegistered(): void
    {
        $this->app->bind('cloudinary', function () {
            return new CloudinaryEngine();
        });
    }

    /**
     * Boot the package macros that extends Laravel Uploaded File API.
     *
     * @return void
     */
    protected function bootMacros()
    {
        UploadedFile::macro(
            'storeOnCloudinary',
            function ($folder = null) {
                return resolve(CloudinaryEngine::class)->uploadFile($this->getRealPath(), ['folder' => $folder]);
            }
        );

        UploadedFile::macro(
            'storeOnCloudinaryAs',
            function ($folder = null, $publicId = null) {
                return resolve(CloudinaryEngine::class)->uploadFile(
                    $this->getRealPath(),
                    ['folder' => $folder, 'public_id' => $publicId]
                );
            }
        );
    }
}
