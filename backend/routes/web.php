<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Folium API',
        'version' => '1.0.0',
        'status' => 'online',
        'docs' => '/api/v1'
    ]);
});
