<?php

namespace Tasmim\CloudinaryLaravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tasmim\CloudinaryLaravel\CloudinaryServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CloudinaryServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
