<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Platform\PlatformContext;

class PlatformRequest
{

    private $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->context->request($request);
        return $next($request);
    }
}
