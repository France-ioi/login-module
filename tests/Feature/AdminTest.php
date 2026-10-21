<?php

namespace Tests\Feature;

class AdminTest extends FeatureTestCase
{
    public static function adminPages()
    {
        return [
            ['/admin/users'],
            ['/admin/users/100000002'],
            ['/admin/users/100000002/permissions'],
            ['/admin/clients'],
            ['/admin/clients/1/edit'],
            ['/admin/official_domains'],
            ['/admin/origin_instances'],
            ['/admin/lti_configs'],
            ['/admin/badge_apis'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminPages')]
    public function testAdminPagesAreDisplayed($path)
    {
        $this->actingAs($this->user('admin'))->get($path)->assertOk();
    }

    public function testRegularUserCannotAccessAdminPages()
    {
        $this->actingAs($this->user('test1'))->get('/admin/users')->assertForbidden();
    }

    public function testRolesCanBeGrantedAndRevoked()
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)
            ->post('/admin/users/100000002/permissions', ['roles' => ['admin']])
            ->assertRedirect();
        $this->assertTrue($this->user('test1')->hasRole('admin'));

        $this->actingAs($admin)
            ->post('/admin/users/100000002/permissions', [])
            ->assertRedirect();
        $this->assertFalse($this->user('test1')->fresh()->hasRole('admin'));
    }
}
