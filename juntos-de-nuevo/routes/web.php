<?php

use App\Http\Controllers\Web\Auth\ActivationController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\NinoController;
use App\Http\Controllers\Web\Gestor\DashboardController as GestorDashboardController;
use App\Http\Controllers\Web\Gestor\NinoController as GestorNinoController;
use App\Http\Controllers\Web\Gestor\RecogidaController as GestorRecogidaController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\CentroController as AdminCentroController;
use App\Http\Controllers\Web\Admin\ConfiguracionController as AdminConfiguracionController;
use App\Http\Controllers\Web\Admin\NinoController as AdminNinoController;
use App\Http\Controllers\Web\Admin\RecogidaController as AdminRecogidaController;
use App\Http\Controllers\Web\Admin\UsuarioController as AdminUsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Reencuentro
|--------------------------------------------------------------------------
*/

// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ninos/{nino}', [NinoController::class, 'show'])->name('ninos.show');

// ============================================
// AUTH (solo para invitados)
// ============================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registro', [RegisterController::class, 'register']);
});

// Activación de cuenta (accesible desde email)
Route::get('/activar/{token}', [ActivationController::class, 'activate'])->name('activate');

// Logout (requiere autenticación)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================
// RUTAS DE GESTOR (protegidas)
// ============================================

Route::middleware(['auth', 'role:gestor', 'centro.activo'])
    ->prefix('gestor')
    ->name('gestor.')
    ->group(function () {
        Route::get('/dashboard', [GestorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/ninos', [GestorNinoController::class, 'index'])->name('ninos.index');
        Route::get('/ninos/crear', [GestorNinoController::class, 'create'])->name('ninos.create');
        Route::post('/ninos', [GestorNinoController::class, 'store'])->name('ninos.store');
        Route::get('/ninos/{nino}', [GestorNinoController::class, 'show'])->name('ninos.show');
        Route::get('/ninos/{nino}/recogida', [GestorRecogidaController::class, 'create'])->name('recogidas.create');
        Route::post('/ninos/{nino}/recogida', [GestorRecogidaController::class, 'store'])->name('recogidas.store');
    });

// ============================================
// RUTAS DE ADMIN (protegidas)
// ============================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/centros', [AdminCentroController::class, 'index'])->name('centros.index');
        Route::put('/centros/{centro}/validar', [AdminCentroController::class, 'validar'])->name('centros.validar');
        Route::get('/configuracion/mailjet', [AdminConfiguracionController::class, 'mailjet'])->name('configuracion.mailjet');
        Route::put('/configuracion/mailjet', [AdminConfiguracionController::class, 'updateMailjet'])->name('configuracion.mailjet.update');
        Route::post('/configuracion/mailjet/test', [AdminConfiguracionController::class, 'testMailjet'])->name('configuracion.mailjet.test');

        // Registro de centros
        Route::get('/configuracion/registro-centros', [AdminConfiguracionController::class, 'registroCentros'])->name('configuracion.registro-centros');
        Route::put('/configuracion/registro-centros', [AdminConfiguracionController::class, 'actualizarRegistroCentros'])->name('configuracion.registro-centros.update');

        // Niños
        Route::get('/ninos', [AdminNinoController::class, 'index'])->name('ninos.index');
        Route::get('/ninos/{nino}', [AdminNinoController::class, 'show'])->name('ninos.show');

        // Recogidas
        Route::get('/recogidas', [AdminRecogidaController::class, 'index'])->name('recogidas.index');
        Route::get('/recogidas/{recogida}', [AdminRecogidaController::class, 'show'])->name('recogidas.show');

        // Usuarios
        Route::get('/usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [AdminUsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [AdminUsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/editar', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');
        Route::put('/usuarios/{usuario}/password', [AdminUsuarioController::class, 'updatePassword'])->name('usuarios.updatePassword');
        Route::delete('/usuarios/{usuario}', [AdminUsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
