<?php

namespace Bastuijnman\LaravelPennantCookie\Driver;

use Laravel\Pennant\Contracts\Driver;

class CookieFeatureDriver implements Driver
{
    public function define(string $feature, callable $resolver): void
    {
        // TODO
    }

    public function defined(): array
    {
        // TODO
        return [];
    }

    public function getAll(array $features): array
    {
        return [];
    }

    public function get(string $feature, mixed $scope): mixed
    {
        // TODO
        return null;
    }

    public function set(string $feature, mixed $scope, mixed $value): void
    {
        // TODO
    }

    public function setForAllScopes(string $feature, mixed $value): void
    {
        // TODO
    }

    public function delete(string $feature, mixed $scope): void
    {
        // TODO
    }

    public function purge(?array $features): void
    {
        // TODO
    }
}
