<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verify_email_address()
    {
        $user = User::create([
            'first_name' => 'Hayk',
            'last_name'  => 'Margaryan',
            'email'      => 'devhay1996@gmail.com',
            'password'   => bcrypt('123456789')
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->get($url);
        $response->assertSuccessful();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /** @test */
    public function resend_verification_email()
    {
        Notification::fake();

        $user = User::create([
            'first_name' => 'Hayk',
            'last_name'  => 'Margaryan',
            'email'      => 'devhay1996@gmail.com',
            'password'   => bcrypt('123456789')
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('verification.send'));
        $response->assertSuccessful();

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
