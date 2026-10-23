<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\NinoController;
use App\Http\Controllers\Api\RecogidaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Reencuentro
|--------------------------------------------------------------------------
*/

// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/activate/{token}', [AuthController::class, 'activate']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Niños (listado público)
Route::get('/ninos', [NinoController::class, 'index']);
Route::get('/ninos/{nino}', [NinoController::class, 'show']);


// ============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ============================================

Route::middleware('auth:sanctum')->group(function () {

    // Auth (todos los autenticados)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });


    // ============================================
    // RUTAS DE GESTOR (+ centro activo)
    // ============================================

    Route::middleware(['role:gestor', 'centro.activo'])->group(function () {

        // Niños (crear, listar los del centro)
        Route::post('/ninos', [NinoController::class, 'store']);
        Route::get('/gestor/ninos', [NinoController::class, 'misNinos']);

        // Recogidas
        Route::post('/ninos/{nino}/recogida', [RecogidaController::class, 'store']);
    });

    // Actualizar niño: gestor (de su propio centro) o admin (cualquiera)
    Route::middleware(['role:gestor,admin', 'centro.activo'])->group(function () {
        Route::put('/ninos/{nino}', [NinoController::class, 'update']);
    });


    // ============================================
    // RUTAS DE ADMIN
    // ============================================

    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Centros
        Route::get('/centros', [AdminController::class, 'centros']);
        Route::put('/centros/{centro}/validar', [AdminController::class, 'validarCentro']);

        // Estadísticas
        Route::get('/estadisticas', [AdminController::class, 'estadisticas']);

        // Recogidas (ver todas)
        Route::get('/recogidas', [RecogidaController::class, 'index']);

        // Configuración de Mailjet
        Route::prefix('configuracion')->group(function () {
            Route::get('/mailjet', [ConfiguracionController::class, 'mailjet']);
            Route::put('/mailjet', [ConfiguracionController::class, 'updateMailjet']);
            Route::post('/mailjet/test', [ConfiguracionController::class, 'testMailjet']);
        });
    });
});
