<p align="center">
    <img alt="Packagist License" src="https://img.shields.io/packagist/l/mobihouse/laravel-pennant-cookie">
    <img alt="Packagist Version" src="https://img.shields.io/packagist/v/mobihouse/laravel-pennant-cookie">
</p>

# Laravel Pennant - Cookie Driver

This package adds the ability to remember feature flags/values
from Laravel pennant in a cookie, which allows you to have
persisted values for anonymous users.

> [!IMPORTANT]
> This driver is most suited for tracking anonymous users, if you
> want to track actual users it will be more beneficial to use
> the database driver.

## Usage

Install the package using:

```bash
composer require mobihouse/laravel-pennant-cookie
```

And configure it by adding a new store to the `config/pennant.php` config file:

```php
return [

    'stores' => [
        
        // ...
        'cookie' => [
            'driver' => 'cookie'
        ]

    ]

]
```

Now that the store is configured set your `PENNANT_STORE` environment variable to `cookie` (or
set the `default` key in the pennant config to `cookie`).

### Scope

Laravel Pennant will try to use the auth scope by default and thus look for a current user.
Though the driver will work with this it isn't it's intended purpose, so the easiest way
to use anonymised scope is setting the default scope to null:

```php
<?php
 
namespace App\Providers;
 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Feature::resolveScopeUsing(fn () => null);
 
        // ...
    }
}

```

### Options

By default the cookie that gets set will live for a year, if you want to change the
default lifetime you can do the following in the config:

```php
return [

    'stores' => [
        'cookie' => [
            'driver' => 'cookie',
            'lifetime' => 3600, // Live for one hour
        ]
    ]

]
```

## Tests

Running the automated tests can be done by

```bash
./vendor/bin/pest
```
