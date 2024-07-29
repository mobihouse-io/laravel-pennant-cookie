<?php

use Bastuijnman\LaravelPennantCookie\Driver\CookieFeatureDriver;

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
