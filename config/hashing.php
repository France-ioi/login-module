<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Hashing
    |--------------------------------------------------------------------------
    |
    | Keeps the Laravel 5.4 behaviour:
    |
    | - rehash_on_login MUST stay false: App\LoginModule\UserPassword accepts
    |   the admin master password and obsolete passwords for a user, and the
    |   session guard would otherwise store a new hash of whatever password
    |   was used to log in (e.g. the master password) in users.password.
    | - verify = false: Hash::check() returns false instead of throwing an
    |   exception when a stored hash is not a bcrypt hash.
    | - 10 rounds, as before (Laravel 11+ defaults to 12).
    |
    */

    'driver' => 'bcrypt',

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
        'verify' => false,
        'limit' => null,
    ],

    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
        'verify' => true,
    ],

    'rehash_on_login' => false,

];
