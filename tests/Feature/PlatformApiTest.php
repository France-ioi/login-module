<?php

namespace Tests\Feature;

use App\Client;
use App\User;

class PlatformApiTest extends FeatureTestCase
{
    private function callApi($path, $data, $client = null)
    {
        $client = $client ?: Client::findOrFail(1);
        $response = $this->post('/platform_api/'.$path, [
            'client_id' => $client->id,
            'data' => openssl_encrypt(json_encode($data), 'AES-128-ECB', $client->secret),
        ]);
        $response->assertOk();
        return json_decode(openssl_decrypt($response->getContent(), 'AES-128-ECB', $client->secret), true);
    }

    public function testAccountsAreCreatedAndDeleted()
    {
        $res = $this->callApi('accounts_manager/create', [
            'prefix' => 'ptest_',
            'amount' => 3,
            'postfix_length' => 5,
            'password_length' => 8,
        ]);
        $this->assertTrue($res['success']);
        $this->assertCount(3, $res['data']);
        foreach ($res['data'] as $account) {
            $this->assertStringStartsWith('ptest_', $account['login']);
            $this->assertSame(8, strlen($account['password']));
        }
        $this->assertSame(3, User::where('login', 'like', 'ptest_%')->count());

        $res = $this->callApi('accounts_manager/delete', ['prefix' => 'ptest_']);
        $this->assertTrue($res['success']);
        $this->assertSame(0, User::where('login', 'like', 'ptest_%')->count());
    }

    public function testWrongParams()
    {
        $res = $this->callApi('accounts_manager/create', ['prefix' => '']);
        $this->assertFalse($res['success']);
        $this->assertSame('Wrong params', $res['error']);
    }

    public function testUnknownClient()
    {
        $this->post('/platform_api/accounts_manager/create', ['client_id' => 9999])
            ->assertStatus(400);
    }
}
