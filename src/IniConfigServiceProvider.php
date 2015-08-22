<?php

namespace HazeDevelopment;

use Illuminate\Support\ServiceProvider;

class IniConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/iniconfig.php' => config_path('iniconfig.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
