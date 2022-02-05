<?php

namespace Tasmim\CloudinaryLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use Tasmim\CloudinaryLaravel\CloudinaryEngine;

class Cloudinary extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CloudinaryEngine::class;
    }
}
