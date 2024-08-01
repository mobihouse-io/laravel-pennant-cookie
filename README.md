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

## Tests

Running the automated tests can be done by

```bash
./vendor/bin/pest
```
