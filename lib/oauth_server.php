<?php

function oauth_server($db) {
  $storage = new OAuth2\Storage\Pdo($db);
  $server = new OAuth2\Server($storage);
  $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
  $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, []));
  return $server;
}
