<?php

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\SessionHandler;

require_once __DIR__.'/../vendor/autoload.php';

function connect() {
   global $config;
   $host = $config->db->host;
   $database = $config->db->database;
   $password = $config->db->password;
   $user = $config->db->user;
   // computing timezone difference with gmt:
   // http://www.sitepoint.com/synchronize-php-mysql-timezone-configuration/
   $now = new DateTime();
   $mins = $now->getOffset() / 60;
   $sgn = ($mins < 0 ? -1 : 1);
   $mins = abs($mins);
   $hrs = floor($mins / 60);
   $mins -= $hrs * 60;
   $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
   try {
      $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
      $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
      if ($config->db->logged === true) {
         require_once __DIR__.'/shared/LoggedPDO.php';
         $db = new LoggedPDO("mysql:host=".$host.";dbname=".$database, $user, $password, $pdo_options);
      } else {
         $db = new PDO("mysql:host=".$host.";dbname=".$database, $user, $password, $pdo_options);
      }
      $db->exec("SET time_zone='".$offset."';");
   } catch (Exception $e) {
      die("Erreur : " . $e->getMessage());
   }
   return $db;
}

function connect_dynamoDB($config) {
   $client = DynamoDbClient::factory(array(
      'credentials' => array(
           'key'    => $config->aws->key,
           'secret' => $config->aws->secret
       ),
      'region' => $config->aws->region,
      'version' => '2012-08-10'
   ));
   return $client;
}

if ($config->aws->dynamoSessions == true) {
   $dynamoDB = connect_dynamoDB($config);
   // registering the dynamodb session handler performs some useless operations
   // in session!
   if (!isset($noSessions) || !$noSessions) {
      $sessionHandler = SessionHandler::fromClient($dynamoDB, array(
         'table_name'       => 'sessions',
      ));
      $sessionHandler->register();
   }
}
