<?php

require_once(__DIR__."/connect.php");

$storage = new OAuth2\Storage\Pdo(connect());
$server = new OAuth2\Server($storage);
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
