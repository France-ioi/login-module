<?php

namespace App\LoginModule\Profile;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;

class UserProfileServiceProvider extends ServiceProvider
{

    protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserProfileServiceProvider::class, function ($app) {
            return new UserProfileServiceProvider(
                $app->make(PlatformContext::class)
            );
        });
    }
}
