<?php

function getRequiredPostParam($name) {
  if (!array_key_exists($name, $_POST)) {
    echo json_encode(['success' => false, 'error' => $name." is required"]);
    die();
  }
  return $_POST[$name];
}

function getAdminPostParam($name, $default, $isAdmin) {
  /* param `userId` is optional, overwritten by the authenticated user id unless
     the authenticated user is an admin, enabling an admin to perform this request
     on behalf of another user. */
  if ($isAdmin and array_key_exists($name, $_POST)) {
    return $_POST[$name];
  } else {
    return $default;
  }
}
