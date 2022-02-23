<?php
return [
    'subject' => ":app_name email verification",
    'body' => 
<<<PHP_STR
You are receiving this message because a email verification required for your account.<br>
Verification code: :code<br>
Please follow the link below to continue verification process:<br>
<a href=":url">:url</a> 
PHP_STR
];