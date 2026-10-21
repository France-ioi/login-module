<?php

namespace Tests\Feature;

use App\Email;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;

class PasswordResetTest extends FeatureTestCase
{
    private function requestToken()
    {
        Notification::fake();
        $this->post('/password/email', ['login_or_email' => 't1@t.t'])->assertRedirect();
        $token = null;
        Notification::assertSentTo(
            Email::where('email', 't1@t.t')->first(),
            ResetPasswordNotification::class,
            function ($notification) use (&$token) {
                $token = $notification->token;
                return true;
            }
        );
        $this->assertSame(config('auth.reset_password_token_length'), strlen($token));
        return $token;
    }

    private function reset($token)
    {
        return $this->post('/password/reset', [
            'token' => $token,
            'email' => 't1@t.t',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);
    }

    public function testTokenIsValidForAnHour()
    {
        $token = $this->requestToken();
        // config auth.passwords.emails.expire is in minutes (60)
        $this->travel(59)->minutes();

        $this->reset($token)->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function testTokenExpires()
    {
        $token = $this->requestToken();
        $this->travel(61)->minutes();

        $this->reset($token)->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function testSixCharacterPasswordsAreAccepted()
    {
        $token = $this->requestToken();
        $this->post('/password/reset', [
            'token' => $token,
            'email' => 't1@t.t',
            'password' => 'abc123',
            'password_confirmation' => 'abc123',
        ])->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function testSecondRequestIsRefusedDuringTheRecoveryInterval()
    {
        $token = $this->requestToken();
        $this->reset($token)->assertSessionHasNoErrors();
        auth()->logout();

        Notification::fake();
        $this->post('/password/email', ['login_or_email' => 't1@t.t']);
        Notification::assertNothingSent();
    }
}
