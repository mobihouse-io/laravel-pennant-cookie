<?php

namespace Mobihouse\LaravelPennantCookie\Driver;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Laravel\Pennant\Contracts\Driver;
use Laravel\Pennant\Feature;
use stdClass;

class CookieFeatureDriver implements Driver
{
    public function __construct(
        protected array $featureStateResolvers = [],
        protected stdClass $unknownFeatureValue = new stdClass()
    ) {
    }

    /**
     * Define a feature on the driver
     */
    public function define(string $feature, callable $resolver): void
    {
        $this->featureStateResolvers[$feature] = $resolver;
    }

    /**
     * Gets a list of all defined features.
     */
    public function defined(): array
    {
        return array_keys($this->featureStateResolvers);
    }

    /**
     * Get values for all provided features/scopes.
     */
    public function getAll(array $features): array
    {
        return Collection::make($features)
            ->map(
                fn ($scopes, $feature) => Collection::make($scopes)
                ->map(fn ($scope) => $this->get($feature, $scope))
                ->all()
            )
            ->all();
    }

    /**
     * Get value for specific feature/scope.
    */
    public function get(string $feature, mixed $scope): mixed
    {
        $key = Feature::serializeScope($scope);
        if ($result = Cookie::get(sprintf('%s:%s', $feature, $key))) {
            return $result;
        }

        return with($this->resolveValue($feature, $scope), function ($value) use ($feature, $key) {
            if ($value === $this->unknownFeatureValue) {
                return false;
            }
            $this->set($feature, $key, $value);

            return $value;
        });
    }

    /**
     * Resolve the value of a feature based on a scope.
     */
    protected function resolveValue(string $feature, mixed $scope): mixed
    {
        if (!array_key_exists($feature, $this->featureStateResolvers)) {
            return $this->unknownFeatureValue;
        }
        return $this->featureStateResolvers[$feature]($scope);
    }

    /**
     * Set the value of a feature for a scope.
     */
    public function set(string $feature, mixed $scope, mixed $value): void
    {
        $key = Feature::serializeScope($scope);

        // TODO: resolve cookie lenght from config
        Cookie::queue(sprintf('%s:%s', $feature, $key), $value, 3600);
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
