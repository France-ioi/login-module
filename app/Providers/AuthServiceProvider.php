<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensCan([
            'account' => 'account'
        ]);

        $this->app['auth']->provider('login_module_user_provider', function($app) {
            return new \App\LoginModule\AuthProviders\UserProvider(
                $app['hash'],
                $app['config']['auth.providers.users.model']
            );
        });

        $this->app['auth']->provider('login_module_email_provider', function($app) {
            return new \App\LoginModule\AuthProviders\EmailProvider(
                $app['hash'],
                $app['config']['auth.providers.emails.model']
            );
        });
    }
}
