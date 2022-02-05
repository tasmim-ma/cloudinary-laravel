<?php

namespace Tasmim\CloudinaryLaravel;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
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
        $this->bootCloudinaryDriver();
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

    protected function bootCloudinaryDriver()
    {
        $this->app['config']['filesystems.disks.cloudinary'] = ['driver' => 'cloudinary'];

        Storage::extend(
            'cloudinary',
            function ($app, $config) {
                return new Filesystem(new CloudinaryAdapter(config('cloudinary.cloud_url')));
            }
        );
    }
}
