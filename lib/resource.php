
function getAuthenticateUserToken($db) {
  // Handle a request to a resource and authenticate the access token
  $server = oauth_server($db);
  $request = OAuth2\Request::createFromGlobals();
  $token = $server->getAccessTokenData($request);
  $response = new OAuth2\Response();
  if (!$server->verifyResourceRequest($request, $response)) {
      $response->send();
      die;
  }
  return $token;
}
