<?php

namespace Tests\Unit;

use App\OAuth\AuthCodeGrant;
use DateInterval;
use Laravel\Passport\Bridge\Client;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Redirect URI matching of the custom authorization code grant
 * (rewritten for league/oauth2-server 8 during the PHP 8 port).
 */
class AuthCodeGrantTest extends TestCase
{
    private function redirectMatches($uri, $clientRedirect)
    {
        $grant = new AuthCodeGrant(
            $this->createMock(AuthCodeRepositoryInterface::class),
            $this->createMock(RefreshTokenRepositoryInterface::class),
            new DateInterval('PT10M')
        );
        $client = new Client(1, 'client', $clientRedirect, true);
        $method = new \ReflectionMethod($grant, 'matchesClientRedirectUri');
        return $method->invoke($grant, $uri, $client);
    }

    public static function redirectUris()
    {
        return [
            'exact match' => ['http://a.test/cb', 'http://a.test/cb', true],
            'exact mismatch' => ['http://a.test/cb2', 'http://a.test/cb', false],
            'second of a comma separated list' => ['http://b.test/cb', 'http://a.test/cb,http://b.test/cb', true],
            // the pattern is wrapped in /.../, so "/" must be escaped
            'regexp match' => ['https://x.algorea.org/login/cb?x=1', '^https:\/\/[a-z]+\.algorea\.org\/login\/', true],
            'regexp mismatch' => ['https://evil.test/?https://x.algorea.org/login/', '^https:\/\/[a-z]+\.algorea\.org\/login\/', false],
            'unescaped regexp is invalid' => ['https://x.algorea.org/login/', '^https://[a-z]+\.algorea\.org/login/', false],
            'invalid regexp' => ['http://a.test/', '^(', false],
            'null uri' => [null, 'http://a.test/cb', false],
        ];
    }

    #[DataProvider('redirectUris')]
    public function testRedirectUriMatching($uri, $clientRedirect, $expected)
    {
        $this->assertSame($expected, $this->redirectMatches($uri, $clientRedirect));
    }
}
