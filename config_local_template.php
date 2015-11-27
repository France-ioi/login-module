<?php

$config->timezone = 'Europe/Paris';

$config->db->host = 'localhost';
$config->db->database = 'login_module';
$config->db->password = '';
$config->db->user = 'login_module';

// Emails
$config->login_module = (object) array();
$config->login_module->public_key = "";
$config->login_module->name = 'name in the jws token';
$config->login_module->private_key = "";

$config->selfBaseUrl = 'path/to/login.html';
