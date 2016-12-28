<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once(__DIR__."/lib/connect.php");
require_once(__DIR__."/lib/params.php");
require_once(__DIR__."/lib/oauth_server.php");
require_once __DIR__.'/lib/badge.php';

$db = connect();
$token = getAuthenticateUserToken($db);
$authUserId = $token['user_id'];
$isAdmin = isAdminUserId($db, $authUserId);

$badgeUrl = getRequiredPostParam('badgeUrl');
$qualCode = getRequiredPostParam('qualCode');
$idUser = getAdminPostParam('idUser', $authUserId, $isAdmin);

$result = attachBadge($idUser, $badgeUrl, ['code' => $qualCode], 'code');
echo json_encode($result);
