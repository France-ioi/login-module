<?php

namespace App\LoginModule\Profile;

use Illuminate\Support\ServiceProvider;
use App\LoginModule\Platform\PlatformContext;


class UserProfileServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->singleton(UserProfile::class, function ($app) {
            return new UserProfile(
                $app->make(PlatformContext::class)
            );
        });
    }

}