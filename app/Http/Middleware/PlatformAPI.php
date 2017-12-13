<?php

namespace App\Http\Middleware;

use Closure;
use App\Client;

class PlatformAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->has('client_id')) {
            return response('client_id missed', 400);
        }
        if(!($client = Client::find($request->get('client_id')))) {
            return response('client not found', 400);
        }
        $data = $this->decode($request->get('data'), $client->secret);
        $data['secret'] = $client->secret;
        $request->merge($data);
        return $next($request);
    }


    private function decode($data, $secret) {
        $data = openssl_decrypt($data, 'AES-128-ECB', $secret);
        $data = json_decode($data, true);
        return $data;
    }
}
