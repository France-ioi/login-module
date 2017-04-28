<?php

namespace App\LoginModule\Passwords;

use Illuminate\Support\ServiceProvider;

class PasswordResetServiceProvider extends ServiceProvider{

    protected $defer = true;


    public function register() {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });
    }


    public function provides()
    {
        return ['auth.password', 'auth.password.broker'];
    }
}