<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * GET /api/v1/users - List all staff users and readers with role & branch.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $users = User::with(['role', 'branch'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => \App\Http\Resources\UserResource::collection($users)
        ]);
    }

    /**
     * GET /api/v1/roles - List available system roles with presentation metadata.
     */
    public function roles(): JsonResponse
    {
        $roles = Role::all();

        return response()->json([
            'status' => 'success',
            'data' => \App\Http\Resources\RoleResource::collection($roles)
        ]);
    }

    /**
     * POST /api/v1/users - Register new user / staff member.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'dni' => 'nullable|string|max:20',
        ]);

        $role = Role::find($validated['role_id']);

        // Mandatory branch restriction for desk librarian
        if ($role && $role->name === 'librarian' && empty($validated['branch_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'La asignación de sede física (branch_id) es obligatoria para el rol de Bibliotecario de Sede.'
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'branch_id' => $validated['branch_id'] ?? null,
            'dni' => $validated['dni'] ?? null,
        ]);

        $user->load(['role', 'branch']);

        return response()->json([
            'status' => 'created',
            'message' => 'Usuario registrado exitosamente',
            'data' => new \App\Http\Resources\UserResource($user)
        ], 201);
    }

    /**
     * PUT /api/v1/users/{id} - Update user data and role assignment.
     */
    public function update(Request $request, string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_id' => 'sometimes|required|exists:roles,id',
            'branch_id' => 'sometimes|nullable|exists:branches,id',
            'dni' => 'sometimes|nullable|string|max:20',
        ]);

        // Self-privilege degradation guard
        if ($request->user() && (int)$request->user()->id === (int)$user->id) {
            if (isset($validated['role_id']) && (int)$validated['role_id'] !== (int)$user->role_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Operación no permitida: no puedes revocar o modificar tus propios privilegios de administración.'
                ], 403);
            }
        }

        if (isset($validated['role_id'])) {
            $role = Role::find($validated['role_id']);
            $targetBranchId = $validated['branch_id'] ?? $user->branch_id;
            if ($role && $role->name === 'librarian' && empty($targetBranchId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La asignación de sede física es obligatoria para el rol de Bibliotecario de Sede.'
                ], 422);
            }
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load(['role', 'branch']);

        return response()->json([
            'status' => 'updated',
            'message' => 'Usuario actualizado correctamente',
            'data' => new \App\Http\Resources\UserResource($user)
        ]);
    }

    /**
     * DELETE /api/v1/users/{id} - Revoke tokens and delete user.
     */
    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Self-deletion lockout prevention
        if ($request->user() && (int)$request->user()->id === (int)$user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Operación no permitida: no puedes eliminar o suspender tu propia cuenta activa.'
            ], 403);
        }

        Gate::authorize('delete', $user);

        // Revoke Sanctum tokens
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => 'deleted',
            'message' => "Credenciales del usuario ID {$id} revocadas y registro eliminado"
        ]);
    }
}
