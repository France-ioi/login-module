<?php

namespace App\Http\Controllers\Auth;

use Laravel\Passport\Http\Controllers\AuthorizationController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;

// Class to auto-approve some authorizations based on some criteria
// (here, coming from PMS and going to an auto-approved client)
class AutoAuthorizationController extends AuthorizationController {
    public function authorize(ServerRequestInterface $psrRequest,
                              Request $request,
                              ClientRepository $clients,
                              TokenRepository $tokens)
    {
        return $this->withErrorHandling(function () use ($psrRequest, $request, $clients, $tokens) {
            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);

            $scopes = $this->parseScopes($authRequest);

            $token = $tokens->getValidToken(
                $user = $request->user(),
                $client = $clients->find($authRequest->getClient()->getIdentifier())
            );

            if ($token && $token->scopes === collect($scopes)->pluck('id')->all()) {
                return $this->approveRequest($authRequest, $user);
            }

            $request->session()->put(
                'authRequest', $authRequest = $this->server->validateAuthorizationRequest($psrRequest)
            );

            // If coming from PMS and going to an auto-approve client, approve
            // the request automatically
            if($user->auth_connections()->where('provider', 'pms')->where('active', '1')->first()
                    && $client['autoapprove_authorization']) {
                return $this->approveRequest($authRequest, $user);
            }

            return $this->response->view('passport::authorize', [
                'client' => $client,
                'user' => $request->user(),
                'scopes' => $scopes,
                'request' => $request,
            ]);
        });
    }
}
