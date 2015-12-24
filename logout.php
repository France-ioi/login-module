<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/lib/session.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(405);
  echo 'Method Not Allowed (use POST)';
  die;
}

session_unset();
session_regenerate_id(true);

header('Content-Type: application/json');

// TODO: if the user was authenticated with their Google+ account,
// return {"provider":"google"}.

// TODO: if the user was authenticated with their Facebook account,
// return {"provider":"facebook"}.

echo '{}';
