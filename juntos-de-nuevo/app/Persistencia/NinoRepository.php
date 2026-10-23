<?php

namespace App\Persistencia;

use App\Models\Nino;
use Illuminate\Http\UploadedFile;

class NinoRepository
{
    /**
     * Listado público de niños (sin recogidos)
     */
    public function listarPublicos(?int $centroId = null, ?string $estado = null, int $porPagina = 20)
    {
        $query = Nino::with('centro:id,nombre')
            ->where('estado', '!=', 'recogido');

        if ($centroId) {
            $query->where('centro_id', $centroId);
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->orderBy('created_at', 'desc')->paginate($porPagina);
    }

    /**
     * Buscar niño por ID con relaciones
     */
    public function buscarConDetalles(int $id): ?Nino
    {
        return Nino::with(['centro', 'familiares', 'creadoPor:id,nombre', 'recogida', 'recogida.registradoPor:id,nombre'])->find($id);
    }

    /**
     * Buscar niño por ID (simple)
     */
    public function buscarPorId(int $id): ?Nino
    {
        return Nino::find($id);
    }

    /**
     * Crear un nuevo niño con foto
     */
    public function crear(array $datos, int $centroId, int $creadoPor, ?UploadedFile $foto = null): Nino
    {
        $fotoPath = null;
        if ($foto) {
            $fotoPath = $foto->store('ninos', 'public');
        }

        return Nino::create([
            'centro_id' => $centroId,
            'foto_url' => $fotoPath,
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'edad' => $datos['edad'] ?? null,
            'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
            'sexo' => $datos['sexo'] ?? null,
            'direccion_previa' => $datos['direccion_previa'] ?? null,
            'nombre_madre' => $datos['nombre_madre'] ?? null,
            'nombre_padre' => $datos['nombre_padre'] ?? null,
            'notas' => $datos['notas'] ?? null,
            'estado' => 'encontrado',
            'creado_por' => $creadoPor,
        ]);
    }

    /**
     * Actualizar datos de un niño
     */
    public function actualizar(Nino $nino, array $datos): bool
    {
        return $nino->update($datos);
    }

    /**
     * Listar niños de un centro específico
     */
    public function listarPorCentro(int $centroId, int $porPagina = 20)
    {
        return Nino::where('centro_id', $centroId)
            ->with('recogida')
            ->orderBy('created_at', 'desc')
            ->paginate($porPagina);
    }

    /**
     * Verificar si un niño pertenece a un centro
     */
    public function perteneceACentro(int $ninoId, int $centroId): bool
    {
        return Nino::where('id', $ninoId)
            ->where('centro_id', $centroId)
            ->exists();
    }

    /**
     * Agregar familiares a un niño
     */
    public function agregarFamiliares(Nino $nino, array $familiares): void
    {
        foreach ($familiares as $familiar) {
            $nino->familiares()->create($familiar);
        }
    }

    /**
     * Contar total de niños
     */
    public function contarTotal(): int
    {
        return Nino::count();
    }

    /**
     * Contar niños por estado
     */
    public function contarPorEstado(string $estado): int
    {
        return Nino::where('estado', $estado)->count();
    }

    /**
     * Contar niños de un centro por estado
     */
    public function contarPorCentroYEstado(int $centroId, string $estado): int
    {
        return Nino::where('centro_id', $centroId)->where('estado', $estado)->count();
    }

    /**
     * Contar total de niños de un centro
     */
    public function contarPorCentro(int $centroId): int
    {
        return Nino::where('centro_id', $centroId)->count();
    }

    /**
     * Últimos niños registrados de un centro
     */
    public function ultimosDelCentro(int $centroId, int $cantidad = 5)
    {
        return Nino::where('centro_id', $centroId)
            ->with('centro:id,nombre')
            ->orderBy('created_at', 'desc')
            ->limit($cantidad)
            ->get();
    }

    /**
     * Listar todos los niños (incluyendo recogidos) para admin
     */
    public function listarTodos(?string $estado = null, int $porPagina = 20)
    {
        $query = Nino::with('centro:id,nombre');

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->orderBy('created_at', 'desc')->paginate($porPagina);
    }

    /**
     * Listar niños de un centro con filtro opcional por estado
     */
    public function listarPorCentroConFiltro(int $centroId, ?string $estado = null, int $porPagina = 20)
    {
        $query = Nino::where('centro_id', $centroId)
            ->with('recogida');

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->orderBy('created_at', 'desc')->paginate($porPagina);
    }
}
