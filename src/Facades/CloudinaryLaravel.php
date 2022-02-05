<?php

namespace Tasmim\CloudinaryLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tasmim\CloudinaryLaravel\CloudinaryLaravel
 */
class CloudinaryLaravel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cloudinary-laravel';
    }
}
