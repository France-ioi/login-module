<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once(__DIR__."/lib/connect.php");
require_once(__DIR__."/lib/oauth_server.php");

$db = connect();

// Handle a request to a resource and authenticate the access token
$server = oauth_server($db);
$request = OAuth2\Request::createFromGlobals();
$token = $server->getAccessTokenData($request);
$response = new OAuth2\Response();
if (!$server->verifyResourceRequest($request, $response)) {
    $response->send();
    die;
}
$user_id = $token['user_id'];

$stmt = $db->prepare("SELECT `id`, `sLogin` FROM `users` WHERE `id` = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetchObject();

// TODO: $badges should contain a list of the user's badges (string used, for
// example, to indicate that the user has qualified for some competition).
$badges = [];

echo json_encode([
  'id' => $user_id,
  'sLogin' => $user->sLogin,
  'badges' => $badges
]);
