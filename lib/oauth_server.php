<?php

function oauth_server($db) {
  $storage = new OAuth2\Storage\Pdo($db);
  $server = new OAuth2\Server($storage);
  $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
  $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, []));
  return $server;
}

/* Handle a request to a resource and authenticate the access token */
function getAuthenticateUserToken($db) {
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

function isAdminUserId($db, $userId) {
  $stmt = $db->prepare("SELECT `bIsAdmin` FROM `users` WHERE `id` = :user_id");
  $stmt->execute(['user_id' => $userId]);
  $row = $stmt->fetchObject();
  return $row && $row->bIsAdmin;
}
