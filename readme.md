# Laravel Config

<a href="https://packagist.org/packages/astatroth/laravel-config"><img src="https://poser.pugx.org/astatroth/laravel-config/d/total.svg" alt="Total Downloads"></a>

An extension of the Config component, it adds the ability to write to configuration files. Compatible with Laravel 5.2.

Based on [daftspunk/laravel-config-writer] (https://github.com/daftspunk/laravel-config-writer)

## Installation

Add Laravel Config to your `composer.json` file:

    "astatroth/laravel-config": "~1.0"
    
and run `composer update` or `composer install`.

Another way is to require the package:

    composer require astatroth/laravel-config
    
Next, add the package service provider to your `app/config.php`:

```php
Astatroth\LaravelConfig\LaravelConfigServiceProvider::class,
```

That's it!

## Usage

Usade is as simple as 
```php
Config::write($key, $value);
```

Have fun! :)
