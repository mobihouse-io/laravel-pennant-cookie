<?php

use Mobihouse\LaravelPennantCookie\Driver\CookieFeatureDriver;
use Illuminate\Support\Facades\Cookie;
use Laravel\Pennant\Feature;

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

    $feature = 'feature1';
    $scope = 'user_id_1';
    $expectation = [
        'feature1' => [
            Feature::serializeScope($scope) => 'some_value',
        ],
    ];

    // Ensure we set the cookie for a year by default
    Cookie::shouldReceive('queue')->once()->with('laravel_pennant_cookie', json_encode($expectation), 525600)->andReturn('some_value');

    $driver = new CookieFeatureDriver();

    $driver->define($feature, fn () => 'some_value');
    $value = $driver->get($feature, $scope);

    expect($value)->toEqual('some_value');
});

it('should queue a cookie with a specific lifetime when configuring', function () {
    $driver = new CookieFeatureDriver(config: [

        // Cookie lifetime of two weeks
        'lifetime' => 20160,
    ]);


    $feature = 'feature1';
    $scope = 'user_id_1';
    $expectation = [
        'feature1' => [
            Feature::serializeScope($scope) => 'some_value',
        ],
    ];
    Cookie::shouldReceive('queue')->once()->with('laravel_pennant_cookie', json_encode($expectation), 20160)->andReturn('some_value');

    $driver->define($feature, fn () => 'some_value');
    $driver->get($feature, $scope);
});
