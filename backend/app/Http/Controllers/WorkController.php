<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkQueryRequest;
use App\Infrastructure\Persistence\EloquentWorkRepository;
use App\Models\Work;
use App\Models\Expression;
use App\Models\Manifestation;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkController extends Controller
{
    public function __construct(
        private EloquentWorkRepository $repository
    ) {}

    /**
     * GET /api/v1/works - List all works with full WEMI structure.
     */
    public function index(WorkQueryRequest $request): JsonResponse
    {
        $works = Work::with(['authors', 'subjects', 'expressions.manifestations.items.branch'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $works
        ]);
    }

    /**
     * GET /api/v1/works/{id} - Get a single work details.
     */
    public function show(string|int $id): JsonResponse
    {
        $work = $this->repository->findById($id);

        if (!$work) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $work]);
    }

    /**
     * POST /api/v1/works - Create a new Work in WEMI model.
     */
    public function store(Request $request): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('create', Work::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'original_language' => 'nullable|string|max:100',
            'dewey' => 'nullable|string|max:50',
            'nature' => 'nullable|string|max:100',
            'author' => 'nullable|string'
        ]);

        $work = Work::create([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'] ?? '',
            'original_language' => $validated['original_language'] ?? 'Español',
            'dewey' => $validated['dewey'] ?? null,
            'nature' => $validated['nature'] ?? 'Obra Literaria'
        ]);

        // Create default expression & manifestation & item if provided
        $expression = $work->expressions()->create([
            'language' => $validated['original_language'] ?? 'Español',
            'type' => 'Texto Impreso',
            'revision_year' => date('Y'),
            'description' => 'Expresión catalogada vía API'
        ]);

        $manifestation = $expression->manifestations()->create([
            'format' => $request->input('format', 'Tapa Dura en Lino'),
            'isbn' => $request->input('isbn', '978-612-' . rand(10000, 99999)),
            'publisher' => 'Editorial Folium SIGB',
            'publication_year' => date('Y'),
            'dimensions' => '24 x 17 cm, 320 pp.'
        ]);

        $manifestation->items()->create([
            'branch_id' => $request->input('branch_id', 1),
            'barcode' => 'FOL-' . rand(10000, 99999),
            'shelf_location' => $request->input('shelf_location', 'ESTANTE-A01'),
            'status' => 'available'
        ]);

        $fullWork = Work::with(['authors', 'subjects', 'expressions.manifestations.items.branch'])->find($work->id);

        return response()->json([
            'status' => 'created',
            'message' => 'Obra catalogada exitosamente en la base de datos',
            'data' => $fullWork
        ], 201);
    }

    /**
     * PUT /api/v1/works/{id} - Full update of a work.
     */
    public function update(Request $request, string|int $id): JsonResponse
    {
        $work = Work::find($id);
        if (!$work) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        \Illuminate\Support\Facades\Gate::authorize('update', $work);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'abstract' => 'sometimes|nullable|string',
            'original_language' => 'sometimes|nullable|string|max:100'
        ]);

        $work->update($validated);

        return response()->json([
            'status' => 'updated',
            'message' => "Obra ID {$id} actualizada correctamente",
            'data' => Work::with(['authors', 'subjects', 'expressions.manifestations.items.branch'])->find($id)
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
        if (!$work) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        \Illuminate\Support\Facades\Gate::authorize('delete', $work);

        $work->delete();

        return response()->json([
            'status' => 'deleted',
            'message' => "Obra ID {$id} eliminada correctamente de la base de datos"
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
}

