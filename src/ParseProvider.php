<?php

namespace Parsequent;

use Illuminate\Support\ServiceProvider;

class ParseProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Parsequent\Parse');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/parsequent.php' => config_path('parsequent.php'),
        ]);
    }
}
