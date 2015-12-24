<?php

// Do not modify this file, but override the configuration
// in a config_local.php file based on config_local_template.php

error_reporting(E_ALL);

$config = (object) array();

$config->db = (object) array();
$config->db->host = 'localhost';
$config->db->database = 'castor';
$config->db->password = 'castor';
$config->db->user = 'castor';
$config->db->logged = false;

$config->login_module = (object) array();
$config->login_module->public_key = '';
$config->login_module->name = '';
$config->login_module->private_key = '';

$config->aws = (object) array();
$config->aws->key = '';
$config->aws->secret = '';
$config->aws->region = '';
$config->aws->dynamoSessions = false;

$config->Facebook = (object) array();
$config->Facebook->appId = '';
$config->Facebook->secret = '';

$config->Google0Auth2 = (object) array();
$config->Google0Auth2->client_id = '';
$config->Google0Auth2->client_secret = '';
$config->Google0Auth2->redirect_uri = '';
$config->Google0Auth2->realm = '';

$config->timezone = ini_get('date.timezone');
$config->selfBaseUrl = 'https://loginaws.algorea.org/';

// variables shared with javascript
$config->shared = (object) array();
$config->shared->Google0Auth2 = (object) array();
$config->shared->Facebook = (object) array();

if (is_readable(__DIR__.'/config_local.php')) {
   include_once __DIR__.'/config_local.php';
}

$config->shared->selfBaseUrl = $config->selfBaseUrl;
$config->shared->Google0Auth2->client_id = $config->Google0Auth2->client_id;
$config->shared->Google0Auth2->redirect_uri = $config->Google0Auth2->redirect_uri;
$config->shared->Google0Auth2->realm = $config->Google0Auth2->realm;
$config->shared->Facebook->appId = $config->Facebook->appId;

date_default_timezone_set($config->timezone);
