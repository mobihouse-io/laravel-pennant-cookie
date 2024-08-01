<?php

namespace Mobihouse\LaravelPennantCookie;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Mobihouse\LaravelPennantCookie\Driver\CookieFeatureDriver;

class LaravelPennantCookieServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Feature::extend('cookie', function (Application $app, array $config) {
            return new CookieFeatureDriver();
        });
    }
}
