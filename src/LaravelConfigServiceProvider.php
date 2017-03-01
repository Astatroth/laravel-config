<?php

namespace Astatroth\LaravelConfig;

use Illuminate\Support\ServiceProvider;

class LaravelConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configItems = app('config')->all();

        $this->app->bind('config', function ($app) use ($configItems) {
            $writer = new FileWriter($app['files'], $app['path.config']);

            return new Repository($configItems, $writer);
        });
    }
}