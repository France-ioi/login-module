<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once(__DIR__."/lib/connect.php");
require_once(__DIR__."/lib/oauth_server.php");

$db = connect();
$token = getAuthenticateUserToken($db);
$authUserId = $token['user_id'];
$isAdmin = isAdminUserId($db, $authUserId);

/* Param `idUser` can be set by an admin and causes details on the specified
   user to be returned.  Normal users can only query their own user details.
*/
if ($isAdmin and array_key_exists('idUser', $_GET)) {
  $userId = $_GET['idUser'];
} else {
  $userId = $authUserId;
}

$stmt = $db->prepare("SELECT `id`, `sLogin`, `sFirstName`, `sLastName` FROM `users` WHERE `id` = :user_id");
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetchObject();
if (!$user) {
  echo json_encode(['error' => 'no such user']);
  die();
}

$stmt = $db->prepare("SELECT `sBadge` FROM `user_badges` WHERE `idUser` = :user_id and bDoNotPossess = 0");
$stmt->execute(['user_id' => $userId]);
$badges = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

echo json_encode([
  'idUser' => $userId,
  'sLogin' => $user->sLogin,
  'sFirstName' => $user->sFirstName,
  'sLastName' => $user->sLastName,
  'aBadges' => $badges
]);
