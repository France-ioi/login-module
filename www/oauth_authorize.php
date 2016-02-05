<?php

require_once __DIR__.'/../config.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__."/../lib/connect.php";
require_once __DIR__."/../lib/session.php";
require_once __DIR__."/../lib/oauth_server.php";

$db = connect();

$have_user_id = array_key_exists('idUser', $login_session);
$user_id = $have_user_id ? $login_session["idUser"] : null;

// Perform OAuth2 request validation and get the client id.
$server = oauth_server($db);
$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}
$authCtrlr = $server->getAuthorizeController();
$client_id = $authCtrlr->getClientId();

// Test whether the user has already authorized this client for (a subset of)
// the requested scope.
$already_authorized = false;
$stmt = $db->prepare("SELECT `scope` FROM `user_authorized_clients` WHERE `user_id` = :user_id AND `client_id` = :client_id;");
$stmt->execute(["user_id" => $user_id, "client_id" => $client_id]);
$scopeUtil = $server->getScopeUtil();
$required_scope = $authCtrlr->getScope();
error_log("required scope: ".json_encode($required_scope));
while ($row = $stmt->fetchObject()) {
  $available_scope = $row->scope;
  error_log("available scope: ".json_encode($available_scope));
  if ($scopeUtil->checkScope($required_scope, $available_scope)) {
    error_log("we have a match!");
    $already_authorized = true;
    break;
  }
}

if (!empty($_POST)) {
  // POST request: redirect to client with the authorization code.
  // XXX We can either get user_id from the session or from the token in
  // $_GET['jwt'] -- it is not clear which is best.
  $is_authorized = $have_user_id && $_POST['authorized'] === 'yes';
  if ($is_authorized && !$already_authorized) {
    $stmt = $db->prepare("INSERT INTO `user_authorized_clients` (`user_id`, `client_id`, `scope`) VALUES (:user_id, :client_id, :scope)");
    $stmt->execute(["user_id" => $user_id, "client_id" => $client_id, "scope" => $required_scope]);
  }
  $server->handleAuthorizeRequest($request, $response, $is_authorized, $user_id);
  $response->send();
  die();
}

//
// GET request
//

// If the user is not logged in, redirect to authorize.html to display the
// login form and redirect back here once the user has logged in.
if (!$have_user_id) {
  header('Location: authorize.html?'.http_build_query([
    'afterLogin' => $_SERVER['REQUEST_URI']
  ]));
  die;
}

// Skip showing the authorization form to the logged-in user if they have
// already authorized the client for the requested scopes.
$approval_prompt = array_key_exists('approval_prompt', $_GET) ? $_GET['approval_prompt'] : '';
if ($already_authorized && $approval_prompt != 'force') {
  $server->handleAuthorizeRequest($request, $response, true, $user_id);
  $response->send();
  die();
}

// Show the authorization form to the user.
$client_data = $server->getStorage('client')->getClientDetails($client_id);
$client_title = $client_data['title'];
$user_login = $login_session["sLogin"];
echo('<!DOCTYPE html>
<form method="POST">
  <p>Autoriser '.htmlspecialchars($client_title).' à accéder à votre compte '.htmlspecialchars($user_login).' ?</p>
  <button type="submit" name="authorized" value="yes">Oui</button>
  <button type="submit" name="authorized" value="no">Non</button>
</form>');
