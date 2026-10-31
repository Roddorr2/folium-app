<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Models\Work;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    #[Test]
    public function public_opac_can_list_works_with_wemi_hierarchy_and_available_counts(): void
    {
        $response = $this->getJson('/api/v1/works');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'availableCount',
                        'authors',
                        'subjects',
                        'expressions' => [
                            '*' => [
                                'id',
                                'language_id',
                                'manifestations' => [
                                    '*' => [
                                        'id',
                                        'format',
                                        'items',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $firstWork = collect($response->json('data'))->firstWhere('id', 101);
        $this->assertNotNull($firstWork);
        $this->assertGreaterThan(0, $firstWork['availableCount']);
    }

    #[Test]
    public function cataloger_can_create_work_with_full_wemi_structure_and_pivots(): void
    {
        $cataloger = User::whereHas('role', fn ($q) => $q->where('name', 'cataloger'))->firstOrFail();
        $branch = Branch::firstOrFail();

        Sanctum::actingAs($cataloger);

        $payload = [
            'title' => 'Comentarios Reales de los Incas',
            'author' => 'Inca Garcilaso de la Vega',
            'subjects' => ['Historia Andina', 'Crónicas Coloniales'],
            'abstract' => 'Crónica fundamental sobre el origen e historia del Imperio Incaico.',
            'original_language' => 'Español',
            'dewey' => '985.02 G21c',
            'nature' => 'Crónica Histórica',
            'format' => 'Tapa Dura en Lino',
            'isbn' => '978-612-4000-01-9',
            'branch_id' => $branch->id,
            'shelf_location' => 'HIST-A01',
        ];

        $response = $this->postJson('/api/v1/works', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'created')
            ->assertJsonPath('data.title', 'Comentarios Reales de los Incas')
            ->assertJsonPath('data.author', 'Inca Garcilaso de la Vega')
            ->assertJsonPath('data.availableCount', 1);

        $this->assertDatabaseHas('works', [
            'title' => 'Comentarios Reales de los Incas',
            'dewey' => '985.02 G21c',
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => 'Inca Garcilaso de la Vega',
        ]);

        $this->assertDatabaseHas('items', [
            'home_branch_id' => $branch->id,
            'current_branch_id' => $branch->id,
            'shelf_location' => 'HIST-A01',
            'status' => 'available',
        ]);
    }

    #[Test]
    public function reader_is_forbidden_from_creating_or_deleting_works(): void
    {
        $reader = User::whereHas('role', fn ($q) => $q->where('name', 'reader'))->firstOrFail();
        Sanctum::actingAs($reader);

        $createRes = $this->postJson('/api/v1/works', [
            'title' => 'Obra No Autorizada',
        ]);
        $createRes->assertStatus(403);

        $work = Work::firstOrFail();
        $deleteRes = $this->deleteJson("/api/v1/works/{$work->id}");
        $deleteRes->assertStatus(403);
    }

    #[Test]
    public function cataloger_can_update_existing_work_and_authors(): void
    {
        $cataloger = User::whereHas('role', fn ($q) => $q->where('name', 'cataloger'))->firstOrFail();
        $work = Work::firstOrFail();

        Sanctum::actingAs($cataloger);

        $response = $this->putJson("/api/v1/works/{$work->id}", [
            'title' => 'Título Revisado Edición Crítica',
            'author' => 'Gabriel García Márquez',
            'dewey' => '863.64 G25R',
            'abstract' => 'Sinopsis actualizada por catalogación.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'updated')
            ->assertJsonPath('data.title', 'Título Revisado Edición Crítica')
            ->assertJsonPath('data.dewey', '863.64 G25R');
    }

    #[Test]
    public function openapi_contract_and_scalar_docs_endpoints_are_accessible(): void
    {
        $docsResponse = $this->get('/api/docs');
        $docsResponse->assertStatus(200)
            ->assertSee('@scalar/api-reference', false);

        $specResponse = $this->get('/api/openapi.yaml');
        $specResponse->assertStatus(200);
    }
}

