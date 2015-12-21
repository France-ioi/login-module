<?php

require_once(__DIR__."/connect.php");
require_once(__DIR__."/oauth_server.php");

$db = connect();
$server = oauth_server($db);
$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

session_start();
$login_session = array();
if (array_key_exists('modules', $_SESSION) &&
    array_key_exists('login', $_SESSION['modules']))
  $login_session = $_SESSION['modules']['login'];

// GET request: show the authorization form.
if (empty($_POST)) {
  if (!array_key_exists('idUser', $login_session)) {
    // TODO: show the login form if the user is not already logged in.
    echo('<!DOCTYPE html><p>TODO: make user log in</p>');
    die;
  }
  // TODO: also request user credentials if the query string indicates
  // that we are requesting authorisation from a specific user, and this
  // user differs from the logged-in user (if any).
  $user_login = $login_session["sLogin"];
  echo('<!DOCTYPE html>
<form method="POST">
  <p>Autoriser (XXX client name here) à accéder à votre compte '.htmlspecialchars($user_login).' ?</p>
  <button type="submit" name="authorized" value="yes">Oui</button>
  <button type="submit" name="authorized" value="no">Non</button>
</form>');
  die;
}

// POST request: redirect to client with the authorization code.
$is_authorized = ($_POST['authorized'] === 'yes');
$userid = $login_session["idUser"];
$server->handleAuthorizeRequest($request, $response, $is_authorized, $userid);
$response->send();
