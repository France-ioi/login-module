<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\LoginModule\Platform\PlatformContext;


class Controller extends BaseController {

    protected $client_id;

    public function __construct(PlatformContext $context)
    {
        $this->context = $context;
        $this->middleware(function($request, $next) {
            $client_id = $request->route('client_id');
            $this->context->setClientId($client_id);
            $client = $this->context->client();
            if($client) {
                $user = auth()->user();
                $user_client = $user->clients()->where('client_id', $client_id)->firstOrFail();
                if($user_client && $user_client->pivot->admin) {
                    return $next($request);
                }
            }
            abort(403);
        });
    }    
}