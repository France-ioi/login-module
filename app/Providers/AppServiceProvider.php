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
        Validator::extend('value_different', function ($attribute, $value, $parameters, $validator) {
            $other = array_get($validator->getData(), $parameters[0]);
            return $value !== $other;
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
    }
}
