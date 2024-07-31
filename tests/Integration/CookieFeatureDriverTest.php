<?php

use Illuminate\Foundation\Application;
use Laravel\Pennant\Feature;

use function Orchestra\Testbench\Pest\defineEnvironment;

defineEnvironment(function (Application $app) {
    config()->set('pennant.stores.cookie', [ 'driver' => 'cookie' ]);
    config()->set('pennant.default', 'cookie');
});

it('should be able to configure pennant using the cookie driver', function () {
    Feature::define('some-feature', fn () => 'some-feature-value');

    $value = Feature::value('some-feature');
    expect($value)->toBe('some-feature-value');
});
