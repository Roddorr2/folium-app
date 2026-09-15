<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Database\Seeders\RoleSeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class UserControllerTest extends TestCase
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
    public function admin_can_list_all_users(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'short_name',
                        'initials',
                        'email',
                        'role_id',
                        'role' => [
                            'id',
                            'key',
                            'name',
                            'display_name',
                            'short_label',
                            'badge_variant',
                        ],
                        'branch',
                    ]
                ]
            ]);
    }

    #[Test]
    public function non_admin_is_forbidden_from_listing_users(): void
    {
        $cataloger = User::whereHas('role', fn ($q) => $q->where('name', 'cataloger'))->firstOrFail();
        Sanctum::actingAs($cataloger);

        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_is_unauthorized_from_listing_users(): void
    {
        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_retrieve_roles_with_presentation_metadata(): void
    {
        $user = User::firstOrFail();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'name',
                        'display_name',
                        'short_label',
                        'badge_variant',
                    ]
                ]
            ]);

        $roles = $response->json('data');
        $adminRole = collect($roles)->firstWhere('key', 'admin');
        $this->assertNotNull($adminRole);
        $this->assertSame('forest', $adminRole['badge_variant']);
        $this->assertSame('Admin', $adminRole['short_label']);
    }

    #[Test]
    public function admin_can_create_new_staff_user(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        $catalogerRole = Role::where('name', 'cataloger')->firstOrFail();
        $branch = Branch::firstOrFail();

        Sanctum::actingAs($admin);

        $payload = [
            'name' => 'Nuevo Especialista WEMI',
            'email' => 'nuevo.catalogador@folium.test',
            'password' => 'secret123',
            'role_id' => $catalogerRole->id,
            'branch_id' => $branch->id,
            'dni' => '77889900',
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'created')
            ->assertJsonPath('data.name', 'Nuevo Especialista WEMI')
            ->assertJsonPath('data.role.key', 'cataloger');

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo.catalogador@folium.test',
            'dni' => '77889900',
        ]);
    }

    #[Test]
    public function creating_librarian_without_branch_fails_validation(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        $librarianRole = Role::where('name', 'librarian')->firstOrFail();

        Sanctum::actingAs($admin);

        $payload = [
            'name' => 'Bibliotecario Sin Sede',
            'email' => 'sin.sede@folium.test',
            'password' => 'secret123',
            'role_id' => $librarianRole->id,
            'branch_id' => null,
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    #[Test]
    public function admin_cannot_demote_or_modify_own_admin_privileges(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        $readerRole = Role::where('name', 'reader')->firstOrFail();

        Sanctum::actingAs($admin);

        $payload = [
            'role_id' => $readerRole->id,
        ];

        $response = $this->putJson("/api/v1/users/{$admin->id}", $payload);

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error');
    }

    #[Test]
    public function admin_cannot_delete_their_own_active_account(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/v1/users/{$admin->id}");

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    #[Test]
    public function admin_can_delete_another_user_and_revoke_tokens(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        $target = User::whereHas('role', fn ($q) => $q->where('name', 'reader'))->firstOrFail();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/v1/users/{$target->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'deleted');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}
