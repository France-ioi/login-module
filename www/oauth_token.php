<?php

require_once __DIR__.'/../config.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__."/../lib/connect.php";
require_once __DIR__."/../lib/oauth_server.php";

$db = connect();
$server = oauth_server($db);
$request = OAuth2\Request::createFromGlobals();
$server->handleTokenRequest($request)->send();
