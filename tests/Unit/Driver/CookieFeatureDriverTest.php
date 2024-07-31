<?php

use Mobihouse\LaravelPennantCookie\Driver\CookieFeatureDriver;
use Illuminate\Support\Facades\Cookie;

it('should be able to instantiate', function () {
    $driver = new CookieFeatureDriver();
    expect($driver)->not->toBeNull();
});

it('should be able to define multiple features', function () {
    $driver = new CookieFeatureDriver();

    $driver->define('feature1', fn () => 'some_value');
    $driver->define('feature2', fn () => 'some_other_value');

    $defined = $driver->defined();

    expect($defined)->toHaveCount(2);
    expect($defined)->toEqualCanonicalizing(['feature1', 'feature2']);
});

it('should queue a cookie value when setting a value', function () {

    Cookie::shouldReceive('queue')->once()->with('feature1:user_id_1', 'some_value', 3600)->andReturn('some_value');

    $driver = new CookieFeatureDriver();

    $driver->define('feature1', fn () => 'some_value');
    $value = $driver->get('feature1', 'user_id_1');

    expect($value)->toEqual('some_value');
});
