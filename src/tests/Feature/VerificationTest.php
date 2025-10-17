<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class VerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testVerificationMail()
    {
        Notification::fake();

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@example.com',
            'password' => '11111111',
            'password_confirmation' => '11111111'
        ]);

        $user = User::where('name', '鈴木一郎')->first();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo(
            [$user],
            VerifyEmail::class
        );
    }

    public function testRedirectToMailhog()
    {
        Notification::fake();

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@example.com',
            'password' => '11111111',
            'password_confirmation' => '11111111'
        ]);

        $user = User::where('name', '鈴木一郎')->first();
        $this->actingAs($user);

        $response->assertRedirect('email/verify');
        $response = $this->get('/email/verify');
        $response->assertStatus(200);
        $this->assertMatchesRegularExpression(
            '/<a[^>]+href="http:\/\/localhost:8025"[^>]*>[\s\S]*?認証はこちらから[\s\S]*?<\/a>/',
            $response->getContent()
        );
    }

    public function testVerification(){
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@example.com',
            'password' => '11111111',
            'password_confirmation' => '11111111'
        ]);

        $user = User::where('name', '鈴木一郎')->first();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');
    }
}
        
