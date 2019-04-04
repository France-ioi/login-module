<?php

$pwd_length = 10;
$servername = 'localhost';
$username = 'root';
$password = 'buddy';
$dbname = 'login_module';

$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




function randStr() {
    global $pwd_length;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $pwd_length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function resetPwd($id) {
    global $conn;
    $pwd = randStr();
    $hash = password_hash($pwd, PASSWORD_BCRYPT, [ 'cost' => 10 ]);
    $sql = 'UPDATE users SET password = \''.$conn->escape_string($hash).'\' WHERE id = '.$id.' LIMIT 1';
    if(!mysqli_query($conn, $sql)) {
        die('Error: '.mysqli_error($conn));
    }
    return $pwd;
}


$logins = [];
for($i=1; $i<count($argv); $i++) {
    $logins[] = '\''.$conn->escape_string($argv[$i]).'\'';
}
$logins = '('.implode(',', $logins).')';


$sql = '
    SELECT
        id, login
    FROM
        users
    WHERE
        login IN '.$logins;
$res = $conn->query($sql);

echo 'ID Login Password'.PHP_EOL;
if($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        $pwd = resetPwd($row['id']);
        echo $row['id'].' '.$row['login'].' '.$pwd.PHP_EOL;
    }
}
$conn->close();