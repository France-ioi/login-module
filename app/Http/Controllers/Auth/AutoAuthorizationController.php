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
            return $this->approveRequest($authRequest, $request->user());
        });
    }

}