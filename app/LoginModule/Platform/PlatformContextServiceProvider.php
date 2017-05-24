<?php

namespace App\LoginModule\Platform;

use Illuminate\Support\ServiceProvider;

class PlatformContextServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //dd($this->app['session']->all());
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PlatformContext::class, function($app) {
            return new PlatformContext(
                new PlatformContextState()
            );
        });
    }
}
