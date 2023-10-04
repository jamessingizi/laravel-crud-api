<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/api/admin/create_user', [
            'name' => 'test_user',
            'email' => 'test.user@gmail.com',
            'password' => 'top-secret',
            'phone_number' => '0775123645',
            'role' => 'user',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'test_user',
            'email' => 'test.user@gmail.com',
            'phone_number' => '0775123645',
            'role' => 'user',
        ]);
    }

    /**
     * @test
     */
    public function cannot_create_user_if_not_admin()
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->post('/api/admin/create_user', [
            'name' => 'test_user',
            'email' => 'test.user@gmail.com',
            'password' => 'top-secret',
            'phone_number' => '0775123645',
            'role' => 'user',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'unauthorized!',
        ]);
    }

    /**
     * @test
     */
    public function admin_can_view_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(9)->create();
        $response = $this->actingAs($admin)->get('/api/admin/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'phone_number',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ],
                ],
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
        $response->assertJsonCount(10, 'data');

    }

    /**
     * @test
     */
    public function cannot_view_users_if_not_admin()
    {
        $user = User::factory()->create(['role' => 'user']);
        User::factory()->count(9)->create();
        $response = $this->actingAs($user)->get('/api/admin/users');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'unauthorized!',
        ]);
    }

    /**
     * @test
     */
    public function admin_can_view_single_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'role' => 'user',
        ]);
        $response = $this->actingAs($admin)->get("/api/admin/user/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
        ]);

    }

    /**
     * @test
     */
    public function admin_can_update_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => '0772465712',
            'role' => 'user',
        ]);
        $response = $this->actingAs($admin)->put('/api/admin/update_user', [
            'id' => $user->id,
            'name' => 'new name',
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => 'new name',
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
        ]);

    }

    /**
     * @test
     */
    public function user_can_view_their_details()
    {
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'role' => 'user',
        ]);
        $response = $this->actingAs($user)->get("/api/user/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
        ]);

    }

    /**
     * @test
     */
    public function cannot_view_their_details_for_other_users()
    {
        $user1 = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'role' => 'user',
        ]);

        $user2 = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user2)->get("/api/user/{$user1->id}");

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'unauthorised to view this resource'
        ]);

    }

    /**
     * @test
     */
    public function user_can_update_their_details()
    {
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => '0772465712',
            'role' => 'user',
        ]);
        $response = $this->actingAs($user)->put('/api/user/update_user', [
            'id' => $user->id,
            'name' => 'new name',
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => 'new name',
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
        ]);
    }

    /**
     * @test
     */
    public function cannot_view_user_details_without_user_role()
    {
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'role' => 'test',
        ]);
        $response = $this->actingAs($user)->get("/api/user/{$user->id}");

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'unauthorized!'
        ]);
    }

}
