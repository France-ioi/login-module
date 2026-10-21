<?php

namespace App\LoginModule;

class Keys
{

    private static $files = [
        'private' => 'lm-private.key',
        'public' => 'lm-public.key',
    ];


    static function getPath($target) {
        $file = self::$files[$target];
        return storage_path($file);
    }


    static function getPrivate() {
        return file_get_contents(self::getPath('private'));
    }


    static function getPublic() {
        return file_get_contents(self::getPath('public'));
    }


    static function generate() {
        // 2048 bits: RS512 signatures do not fit in smaller keys
        $key = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        if($key === false) {
            throw new \RuntimeException('Cannot generate the keys: '.openssl_error_string());
        }
        openssl_pkey_export($key, $private);
        file_put_contents(self::getPath('private'), $private);
        file_put_contents(self::getPath('public'), openssl_pkey_get_details($key)['key']);
    }

}
