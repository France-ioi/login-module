<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\LoginModule\Platform\PlatformContext;
use App\User;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController {

    protected $client_id;

    public function __construct(PlatformContext $context)
    {
        $this->context = $context;
        $this->middleware(function($request, $next) {
            $user = $request->user();            
            if($user) {
                $this->context->setClientId(
                    $request->route('client_id')
                );
                $client = $this->context->client();
                if($client) {
                    $user_client = $user->clients()->where('client_id', $client->id)->firstOrFail();
                    if($user_client && $user_client->pivot->admin) {
                        return $next($request);
                    }
                }
            }
            return redirect('/auth');
        });
    }    


    public function getUser($user_id) {
        $user = User::findOrFail($user_id);
        $user_client_ids = $user->clients->pluck('id');
        $admin_client_ids = Auth::user()->clients()->where('admin', 1)->pluck('id');
        $valid_ids = $user_client_ids->intersect($admin_client_ids);
        if($valid_ids->count() != $user_client_ids->count()) {
            abort(404);
        }
        return $user;
    }    
}