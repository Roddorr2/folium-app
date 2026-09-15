<?php

use App\Models\Language;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes — Folium Web Application (IFLA LRM WEMI)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // OPTIONS Preflight handling for CORS
    Route::options('/{any}', [WorkController::class, 'options'])->where('any', '.*');

    // Public Auth & Login Endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public Catalog & Works REST Endpoints
    Route::get('/works', [WorkController::class, 'index']);
    Route::get('/works/{id}', [WorkController::class, 'show']);

    // Branch REST Endpoints
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);

    // Languages Endpoint
    Route::get('/languages', fn() => response()->json(['status' => 'success', 'data' => Language::where('is_active', true)->get()]));

    // Protected Routes (Sanctum Auth Required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Works mutation routes
        Route::post('/works', [WorkController::class, 'store']);
        Route::put('/works/{id}', [WorkController::class, 'update']);
        Route::patch('/works/{id}', [WorkController::class, 'patch']);
        Route::delete('/works/{id}', [WorkController::class, 'destroy']);

        // Branches mutation routes
        Route::post('/branches', [BranchController::class, 'store']);
        Route::put('/branches/{id}', [BranchController::class, 'update']);
        Route::delete('/branches/{id}', [BranchController::class, 'destroy']);

        // Users & Roles Management (Admin Policy Protected)
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::get('/roles', [UserController::class, 'roles']);

        Route::post('/loans', fn() => response()->json(['status' => 'created', 'message' => 'Préstamo procesado']));
        Route::post('/transfers', fn() => response()->json(['status' => 'created', 'message' => 'Transferencia iniciada']));
    });
});
