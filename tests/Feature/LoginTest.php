<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function login_existing_user()
    {
        $user = User::create([
            'first_name' => 'Hayk',
            'last_name'  => 'Margaryan',
            'email'      => 'devhay1996@gmail.com',
            'password'   => bcrypt('123456789'),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => '123456789',
            'deviceName' => 'iphone'
        ]);

        $response->assertSuccessful();

        $this->assertNotEmpty($response->getContent());
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'iphone',
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);
    }

    public function get_user_from_token()
    {
        $user = User::create([
            'first_name' => 'Hayk',
            'last_name'  => 'Margaryan',
            'email'      => 'devhay1996@gmail.com',
            'password'   => bcrypt('123456789'),
        ]);

        $token = $user->createToken('iphone')->plainTextToken;

        $response = $this->get('/api/user', [
            'Authorization' => 'Bearer '. $token
        ]);

        $response->assertSuccessful();

        $response->assertJson(function ($json) {
            $json->where('email', 'devhay1996@gmail.com')
                ->missing('password')
                ->etc();
        });

    }
}
