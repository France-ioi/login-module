<?php

// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 3600);
// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600, '/', $config->cookieHost, true, true);

session_start();

$login_session = array();
if (array_key_exists('modules', $_SESSION) &&
    array_key_exists('login', $_SESSION['modules']))
  $login_session = $_SESSION['modules']['login'];

