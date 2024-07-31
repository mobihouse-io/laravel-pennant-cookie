<?php

use Mobihouse\LaravelPennantCookie\Driver\CookieFeatureDriver;
use Illuminate\Support\Arr;
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

    /*
     * For some reason it's not possible to use the regular Facade testing
     * mechanism. So we just create a manual mock & bind it.
     */
    $mock = Mockery::mock('alias:' . Cookie::class);
    $mock->shouldReceive('get')->andReturn(null);
    $mock->shouldReceive('queue')->once()->with('feature1:user_id_1', 'some_value', 3600)->andReturn('some_value');
    $this->app->instance(Cookie::class, $mock);

    $driver = new CookieFeatureDriver();

    $driver->define('feature1', fn () => 'some_value');
    $value = $driver->get('feature1', 'user_id_1');

    expect($value)->toEqual('some_value');
});

it('should attempt to retrieve a value from a cookie', function () {

    $mock = Mockery::mock('alias:' . Cookie::class);
    $mock->shouldReceive('get')->with('feature1:user_id_1')->andReturn('some_value');
    $mock->shouldReceive('get')->with('feature1:user_id_2')->andReturn('some_other_value');
    $driver = new CookieFeatureDriver();

    $driver->define('feature1', fn () => Arr::random([
        'some_value',
        'some_other_value'
    ]));

    $value = $driver->get('feature1', 'user_id_1');
    expect($value)->toEqual('some_value');

    $value = $driver->get('feature1', 'user_id_2');
    expect($value)->toEqual('some_other_value');
});
