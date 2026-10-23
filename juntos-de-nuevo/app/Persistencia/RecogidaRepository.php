<?php

namespace App\Persistencia;

use App\Models\Recogida;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RecogidaRepository
{
    /**
     * Registrar recogida dentro de una transacción
     * Garantiza integridad: crea recogida + cambia estado del niño
     */
    public function registrarRecogida(
        int $ninoId,
        array $datosFamiliar,
        int $registradoPor,
        ?UploadedFile $fotoRecogida = null,
        ?string $notas = null
    ): Recogida {
        return DB::transaction(function () use ($ninoId, $datosFamiliar, $registradoPor, $fotoRecogida, $notas) {
            // Subir foto de recogida
            $fotoPath = null;
            if ($fotoRecogida) {
                $fotoPath = $fotoRecogida->store('recogidas', 'public');
            }

            // Crear registro de recogida
            $recogida = Recogida::create([
                'nino_id' => $ninoId,
                'familiar_nombre' => $datosFamiliar['familiar_nombre'],
                'familiar_apellido' => $datosFamiliar['familiar_apellido'],
                'familiar_parentesco' => $datosFamiliar['familiar_parentesco'],
                'familiar_telefono' => $datosFamiliar['familiar_telefono'],
                'familiar_email' => $datosFamiliar['familiar_email'] ?? null,
                'familiar_documento' => $datosFamiliar['familiar_documento'] ?? null,
                'foto_recogida_url' => $fotoPath,
                'notas' => $notas,
                'fecha_recogida' => now(),
                'registrado_por' => $registradoPor,
            ]);

            // Cambiar estado del niño
            $nino = $recogida->nino;
            $nino->marcarComoRecogido();

            return $recogida;
        });
    }

    /**
     * Listar todas las recogidas (para admin)
     */
    public function listarTodas(int $porPagina = 20)
    {
        return Recogida::with([
                'nino:id,centro_id,nombre,apellido',
                'nino.centro:id,nombre',
                'registradoPor:id,nombre',
            ])
            ->orderBy('fecha_recogida', 'desc')
            ->paginate($porPagina);
    }

    /**
     * Contar total de recogidas
     */
    public function contarTotal(): int
    {
        return Recogida::count();
    }

    /**
     * Buscar una recogida por ID con todas sus relaciones
     */
    public function buscarPorIdConDetalles(int $id): ?Recogida
    {
        return Recogida::with([
                'nino:id,centro_id,nombre,apellido,edad,estado',
                'nino.centro:id,nombre',
                'registradoPor:id,nombre',
            ])
            ->find($id);
    }
}
