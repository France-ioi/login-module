<?php

namespace Tests\Feature;

use App\Client;
use Illuminate\Support\Facades\Artisan;

class OAuthTest extends FeatureTestCase
{
    const SECRET = 'oauth-test-client-secret-0123456789abcd';

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        if (!file_exists(storage_path('oauth-private.key'))) {
            Artisan::call('passport:keys');
        }
        // like the admin interface, the regexp must escape "/" (it is wrapped in /.../)
        $this->client = Client::forceCreate([
            'name' => 'Test platform',
            'secret' => self::SECRET,
            'redirect' => 'https://exact.test/callback,^https:\/\/[a-z]+\.platform\.test\/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'user_attributes' => ['login'],
        ]);
    }

    private function authorizeQuery($redirect, $extra = [])
    {
        return '/oauth/authorize?'.http_build_query(array_merge([
            'client_id' => $this->client->id,
            'response_type' => 'code',
            'redirect_uri' => $redirect,
            'state' => 'xyz',
            'scope' => 'account',
        ], $extra));
    }

    private function getCode($redirect)
    {
        $user = $this->user('test1');
        $page = $this->actingAs($user)->get($this->authorizeQuery($redirect));
        $page->assertOk()->assertViewIs('passport::authorize');

        $response = $this->actingAs($user)->post('/oauth/authorize', [
            'state' => 'xyz',
            'client_id' => $this->client->id,
            'auth_token' => $page->viewData('authToken'),
        ]);
        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith($redirect.'?', $location);
        parse_str(parse_url($location, PHP_URL_QUERY), $query);
        $this->assertSame('xyz', $query['state']);
        return $query['code'];
    }

    private function exchange($code, $redirect)
    {
        return $this->post('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->client->id,
            'client_secret' => self::SECRET,
            'redirect_uri' => $redirect,
            'code' => $code,
        ]);
    }

    public function testAuthorizationCodeFlowWithExactRedirectUri()
    {
        $code = $this->getCode('https://exact.test/callback');
        $token = $this->exchange($code, 'https://exact.test/callback')
            ->assertOk()
            ->assertJsonStructure(['token_type', 'expires_in', 'access_token', 'refresh_token'])
            ->json();

        $this->app['auth']->forgetGuards();
        $account = $this->withHeader('Authorization', 'Bearer '.$token['access_token'])
            ->getJson('/user_api/account')
            ->assertOk()
            ->assertJsonPath('login', 'test1')
            ->assertJsonPath('client_id', $this->client->id)
            ->json();

        // dates keep the Laravel 5.4 serialization format
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $account['created_at']);
        $this->assertArrayNotHasKey('password', $account);
    }

    public function testRegexpRedirectUri()
    {
        $code = $this->getCode('https://www.platform.test/callback');
        // the redirect_uri may differ at token time, as long as it is valid for the client
        $this->exchange($code, 'https://other.platform.test/callback')
            ->assertOk()
            ->assertJsonStructure(['access_token']);
    }

    public function testInvalidRedirectUriIsRejected()
    {
        $this->actingAs($this->user('test1'))
            ->get($this->authorizeQuery('https://evil.test/callback'))
            ->assertStatus(401);
    }

    public function testCodeExchangeWithInvalidRedirectUriIsRejected()
    {
        $code = $this->getCode('https://exact.test/callback');
        $this->exchange($code, 'https://evil.test/callback')->assertStatus(401);
    }

    public function testWrongClientSecretIsRejected()
    {
        $code = $this->getCode('https://exact.test/callback');
        $this->post('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->client->id,
            'client_secret' => 'wrong',
            'redirect_uri' => 'https://exact.test/callback',
            'code' => $code,
        ])->assertStatus(401);
    }

    public function testAutoApprovedClientSkipsTheConsentScreen()
    {
        $this->client->autoapprove_authorization = true;
        $this->client->save();

        $this->actingAs($this->user('test1'))
            ->get($this->authorizeQuery('https://exact.test/callback'))
            ->assertRedirect();
    }

    public function testGuestIsRedirectedToTheLoginPage()
    {
        $this->get($this->authorizeQuery('https://exact.test/callback'))
            ->assertRedirect('/auth');
    }

    public function testUserApiRequiresAToken()
    {
        $this->getJson('/user_api/account')->assertStatus(401);
    }
}
