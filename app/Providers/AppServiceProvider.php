<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('login', function ($attribute, $value, $parameters, $validator) {
            return preg_match(config('profile.login_validator.new'), $value) == 1;
        });
        Validator::extend('value_different', function ($attribute, $value, $parameters, $validator) {
            $other = array_get($validator->getData(), $parameters[0]);
            return $value ? $value !== $other : true;
        });
        Validator::replacer('value_different', function ($message, $attribute, $rule, $parameters) {
            $other = str_replace('_', ' ', $parameters[0]);
            return str_replace([':other'], $other, $message);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
        }
    }
}
