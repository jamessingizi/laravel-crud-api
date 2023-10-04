<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_login(): void
    {
        $password = 'test_password';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role
        ]);
    }

    public function test_user_cannot_login_with_wrong_credentials()
    {
        $response = $this->post('/api/login', [
            'email' => fake()->email,
            'password' => fake()->text,
        ]);
        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'invalid login',
        ]);
    }
}
