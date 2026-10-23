<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentroActivo
{
    /**
     * Verifica que el centro del usuario autenticado esté activo.
     * Solo aplica a usuarios con rol "gestor" (los admin no tienen centro).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'UNAUTHORIZED',
                        'message' => 'No autenticado',
                    ],
                ], 401);
            }

            return redirect()->route('login');
        }

        // Admin no necesita validación de centro
        if ($user->esAdmin()) {
            return $next($request);
        }

        // Gestor debe tener centro activo
        if (!$user->centro_id || !$user->centro || !$user->centro->activo) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'CENTRO_INACTIVO',
                        'message' => 'Tu centro aún no ha sido activado. Contacta al administrador.',
                    ],
                ], 403);
            }

            return redirect()->route('home')
                ->with('error', 'Tu centro aún no ha sido activado. Contacta al administrador.');
        }

        return $next($request);
    }
}
