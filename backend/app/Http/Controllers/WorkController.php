<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Contracts\WorkRepositoryInterface;
use App\Http\Requests\WorkQueryRequest;
use App\Models\Author;
use App\Models\Branch;
use App\Models\Language;
use App\Models\Subject;
use App\Models\Work;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WorkController extends Controller
{
    public function __construct(
        private WorkRepositoryInterface $repository
    ) {}

    /**
     * GET /api/v1/works - List all works with full WEMI structure.
     */
    public function index(WorkQueryRequest $request): JsonResponse
    {
        $query = Work::with([
            'authors',
            'subjects',
            'expressions.language',
            'expressions.manifestations.items.branch',
        ]);

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('abstract', 'like', "%{$term}%")
                    ->orWhere('dewey', 'like', "%{$term}%")
                    ->orWhereHas('authors', fn ($aq) => $aq->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('subjects', fn ($sq) => $sq->where('name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('branch_id')) {
            $branchId = (int) $request->input('branch_id');
            $query->whereHas('expressions.manifestations.items', function ($iq) use ($branchId) {
                $iq->where('current_branch_id', $branchId);
            });
        }

        $works = $query->get()->map(fn (Work $work) => $this->formatWorkPayload($work));

        return response()->json([
            'status' => 'success',
            'data' => $works,
        ]);
    }

    /**
     * GET /api/v1/works/{id} - Get a single work details.
     */
    public function show(string|int $id): JsonResponse
    {
        $work = $this->repository->findById($id);

        if (! $work) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $work]);
    }

    /**
     * POST /api/v1/works - Create a new Work in WEMI model.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Work::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'original_language' => 'nullable|string|max:100',
            'originalLanguage' => 'nullable|string|max:100',
            'dewey' => 'nullable|string|max:50',
            'nature' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:255',
            'authors' => 'nullable|array',
            'authors.*' => 'string|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'string|max:255',
            'format' => 'nullable|string|max:150',
            'isbn' => 'nullable|string|max:50',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'initialBranchId' => 'nullable|integer|exists:branches,id',
            'shelf_location' => 'nullable|string|max:100',
            'shelfLocation' => 'nullable|string|max:100',
        ]);

        $originalLanguage = $validated['original_language']
            ?? $validated['originalLanguage']
            ?? 'Español';

        $fullWork = DB::transaction(function () use ($request, $validated, $originalLanguage) {
            $work = Work::create([
                'title' => $validated['title'],
                'abstract' => $validated['abstract'] ?? '',
                'original_language' => $originalLanguage,
                'dewey' => $validated['dewey'] ?? null,
                'nature' => $validated['nature'] ?? 'Obra Literaria',
            ]);

            // Sync N:M Authors (RF1.2)
            $authorNames = $this->extractAuthorNames($validated);
            if (! empty($authorNames)) {
                $authorIds = collect($authorNames)->map(function (string $name) {
                    return Author::firstOrCreate(['name' => trim($name)])->id;
                })->all();
                $work->authors()->sync($authorIds);
            }

            // Sync N:M Subjects (RF1.2)
            $subjectNames = $validated['subjects'] ?? ['Catálogo General'];
            if (! empty($subjectNames)) {
                $subjectIds = collect($subjectNames)->map(function (string $name) {
                    return Subject::firstOrCreate(['name' => trim($name)])->id;
                })->all();
                $work->subjects()->sync($subjectIds);
            }

            // Resolve ISO Language record for Expression FK
            $language = Language::where('native_name', $originalLanguage)
                ->orWhere('name', $originalLanguage)
                ->orWhere('code', strtolower($originalLanguage))
                ->first()
                ?? Language::first()
                ?? Language::create([
                    'code' => 'es',
                    'name' => 'Spanish',
                    'native_name' => 'Español',
                    'script' => 'Latn',
                    'is_active' => true,
                ]);

            $expression = $work->expressions()->create([
                'language_id' => $language->id,
                'type' => 'Texto Impreso',
                'revision_year' => (int) date('Y'),
                'description' => 'Expresión catalogada vía API',
            ]);

            // Extract nested or top-level manifestation & item fields
            $nestedManif = $request->input('expressions.0.manifestations.0', []);
            $nestedItem = $request->input('expressions.0.manifestations.0.items.0', []);

            $format = $validated['format'] ?? ($nestedManif['format'] ?? 'Tapa Dura en Lino');
            $isbn = $validated['isbn'] ?? ($nestedManif['isbn'] ?? ('978-612-'.rand(10000, 99999)));

            $manifestation = $expression->manifestations()->create([
                'format' => $format,
                'isbn' => $isbn,
                'publisher' => $nestedManif['publisher'] ?? 'Editorial Folium SIGB',
                'publication_year' => (int) ($nestedManif['publicationYear'] ?? date('Y')),
            ]);

            $branchId = (int) (
                $validated['branch_id']
                ?? $validated['initialBranchId']
                ?? ($nestedItem['branchId'] ?? null)
                ?? Branch::value('id')
                ?? 1
            );

            $shelfLocation = $validated['shelf_location']
                ?? $validated['shelfLocation']
                ?? ($nestedItem['shelfLocation'] ?? 'ESTANTE-A01');

            $manifestation->items()->create([
                'home_branch_id' => $branchId,
                'current_branch_id' => $branchId,
                'barcode' => 'FOL-'.rand(10000, 99999),
                'shelf_location' => $shelfLocation,
                'status' => 'available',
            ]);

            return Work::with([
                'authors',
                'subjects',
                'expressions.language',
                'expressions.manifestations.items.branch',
            ])->findOrFail($work->id);
        });

        return response()->json([
            'status' => 'created',
            'message' => 'Obra catalogada exitosamente en la base de datos',
            'data' => $this->formatWorkPayload($fullWork),
        ], 201);
    }

    /**
     * PUT /api/v1/works/{id} - Full update of a work.
     */
    public function update(Request $request, string|int $id): JsonResponse
    {
        $work = Work::find($id);
        if (! $work) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        Gate::authorize('update', $work);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'abstract' => 'sometimes|nullable|string',
            'original_language' => 'sometimes|nullable|string|max:100',
            'originalLanguage' => 'sometimes|nullable|string|max:100',
            'dewey' => 'sometimes|nullable|string|max:50',
            'nature' => 'sometimes|nullable|string|max:100',
            'author' => 'sometimes|nullable|string|max:255',
            'authors' => 'sometimes|nullable|array',
            'authors.*' => 'string|max:255',
            'subjects' => 'sometimes|nullable|array',
            'subjects.*' => 'string|max:255',
        ]);

        DB::transaction(function () use ($work, $validated) {
            $updateFields = [];
            if (array_key_exists('title', $validated)) {
                $updateFields['title'] = $validated['title'];
            }
            if (array_key_exists('abstract', $validated)) {
                $updateFields['abstract'] = $validated['abstract'];
            }
            if (array_key_exists('dewey', $validated)) {
                $updateFields['dewey'] = $validated['dewey'];
            }
            if (array_key_exists('nature', $validated)) {
                $updateFields['nature'] = $validated['nature'];
            }
            if (isset($validated['original_language']) || isset($validated['originalLanguage'])) {
                $updateFields['original_language'] = $validated['original_language'] ?? $validated['originalLanguage'];
            }

            if (! empty($updateFields)) {
                $work->update($updateFields);
            }

            $authorNames = $this->extractAuthorNames($validated);
            if (! empty($authorNames)) {
                $authorIds = collect($authorNames)->map(function (string $name) {
                    return Author::firstOrCreate(['name' => trim($name)])->id;
                })->all();
                $work->authors()->sync($authorIds);
            }

            if (! empty($validated['subjects'])) {
                $subjectIds = collect($validated['subjects'])->map(function (string $name) {
                    return Subject::firstOrCreate(['name' => trim($name)])->id;
                })->all();
                $work->subjects()->sync($subjectIds);
            }
        });

        $updatedWork = Work::with([
            'authors',
            'subjects',
            'expressions.language',
            'expressions.manifestations.items.branch',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'updated',
            'message' => "Obra ID {$id} actualizada correctamente",
            'data' => $this->formatWorkPayload($updatedWork),
        ]);
    }

    /**
     * PATCH /api/v1/works/{id} - Partial update of a work field.
     */
    public function patch(Request $request, string|int $id): JsonResponse
    {
        return $this->update($request, $id);
    }

    /**
     * DELETE /api/v1/works/{id} - Delete a work from the catalog.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $work = Work::find($id);
        if (! $work) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        Gate::authorize('delete', $work);

        $work->delete();

        return response()->json([
            'status' => 'deleted',
            'message' => "Obra ID {$id} eliminada correctamente de la base de datos",
        ]);
    }

    /**
     * OPTIONS /api/v1/works - CORS Preflight options endpoint.
     */
    public function options(): JsonResponse
    {
        return response()->json(['methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']])
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    /**
     * Extract normalized list of author names from validated request data.
     *
     * @param  array<string, mixed>  $validated
     * @return array<int, string>
     */
    private function extractAuthorNames(array $validated): array
    {
        if (! empty($validated['authors']) && is_array($validated['authors'])) {
            return array_values(array_filter(array_map('trim', $validated['authors'])));
        }

        if (! empty($validated['author']) && is_string($validated['author'])) {
            return array_values(array_filter(array_map('trim', explode(',', $validated['author']))));
        }

        return [];
    }

    /**
     * Enrich Work model serialization with computed author string and available stock counts.
     *
     * @return array<string, mixed>
     */
    private function formatWorkPayload(Work $work): array
    {
        $payload = $work->toArray();

        $authorString = $work->authors->pluck('name')->join(', ');
        $payload['author'] = $authorString !== '' ? $authorString : 'Autor Intelectual';

        $availableCount = 0;
        foreach ($work->expressions as $expression) {
            foreach ($expression->manifestations as $manifestation) {
                foreach ($manifestation->items as $item) {
                    if ($item->status === 'available') {
                        $availableCount++;
                    }
                }
            }
        }

        $payload['availableCount'] = $availableCount;

        return $payload;
    }
}
