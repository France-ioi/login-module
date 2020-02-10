<?php
return [
    'subject' => ":app_name - Password reset request",
    'body' => "Hello,
A recovery request for the account :login on the platform :app_name,
associated with your email address, has been made.
If you requested this recovery, here is the code to paste into the recovery
interface in which you made the request :
:token
If you didn't make that request, you can ignore this message.
Best regards,
-- 
The technical team"
];
