<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware de autenticación Sanctum para API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Alias de middlewares personalizados
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'centro.activo' => \App\Http\Middleware\EnsureCentroActivo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

// Este proyecto sirve la app desde `findchild/` (index.php, favicon,
// /images, /build) en vez de la carpeta `public/` estándar de Laravel,
// que fue eliminada. Debe fijarse aquí, antes de que arranque el kernel
// HTTP, porque config/filesystems.php y el helper @vite leen public_path()
// durante el arranque (LoadConfiguration corre antes que cualquier
// ServiceProvider::register()).
$app->usePublicPath(dirname(__DIR__, 2));

return $app;
