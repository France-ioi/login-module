<?php

namespace Tests\Unit;

use App\LoginModule\Shared\TokenGenerator;
use App\LoginModule\Shared\TokenParser;
use PHPUnit\Framework\TestCase;

/**
 * Task tokens: generated with namshi/jose (RS512), parsed back with the
 * parser rewritten during the PHP 8 port (it used spomky-labs/jose).
 */
class TokensTest extends TestCase
{
    private static $private;
    private static $public;

    public static function setUpBeforeClass(): void
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if ($key === false) {
            self::markTestSkipped('openssl cannot generate keys: '.openssl_error_string());
        }
        openssl_pkey_export($key, self::$private);
        self::$public = openssl_pkey_get_details($key)['key'];
    }

    public function testGeneratedTokenIsParsedBack()
    {
        $token = (new TokenGenerator('platform.test', self::$private))
            ->generateToken(['idUser' => 42, 'sLogin' => 'jdoe']);

        $this->assertCount(3, explode('.', $token));
        $header = json_decode(base64_decode(strtr(explode('.', $token)[0], '-_', '+/')), true);
        $this->assertSame(['alg' => 'RS512', 'typ' => 'JWS'], $header);

        $params = (new TokenParser(self::$public, 'platform', 'public'))->decodeJWS($token);
        $this->assertSame(42, $params['idUser']);
        $this->assertSame('jdoe', $params['sLogin']);
        $this->assertSame(date('d-m-Y'), $params['date']);
        $this->assertSame('platform.test', $params['platform']);
        $this->assertArrayHasKey('iat', $params);
    }

    public function testTokenSignedWithAnotherKeyIsRejected()
    {
        $other = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($other, $otherPrivate);
        $token = (new TokenGenerator('platform.test', $otherPrivate))->generateToken(['idUser' => 1]);

        $this->expectException(\Exception::class);
        (new TokenParser(self::$public, 'platform'))->decodeJWS($token);
    }

    public function testUnsignedTokenIsRejected()
    {
        $b64 = function ($data) {
            return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
        };
        $token = $b64(['alg' => 'none', 'typ' => 'JWS']).'.'.$b64(['idUser' => 1, 'date' => date('d-m-Y')]).'.';

        $this->expectException(\Exception::class);
        (new TokenParser(self::$public, 'platform'))->decodeJWS($token);
    }

    public function testExpiredTokenIsRejected()
    {
        $token = \App\LoginModule\Shared\Jws::sign(['idUser' => 1, 'date' => '01-01-2020'], self::$private);

        $this->expectExceptionMessage('API token expired');
        (new TokenParser(self::$public, 'platform'))->decodeJWS($token);
    }
}
