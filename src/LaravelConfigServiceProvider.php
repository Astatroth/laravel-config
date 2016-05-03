<?php

namespace Astatroth\LaravelConfig;

use Illuminate\Support\ServiceProvider;

class LaravelConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Astatroth\LaravelConfig\Repository', function($app, $items) {
            $writer = new FileWriter($app['files'], $app['path.config']);

            return new Repository($items, $writer);
        });

        $configItems = app('config')->all();

        $this->app['config'] = $this->app->share(function($app) use ($configItems) {
            return $app->make('Astatroth\LaravelConfig\Repository', $configItems);
        });
    }
}
