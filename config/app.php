<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => 'Login module',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC', // 'Europe/Paris' ?'

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    'locales' => [
        'en' => 'English',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'sl' => 'Slovenščina',
        'ru' => 'Русский'
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    | Logging is configured in config/logging.php since Laravel 5.6.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->except([
        // replaced by App\LoginModule\Passwords\PasswordResetServiceProvider
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    ])->merge([

        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        // replaces Laravel\Passport\PassportServiceProvider (excluded from package discovery);
        // must boot before RouteServiceProvider, which overrides some Passport routes
        App\Providers\PassportServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\LoginModule\Passwords\PasswordResetServiceProvider::class,
        App\LoginModule\Platform\PlatformContextServiceProvider::class,
        App\LoginModule\Profile\UserProfileServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Form'     => Collective\Html\FormFacade::class,
        'HTML'     => Collective\Html\HtmlFacade::class,
        'BootForm' => Watson\BootstrapForm\Facades\BootstrapForm::class,
        'ProfileFormRenderer' => App\LoginModule\Profile\ProfileFormRenderer::class,
        'EmailMasker' => App\Helpers\EmailMasker::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'PlatformHelper' => App\LoginModule\Platform\PlatformHelper::class,
        'Verification' => App\LoginModule\Profile\Verification\Verification::class,
    ])->toArray(),

];
