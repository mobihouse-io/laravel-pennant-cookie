<?php

use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Laravel\Pennant\Feature;

use function Orchestra\Testbench\Pest\defineEnvironment;
use function Orchestra\Testbench\Pest\defineRoutes;

defineEnvironment(function (Application $app) {
    config()->set('pennant.stores.cookie', [ 'driver' => 'cookie' ]);
    config()->set('pennant.default', 'cookie');
});

defineRoutes(function (Router $router) {
    $router->get('feature-route', fn () => Feature::value('some-feature'))->middleware('web');
    $router->get('change', function () {
        Feature::activateForEveryone('feature1', 'overridden-value');
        Feature::activateForEveryone('feature2', 'overridden-value');
    })->middleware('web');
});

it('should be able to configure pennant using the cookie driver', function () {
    Feature::define('some-feature', fn () => 'some-feature-value');

    $value = Feature::value('some-feature');
    expect($value)->toBe('some-feature-value');
});

it('should be able to get a value from cookies', function () {
    Feature::define('some-feature', fn () => match (true) {
        true => 'some-feature-value',

        // This value cannot be reached by the feature resolver so we can verify it can be set via cookie
        false => 'some-other-feature-value',
    });

    $cookieKey = 'laravel_pennant_cookie';
    $cookieValue = json_encode([
        'some-feature' => [
            Feature::serializeScope(null) => 'some-other-feature-value'
        ],
    ]);

    $this
        ->withCookie($cookieKey, $cookieValue)
        ->get('/feature-route')
        ->assertSee('some-other-feature-value');
});

it('should return a cookie for a resolved feature', function () {
    Feature::define('some-feature', fn () => 'some-feature-value');

    $cookieKey = 'laravel_pennant_cookie';
    $cookieValue = json_encode([
        'some-feature' => [
            Feature::serializeScope(null) => 'some-feature-value',
        ],
    ]);

    $this
        ->get('/feature-route')
        ->assertSee('some-feature-value')
        ->assertCookie($cookieKey, $cookieValue);
});

it('should be able to set values for all scopes', function () {

    $scope1 = Feature::serializeScope('scope1');
    $scope2 = Feature::serializeScope('scope2');

    $cookieKey = 'laravel_pennant_cookie';
    $cookieValue = json_encode([
        'feature1' => [
            $scope1 => 'feature1-value',
            $scope2 => 'some-other-feature',
        ],
        'feature2' => [
            $scope1 => 'feature2-value',
            $scope2 => 'some-other-feature2',
        ],
    ]);

    $this
        ->withCookie($cookieKey, $cookieValue)
        ->get('/change')
        ->assertCookie($cookieKey, json_encode([
            'feature1' => [
                $scope1 => 'overridden-value',
                $scope2 => 'overridden-value',
            ],
            'feature2' => [
                $scope1 => 'overridden-value',
                $scope2 => 'overridden-value',
            ]
        ]));
});
