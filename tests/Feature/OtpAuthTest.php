<?php

namespace Tests\Feature;

use App\Mail\VerificationCodeMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_request_otp_for_login(): void
    {
        Mail::fake();

        $response = $this->post(route('auth.otp.send'), [
            'email' => 'mytestuser@gmail.com',
        ]);

        $response->assertRedirect(route('auth.otp', ['email' => 'mytestuser@gmail.com']));
        
        $this->assertDatabaseHas('email_verifications', [
            'email' => 'mytestuser@gmail.com',
            'purpose' => 'login',
        ]);

        Mail::assertSent(VerificationCodeMail::class, function ($mail) {
            return $mail->email === 'mytestuser@gmail.com';
        });
    }

    public function test_can_verify_otp_and_login_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@gmail.com',
            'role' => User::ROLE_PM,
        ]);

        $otp = EmailVerification::createOtp('existing@gmail.com', 'login');

        $response = $this->post(route('auth.otp.verify'), [
            'email' => 'existing@gmail.com',
            'code' => $otp->code,
        ]);

        $response->assertRedirect(route('change-requests.index'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
        $this->assertNotNull(Auth::user()->email_verified_at);
    }

    public function test_can_verify_otp_and_register_new_client_automatically(): void
    {
        $otp = EmailVerification::createOtp('newclient@gmail.com', 'login');

        $response = $this->post(route('auth.otp.verify'), [
            'email' => 'newclient@gmail.com',
            'code' => $otp->code,
        ]);

        $response->assertRedirect(route('change-requests.index'));
        $this->assertTrue(Auth::check());
        $this->assertEquals('newclient@gmail.com', Auth::user()->email);
        $this->assertEquals(User::ROLE_CLIENT, Auth::user()->role);
        
        $this->assertDatabaseHas('clients', [
            'email' => 'newclient@gmail.com',
        ]);
    }

    public function test_invalid_otp_code_is_rejected(): void
    {
        $otp = EmailVerification::createOtp('tester@gmail.com', 'login');

        $response = $this->from(route('auth.otp', ['email' => 'tester@gmail.com']))
            ->post(route('auth.otp.verify'), [
                'email' => 'tester@gmail.com',
                'code' => '000000',
            ]);

        $response->assertRedirect(route('auth.otp', ['email' => 'tester@gmail.com']));
        $response->assertSessionHasErrors('code');
        $this->assertFalse(Auth::check());
    }
}
