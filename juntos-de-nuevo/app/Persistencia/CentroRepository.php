<?php

namespace App\Persistencia;

use App\Models\Centro;

class CentroRepository
{
    /**
     * Crear un nuevo centro
     */
    public function crear(array $datos): Centro
    {
        return Centro::create([
            'nombre' => $datos['nombre'],
            'tipo' => $datos['tipo'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'activo' => false,
        ]);
    }

    /**
     * Buscar centro por ID
     */
    public function buscarPorId(int $id): ?Centro
    {
        return Centro::find($id);
    }

    /**
     * Listar todos los centros con conteos
     */
    public function listarConConteos(int $porPagina = 20, ?string $estado = null)
    {
        $query = Centro::withCount(['ninos', 'usuarios'])
            ->with(['usuarioResponsable:id,centro_id,nombre,foto_identificacion']);

        if ($estado === 'pendientes') {
            $query->where('activo', false);
        } elseif ($estado === 'activos') {
            $query->where('activo', true);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($porPagina)
            ->appends(['estado' => $estado]);
    }

    /**
     * Validar/activar un centro
     */
    public function activar(Centro $centro): bool
    {
        if ($centro->activo) {
            return false;
        }

        return $centro->update(['activo' => true]);
    }

    /**
     * Verificar si un email de centro ya existe
     */
    public function existeEmail(string $email): bool
    {
        return Centro::where('email', $email)->exists();
    }

    /**
     * Contar total de centros
     */
    public function contarTotal(): int
    {
        return Centro::count();
    }

    /**
     * Contar centros activos
     */
    public function contarActivos(): int
    {
        return Centro::where('activo', true)->count();
    }

    /**
     * Contar centros pendientes
     */
    public function contarPendientes(): int
    {
        return Centro::where('activo', false)->count();
    }
}
