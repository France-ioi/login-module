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
            'account' => 'Account details' //TODO: replace by localization key?
        ]);

        $this->app['auth']->provider('login_module_user_provider', function($app) {
            return new \App\LoginModuleAuth\UserProvider(
                $app['hash'],
                $app['config']['auth.providers.users.model']
            );
        });

        $this->app['auth']->provider('login_module_email_provider', function($app) {
            return new \App\LoginModuleAuth\EmailProvider(
                $app['hash'],
                $app['config']['auth.providers.emails.model']
            );
        });
    }
}
