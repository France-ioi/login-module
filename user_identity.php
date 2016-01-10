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
$auth_user_id = $token['user_id'];

// TODO: clean this up!
$stmt = $db->prepare("SELECT `bIsAdmin` FROM `users` WHERE `id` = :user_id");
$stmt->execute(['user_id' => $auth_user_id]);
$admin_user = $stmt->fetchObject();
$is_admin = $admin_user && $admin_user->bIsAdmin;

// Default to querying the authenticated user's profile.
$query_user_id = $auth_user_id;

// Administrators can query any profile.
if ($is_admin and array_key_exists('idUser', $_GET)) {
  $query_user_id = $_GET['idUser'];
}

$stmt = $db->prepare("SELECT `id`, `sLogin`, `sFirstName`, `sLastName` FROM `users` WHERE `id` = :user_id");
$stmt->execute(['user_id' => $query_user_id]);
$user = $stmt->fetchObject();
if (!$user) {
  echo json_encode(['error' => 'no such user']);
  die();
}

$stmt = $db->prepare("SELECT `sBadge` FROM `user_badges` WHERE `idUser` = :user_id");
$stmt->execute(['user_id' => $query_user_id]);
$badges = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

echo json_encode([
  'idUser' => $query_user_id,
  'sLogin' => $user->sLogin,
  'sFirstName' => $user->sFirstName,
  'sLastName' => $user->sLastName,
  'badges' => $badges
]);
