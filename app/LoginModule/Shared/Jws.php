<?php

namespace App\LoginModule\Shared;

/**
 * Minimal RS512 JWS (JSON Web Signature) used for task / LTI tokens.
 *
 * Replaces the abandoned namshi/jose library and produces the same tokens:
 * header {"alg":"RS512","typ":"JWS"}, an "iat" claim added when missing,
 * JSON encoded with unescaped slashes, url-safe base64 without padding.
 */
class Jws
{
    const ALGORITHM = 'RS512';

    /**
     * @param  array  $payload
     * @param  string|\OpenSSLAsymmetricKey  $privateKey  PEM private key
     * @return string
     */
    public static function sign(array $payload, $privateKey)
    {
        if (!isset($payload['iat'])) {
            $payload['iat'] = time();
        }
        $input = self::encode(json_encode(['alg' => self::ALGORITHM, 'typ' => 'JWS'], JSON_UNESCAPED_SLASHES))
            .'.'.self::encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $key = openssl_pkey_get_private($privateKey);
        if ($key === false || !openssl_sign($input, $signature, $key, OPENSSL_ALGO_SHA512)) {
            throw new \RuntimeException('Cannot sign the token: '.openssl_error_string());
        }
        return $input.'.'.self::encode($signature);
    }

    /**
     * Verifies the RS512 signature and returns the payload.
     *
     * @param  string  $token
     * @param  string|\OpenSSLAsymmetricKey  $publicKey  PEM public key
     * @return array
     * @throws \InvalidArgumentException  malformed token, other algorithm or invalid signature
     */
    public static function verify($token, $publicKey)
    {
        $parts = explode('.', (string) $token);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Malformed token');
        }
        $header = json_decode(self::decode($parts[0]), true);
        $payload = json_decode(self::decode($parts[1]), true);
        if (!is_array($header) || !is_array($payload)) {
            throw new \InvalidArgumentException('Malformed token');
        }
        if (!isset($header['alg']) || $header['alg'] !== self::ALGORITHM) {
            throw new \InvalidArgumentException('Unsupported token algorithm');
        }
        $key = openssl_pkey_get_public($publicKey);
        if ($key === false) {
            throw new \InvalidArgumentException('Invalid public key');
        }
        $signature = self::decode($parts[2]);
        if (openssl_verify($parts[0].'.'.$parts[1], $signature, $key, OPENSSL_ALGO_SHA512) !== 1) {
            throw new \InvalidArgumentException('Invalid token signature');
        }
        return $payload;
    }

    private static function encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function decode($data)
    {
        // accepts url-safe and standard base64, with or without padding
        return (string) base64_decode(strtr($data, '-_', '+/'));
    }
}
