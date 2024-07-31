<?php

namespace Tests;

use Mobihouse\LaravelPennantCookie\LaravelPennantCookieServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class IntegrationTestCase extends BaseTestCase
{
    use WithWorkbench;

    protected function getPackageProviders($app): array
    {
        return [
            LaravelPennantCookieServiceProvider::class,
        ];
    }
}
