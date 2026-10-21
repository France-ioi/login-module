<?php

namespace Tests\Feature;

use App\Email;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\PeerVerificationNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;

class NotificationTest extends FeatureTestCase
{
    private function render($notification)
    {
        $email = Email::where('email', 't1@t.t')->firstOrFail();
        return (string) $notification->toMail($email)->render();
    }

    public function testResetPasswordMail()
    {
        $html = $this->render(new ResetPasswordNotification('TOKEN123'));
        $this->assertStringContainsString('TOKEN123', $html);
        // the "-- " signature separator must not turn "Best regards," into a heading
        $this->assertDoesNotMatchRegularExpression('/<h[1-6][^>]*>\s*Best regards/', $html);
        $this->assertMatchesRegularExpression('/--\s*<br|--\s*\n?The technical team/', $html);
    }

    public function testVerificationMails()
    {
        $this->assertStringContainsString('CODE42', $this->render(new PeerVerificationNotification('CODE42')));
        $this->assertNotEmpty($this->render(new EmailVerificationNotification()));
    }

    public function testResetLinkIsSentByEmail()
    {
        Notification::fake();
        $this->post('/password/email', ['login_or_email' => 't1@t.t'])->assertRedirect();
        Notification::assertSentTo(Email::where('email', 't1@t.t')->first(), ResetPasswordNotification::class);
    }
}
