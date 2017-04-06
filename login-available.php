<?php

header('Content-Type: application/json');

if (!isset($_REQUEST['nickname2check']) || $_REQUEST['nickname2check'] == '') {
    die(json_encode(['error' => 'no login passed in nickname2check']));
}

// nickname2check can either be a string of characters (in which case
// json_decode gives NULL), either an array of strings
try {
    $nicknames2check = json_decode($_REQUEST['nickname2check']);
    if(gettype($nicknames2check) == 'array') {
        $is_array = true;
    } elseif(gettype($nicknames2check) == 'NULL') {
        $is_array = false;
    } else {
        die(json_encode(['error' => 'invalid data in nickname2check']));
    }
} catch(Exception $e) {
    $is_array = false;
}

require_once __DIR__.'/config.php';
require_once __DIR__."/lib/connect.php";

function checkNickname ($db1, $db2, $login) {
    // login-module v1
    $stmt = $db1->prepare('select ID from users where sLogin = :login;');
    $stmt->execute(['login' => $login]);
    $res = $stmt->fetch();
    if($res) {
        return 'FALSE';
    }

    // login-module v2 database
    $stmt = $db2->prepare('select id from users where login = :login;');
    $stmt->execute(['login' => $login]);
    $res = $stmt->fetch();

    return $res ? 'FALSE' : 'TRUE';
}

$db1 = connect(); // Connect to our database
$db2 = connect(true); // Connect to the v2 database

if($is_array) {
    $result_array = array();
    foreach($nicknames2check as $nickname) {
        if(gettype($nickname) != 'string') {
            die(json_encode(['error' => 'login passed in array is not of type string']));
        }
        $result_array[] = checkNickname($db1, $db2, $nickname);
    }
    echo json_encode($result_array);
} else {
    echo checkNickname($db1, $db2, $_REQUEST['nickname2check']);
}
