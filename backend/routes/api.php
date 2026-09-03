<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Folium Web Application
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public OPAC Catalog & Search
    Route::get('/search', fn() => response()->json(['message' => 'Search endpoint ready']));
    Route::get('/works/{id}', fn($id) => response()->json(['message' => "Work details for ID {$id}"]));
    Route::get('/recommendations', fn() => response()->json(['message' => 'Recommendations endpoint ready']));

    // Protected Routes (Sanctum Auth Required)
    Route::middleware('auth:sanctum')->group(function () {

        // Circulation (Loans & Returns)
        Route::post('/loans', fn() => response()->json(['message' => 'Issue loan endpoint ready']));
        Route::post('/loans/{id}/return', fn($id) => response()->json(['message' => "Return loan ID {$id}"]));
        Route::post('/loans/{id}/renew', fn($id) => response()->json(['message' => "Renew loan ID {$id}"]));

        // Multi-Branch Transfers
        Route::post('/transfers', fn() => response()->json(['message' => 'Initiate transfer endpoint ready']));
        Route::post('/transfers/{id}/confirm', fn($id) => response()->json(['message' => "Confirm transfer ID {$id}"]));

        // Cataloging (WEMI CRUD)
        Route::apiResource('works', 'App\Http\Controllers\WorkController')->only(['store', 'update', 'destroy']);
    });
});
