<?php

use Bastuijnman\LaravelPennantCookie\Driver\CookieFeatureDriver;

it('should be able to instantiate', function () {
    $driver = new CookieFeatureDriver();

    expect($driver)->not->toBeNull();
});
