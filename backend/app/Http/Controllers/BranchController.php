<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    /**
     * GET /api/v1/branches - List all library branches with items count.
     */
    public function index(): JsonResponse
    {
        $branches = Branch::withCount('items')->with('users')->get();

        return response()->json([
            'status' => 'success',
            'data' => $branches
        ]);
    }

    /**
     * GET /api/v1/branches/{id} - Get single branch details with physical inventory items.
     */
    public function show(string|int $id): JsonResponse
    {
        $branch = Branch::with(['items.manifestation.expression.work', 'users'])->find($id);

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sede no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $branch
        ]);
    }

    /**
     * POST /api/v1/branches - Create a new library branch.
     */
    public function store(Request $request): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('create', Branch::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $branch = Branch::create($validated);

        return response()->json([
            'status' => 'created',
            'message' => 'Sede bibliotecaria registrada exitosamente',
            'data' => $branch
        ], 201);
    }

    /**
     * PUT /api/v1/branches/{id} - Update an existing branch.
     */
    public function update(Request $request, string|int $id): JsonResponse
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sede no encontrada'
            ], 404);
        }

        \Illuminate\Support\Facades\Gate::authorize('update', $branch);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|nullable|string|max:100',
            'address' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
        ]);

        $branch->update($validated);

        return response()->json([
            'status' => 'updated',
            'message' => "Sede ID {$id} actualizada correctamente",
            'data' => $branch
        ]);
    }

    /**
     * DELETE /api/v1/branches/{id} - Delete a branch.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sede no encontrada'
            ], 404);
        }

        \Illuminate\Support\Facades\Gate::authorize('delete', $branch);

        if ($branch->items()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se puede eliminar la sede porque cuenta con ejemplares físicos asignados.'
            ], 422);
        }

        $branch->delete();

        return response()->json([
            'status' => 'deleted',
            'message' => "Sede ID {$id} eliminada correctamente"
        ]);
    }
}
