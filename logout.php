<?php

require_once __DIR__.'/connect.php';

session_start();
unset($_SESSION);
session_unset();
session_destroy();
session_write_close();
session_regenerate_id(true);
