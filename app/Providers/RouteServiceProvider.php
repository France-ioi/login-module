<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->remapPassportRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('user_api')
             ->middleware('auth:api')
             ->namespace($this->namespace)
             ->group(base_path('routes/user_api.php'));

        Route::prefix('platform_api')
             ->namespace($this->namespace)
             ->group(base_path('routes/platform_api.php'));
    }

    /**
     * Remap authorization routes with profile_completed middleware
     *
     * @return void
     */
    protected function remapPassportRoutes() {
        Route::middleware(['auth.auto_login_token', 'web', 'auth', 'authorization_available'])
             ->namespace('\Laravel\Passport\Http\Controllers')
             ->group(base_path('routes/passport.php'));
    }

}