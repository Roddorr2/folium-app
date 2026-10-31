<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BranchControllerTest extends TestCase
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
    public function public_user_can_list_all_branches(): void
    {
        $response = $this->getJson('/api/v1/branches');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(4, 'data');
    }

    #[Test]
    public function admin_can_create_and_update_branch(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->firstOrFail();
        Sanctum::actingAs($admin);

        $createRes = $this->postJson('/api/v1/branches', [
            'name' => 'Sede Arequipa Centro',
            'city' => 'Arequipa',
            'address' => 'Calle Álvarez Thomas 200',
            'phone' => '(054) 221-100',
        ]);

        $createRes->assertStatus(201)
            ->assertJsonPath('status', 'created')
            ->assertJsonPath('data.name', 'Sede Arequipa Centro');

        $branchId = $createRes->json('data.id');

        $updateRes = $this->putJson("/api/v1/branches/{$branchId}", [
            'name' => 'Sede Arequipa Metropolitana',
        ]);

        $updateRes->assertStatus(200)
            ->assertJsonPath('status', 'updated')
            ->assertJsonPath('data.name', 'Sede Arequipa Metropolitana');
    }

    #[Test]
    public function non_admin_cannot_create_branch(): void
    {
        $librarian = User::whereHas('role', fn ($q) => $q->where('name', 'librarian'))->firstOrFail();
        Sanctum::actingAs($librarian);

        $response = $this->postJson('/api/v1/branches', [
            'name' => 'Sede No Autorizada',
        ]);

        $response->assertStatus(403);
    }
}
