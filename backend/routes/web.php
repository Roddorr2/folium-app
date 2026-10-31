<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Folium API',
        'version' => '1.0.0',
        'status' => 'online',
        'docs' => '/api/docs',
        'openapi_spec' => '/api/openapi.yaml',
    ]);
});

Route::get('/api/openapi.yaml', function () {
    $docsPath = base_path('../docs/api.yaml');
    $publicPath = public_path('api.yaml');
    $path = file_exists($docsPath) ? $docsPath : $publicPath;

    abort_unless(file_exists($path), 404, 'OpenAPI specification file not found.');

    return response()->file($path, [
        'Content-Type' => 'application/yaml; charset=UTF-8',
        'Access-Control-Allow-Origin' => '*',
    ]);
});

Route::get('/api/docs', function () {
    $html = <<<'HTML'
<!doctype html>
<html lang="es">
  <head>
    <title>Folium SIGB — Scalar API Reference</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
    <script
      id="api-reference"
      data-url="/api/openapi.yaml"
      data-configuration='{"theme":"kepler","layout":"modern","darkMode":false}'></script>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
  </body>
</html>
HTML;

    return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
});
