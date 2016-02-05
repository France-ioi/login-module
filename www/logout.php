<?php

require_once __DIR__.'/../config.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../lib/session.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(405);
  echo 'Method Not Allowed (use POST)';
  die;
}

session_unset();
session_regenerate_id(true);

header('Content-Type: application/json');

$hasFacebook = false;
$hasGoogle = false;

if (array_key_exists('hasGoogle', $login_session)) {
	$hasGoogle = $login_session['hasGoogle'];
}
if (array_key_exists('hasFacebook', $login_session)) {
	$hasFacebook = $login_session['hasFacebook'];
}

echo json_encode(['hasFacebook' => $hasFacebook, 'hasGoogle' => $hasGoogle]);
