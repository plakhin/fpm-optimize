<?php

namespace Plakhin\FpmOptimize\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Plakhin\FpmOptimize\FpmOptimizeServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            FpmOptimizeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('fpm-optimize', require __DIR__.'/../config/fpm-optimize.php');
    }
}
