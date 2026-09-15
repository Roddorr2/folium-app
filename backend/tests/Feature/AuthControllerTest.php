<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(BranchSeeder::class);
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function user_can_login_with_valid_credentials_and_receive_token_and_resource(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@folium.org',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'token',
                'user' => [
                    'id',
                    'name',
                    'short_name',
                    'initials',
                    'email',
                    'role_id',
                    'role' => [
                        'key',
                        'display_name',
                        'short_label',
                        'badge_variant',
                    ],
                ]
            ]);

        $userData = $response->json('user');
        $this->assertSame('admin@folium.org', $userData['email']);
        $this->assertSame('admin', $userData['role']['key']);

        // Verify no password hash leaks
        $this->assertArrayNotHasKey('password', $userData);
        $this->assertArrayNotHasKey('remember_token', $userData);
    }

    #[Test]
    public function login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@folium.org',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function authenticated_user_can_access_me_profile_endpoint(): void
    {
        $user = User::where('email', 'admin@folium.org')->firstOrFail();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.email', 'admin@folium.org')
            ->assertJsonPath('user.role.key', 'admin')
            ->assertJsonPath('user.role.badge_variant', 'forest');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_me_profile_endpoint(): void
    {
        $response = $this->getJson('/api/v1/auth/me');
        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_logout_and_revoke_tokens(): void
    {
        $user = User::where('email', 'admin@folium.org')->firstOrFail();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Sesión cerrada correctamente');
    }
}
