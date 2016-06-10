<?php

header('Content-Type: application/json');

if (!isset($_GET['nickname2check'])) {
    die(json_encode(['error' => 'no login passed in query string']));
}

require_once __DIR__.'/config.php';
require_once __DIR__."/lib/connect.php";

$db = connect();

$stmt = $db->prepare('select ID from users where sLogin = :login;');
$stmt->execute(['login' => $_GET['nickname2check']]);
$res = $stmt->fetch();

if ($res) {
    echo 'FALSE';
} else {
    echo 'TRUE';
}
