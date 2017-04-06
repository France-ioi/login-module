<?php

$config->timezone = 'Europe/Paris';

// Database for this login-module
$config->db->host = 'localhost';
$config->db->database = 'login_module';
$config->db->password = '';
$config->db->user = 'login_module';

// Database for the login-module v2 (not this one)
$config->db2->host = 'localhost';
$config->db2->database = 'login_module';
$config->db2->password = '';
$config->db2->user = 'login_module';

// Emails
$config->login_module = (object) array();
$config->login_module->public_key = "";
$config->login_module->name = 'name in the jws token';
$config->login_module->private_key = "";

$config->selfBaseUrl = 'path/to/login.html';
$config->cookieHost = 'example.com';
