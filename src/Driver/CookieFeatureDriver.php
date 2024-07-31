<?php

namespace Mobihouse\LaravelPennantCookie\Driver;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Laravel\Pennant\Contracts\Driver;
use Laravel\Pennant\Feature;
use Mobihouse\LaravelPennantCookie\Exceptions\NotSupportedException;
use stdClass;

use function Pest\Laravel\withCookie;

class CookieFeatureDriver implements Driver
{
    protected const COOKIE_NAME = 'laravel_pennant_cookie';
    protected ?array $cachedCookieValues = null;

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
        $scopeKey = Feature::serializeScope($scope);
        $result = Arr::get($this->getCookieValues(), sprintf('%s.%s', $feature, $scopeKey));
        if ($result) {
            return $result;
        }

        return with($this->resolveValue($feature, $scope), function ($value) use ($feature, $scopeKey) {
            if ($value === $this->unknownFeatureValue) {
                return false;
            }
            $this->set($feature, $scopeKey, $value);

            return $value;
        });
    }

    /**
     * Get list of values from the cookie & cache them
     * on the instance
     */
    protected function getCookieValues(): array
    {
        if ($this->cachedCookieValues !== null) {
            return $this->cachedCookieValues;
        }

        $values = Cookie::get(self::COOKIE_NAME);
        if ($values !== null) {
            return json_decode($values, true) ?? [];
        }

        return [];
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

        $values = $this->getCookieValues();
        Arr::set($values, sprintf('%s.%s', $feature, $key), $value);
        $this->cachedCookieValues = $values;

        // TODO: resolve cookie length from config
        Cookie::queue(self::COOKIE_NAME, json_encode($values), 3600);
    }

    public function setForAllScopes(string $feature, mixed $value): void
    {
        $values = $this->getCookieValues();

        $values = Collection::make($values)
            ->map(
                fn ($scopes) => Collection::make($scopes)
                ->map(fn () => $value)
                ->all()
            )
            ->all();

        $this->cachedCookieValues = $values;

        // TODO: resolve cookie length from config
        Cookie::queue(self::COOKIE_NAME, json_encode($values), 3600);
    }

    public function delete(string $feature, mixed $scope): void
    {
        // TODO
    }

    /**
     * Purge one or more features from storage
     */
    public function purge(?array $features): void
    {
        if ($features === null) {
            $this->cachedCookieValues = null;
            Cookie::forget(self::COOKIE_NAME);
            return;
        }

        $values = $this->getCookieValues();
        foreach ($features as $feature) {
            Arr::pull($values, $feature);
        }

        $this->cachedCookieValues = $values;
        Cookie::queue(self::COOKIE_NAME, json_encode($values), 3600);
    }
}
