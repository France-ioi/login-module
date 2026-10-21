<?php

namespace App\OAuth;

use DateInterval;
use LogicException;
use League\OAuth2\Server\CodeChallengeVerifiers\PlainVerifier;
use League\OAuth2\Server\CodeChallengeVerifiers\S256Verifier;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant as BaseAuthCodeGrant;
use League\OAuth2\Server\RequestAccessTokenEvent;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\RequestRefreshTokenEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Authorization code grant allowing regexps in the clients redirect URIs:
 * a redirect URI starting with ^ is matched as a regexp, others must match exactly.
 *
 * When exchanging the code, the redirect_uri may differ from the one used for
 * the authorization request, as long as both are valid for the client.
 */
class AuthCodeGrant extends BaseAuthCodeGrant
{

    protected function validateRedirectUri(
        string $redirectUri,
        ClientEntityInterface $client,
        ServerRequestInterface $request
    ) {
        if (!$this->matchesClientRedirectUri($redirectUri, $client)) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidClient($request);
        }
    }


    protected function matchesClientRedirectUri($uri, ClientEntityInterface $client)
    {
        if (!is_string($uri)) {
            return false;
        }
        $clientRedirectUri = $client->getRedirectUri();
        if (is_string($clientRedirectUri)) {
            $clientRedirectUri = [$clientRedirectUri];
        }
        if (is_array($clientRedirectUri)) {
            foreach ($clientRedirectUri as $pattern) {
                $pattern = trim($pattern);
                if(substr($pattern, 0, 1) === '^') {
                    if (@preg_match('/' . $pattern . '/', $uri) === 1) {
                        return true;
                    }
                } elseif (strcmp($pattern, $uri) === 0) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Same as the parent implementation (league/oauth2-server 8.5), except for
     * the redirect_uri check in validateAuthorizationCode().
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ) {
        list($clientId) = $this->getClientCredentials($request);

        $client = $this->getClientEntityOrFail($clientId, $request);

        // Only validate the client if it is confidential
        if ($client->isConfidential()) {
            $this->validateClient($request);
        }

        $encryptedAuthCode = $this->getRequestParameter('code', $request, null);

        if (!is_string($encryptedAuthCode)) {
            throw OAuthServerException::invalidRequest('code');
        }

        try {
            $authCodePayload = json_decode($this->decrypt($encryptedAuthCode));

            $this->validateAuthorizationCode($authCodePayload, $client, $request);

            $scopes = $this->scopeRepository->finalizeScopes(
                $this->validateScopes($authCodePayload->scopes),
                $this->getIdentifier(),
                $client,
                $authCodePayload->user_id
            );
        } catch (LogicException $e) {
            throw OAuthServerException::invalidRequest('code', 'Cannot decrypt the authorization code', $e);
        }

        $codeVerifier = $this->getRequestParameter('code_verifier', $request, null);

        // If a code challenge isn't present but a code verifier is, reject the request to block PKCE downgrade attack
        if (empty($authCodePayload->code_challenge) && $codeVerifier !== null) {
            throw OAuthServerException::invalidRequest(
                'code_challenge',
                'code_verifier received when no code_challenge is present'
            );
        }

        if (!empty($authCodePayload->code_challenge)) {
            $this->validateCodeChallenge($authCodePayload, $codeVerifier);
        }

        // Issue and persist new access token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $authCodePayload->user_id, $scopes);
        $this->getEmitter()->emit(new RequestAccessTokenEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request, $accessToken));
        $responseType->setAccessToken($accessToken);

        // Issue and persist new refresh token if given
        $refreshToken = $this->issueRefreshToken($accessToken);

        if ($refreshToken !== null) {
            $this->getEmitter()->emit(new RequestRefreshTokenEvent(RequestEvent::REFRESH_TOKEN_ISSUED, $request, $refreshToken));
            $responseType->setRefreshToken($refreshToken);
        }

        // Revoke used auth code
        $this->authCodeRepository->revokeAuthCode($authCodePayload->auth_code_id);

        return $responseType;
    }


    private function validateAuthorizationCode(
        $authCodePayload,
        ClientEntityInterface $client,
        ServerRequestInterface $request
    ) {
        if (!is_object($authCodePayload) || !property_exists($authCodePayload, 'auth_code_id')) {
            throw OAuthServerException::invalidRequest('code', 'Authorization code malformed');
        }

        if (time() > $authCodePayload->expire_time) {
            throw OAuthServerException::invalidRequest('code', 'Authorization code has expired');
        }

        if ($this->authCodeRepository->isAuthCodeRevoked($authCodePayload->auth_code_id) === true) {
            throw OAuthServerException::invalidRequest('code', 'Authorization code has been revoked');
        }

        if ($authCodePayload->client_id !== $client->getIdentifier()) {
            throw OAuthServerException::invalidRequest('code', 'Authorization code was not issued to this client');
        }

        // The redirect URI is required in this request
        $redirectUri = $this->getRequestParameter('redirect_uri', $request, null);
        if (empty($authCodePayload->redirect_uri) === false && $redirectUri === null) {
            throw OAuthServerException::invalidRequest('redirect_uri');
        }

        if ($authCodePayload->redirect_uri !== $redirectUri) {
            if (!$this->matchesClientRedirectUri($redirectUri, $client)
                || !$this->matchesClientRedirectUri($authCodePayload->redirect_uri, $client)) {
                throw OAuthServerException::invalidRequest('redirect_uri', 'Invalid redirect URI');
            }
        }
    }


    private function validateCodeChallenge($authCodePayload, $codeVerifier)
    {
        if ($codeVerifier === null) {
            throw OAuthServerException::invalidRequest('code_verifier');
        }

        // Validate code_verifier according to RFC-7636
        // @see: https://tools.ietf.org/html/rfc7636#section-4.1
        if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $codeVerifier) !== 1) {
            throw OAuthServerException::invalidRequest(
                'code_verifier',
                'Code Verifier must follow the specifications of RFC-7636.'
            );
        }

        if (property_exists($authCodePayload, 'code_challenge_method')) {
            $verifiers = [
                'S256' => new S256Verifier(),
                'plain' => new PlainVerifier(),
            ];
            if (isset($verifiers[$authCodePayload->code_challenge_method])) {
                $verifier = $verifiers[$authCodePayload->code_challenge_method];
                if ($verifier->verifyCodeChallenge($codeVerifier, $authCodePayload->code_challenge) === false) {
                    throw OAuthServerException::invalidGrant('Failed to verify `code_verifier`.');
                }
            } else {
                throw OAuthServerException::serverError(
                    sprintf(
                        'Unsupported code challenge method `%s`',
                        $authCodePayload->code_challenge_method
                    )
                );
            }
        }
    }
}
