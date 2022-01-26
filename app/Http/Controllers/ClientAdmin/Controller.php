<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;


class Controller extends BaseController {

    protected $client_id;

    public function __construct()
    {
        $this->middleware(function($request, $next) {
            $this->client_id = $request->route('client_id');
            $user = auth()->user();
            $this->client = $user->clients()->where('client_id', $this->client_id)->first();
            if($this->client && $this->client->pivot->admin) {
                return $next($request);
            }
            abort(403);
        });
    }    
}