<?php

require_once(__DIR__."/oauth_server.php");
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
