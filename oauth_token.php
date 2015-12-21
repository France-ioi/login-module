<?php

require_once(__DIR__."/connect.php");
require_once(__DIR__."/oauth_server.php");

$db = connect();
$server = oauth_server($db);
$request = OAuth2\Request::createFromGlobals();
$server->handleTokenRequest($request)->send();
