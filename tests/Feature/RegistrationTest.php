<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_new_user()
    {
        Notification::fake();
        $response = $this->postJson('/api/register', [
            'first_name' => 'Hayk',
            'last_name'  => 'Margaryan',
            'email'      => 'devhay1996@gmail.com',
            'password'   => '123456789',
            'password_confirmation' => '123456789',
            'device_name' => 'iphone'
        ]);

        $response->assertSuccessful();

        $user = User::where('email', 'druc@pinsmile.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->assertNotEmpty($response->getContent());
        $this->assertDatabaseHas('users', ['email' => 'devhay1996@gmail.com']);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'iphone']);
    }
}