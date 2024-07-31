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
    $router->get('feature-route', fn () => Feature::value('some-feature'));
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

    $cookieKey = sprintf('%s:%s', 'some-feature', Feature::serializeScope(null));

    $this
        ->withUnencryptedCookie($cookieKey, 'some-other-feature-value')
        ->get('/feature-route')
        ->assertSee('some-other-feature-value');
});
