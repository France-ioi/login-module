<?php

namespace App\LoginModule\Platform;

use Illuminate\Support\ServiceProvider;

class PlatformContextServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->singleton(PlatformContext::class, function($app) {
            return new PlatformContext(
                new PlatformContextState
            );
        });
    }

}
