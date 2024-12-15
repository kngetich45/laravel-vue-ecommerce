<?php

namespace Tests\Feature;


use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthTest extends TestCase
{
   use RefreshDatabase;


    /** @test */
    public function register_creates_new_user_with_valid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'newuser@paysoko.com',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'User created successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@paysoko.com',
        ]);
    }

    /** @test */
    public function register_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'missingfields@paysoko.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'password']);
    }
    /** @test */
    public function login_returns_token_for_valid_credentials()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@paysoko.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@paysoko.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'error',
                'user',
                'authorisation' => [
                    'token',
                    'type',
                    'expires_in',
                ],
            ])
            ->assertJson(['error' => false]);
    }

    /** @test */
    public function login_fails_for_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid@paysoko.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => true,
                'message' => 'Unauthorized',
            ]);
    }

}

