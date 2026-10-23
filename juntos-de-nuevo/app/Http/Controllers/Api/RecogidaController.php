<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nino;
use App\Persistencia\NinoRepository;
use App\Persistencia\RecogidaRepository;
use App\Validaciones\StoreRecogidaValidacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecogidaController extends Controller
{
    private RecogidaRepository $recogidaRepo;
    private NinoRepository $ninoRepo;

    public function __construct(RecogidaRepository $recogidaRepo, NinoRepository $ninoRepo)
    {
        $this->recogidaRepo = $recogidaRepo;
        $this->ninoRepo = $ninoRepo;
    }

    /**
     * POST /api/ninos/{nino}/recogida
     * Registrar la recogida de un niño por un familiar
     */
    public function store(StoreRecogidaValidacion $request, Nino $nino): JsonResponse
    {
        // Verificar que el niño pertenece al centro del usuario
        if (!$this->ninoRepo->perteneceACentro($nino->id, $request->user()->centro_id)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'No puedes gestionar niños de otros centros',
                ],
            ], 403);
        }

        // Verificar que el niño puede ser recogido
        if (!$nino->puedeSerRecogido()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NINO_YA_RECOGIDO',
                    'message' => 'Este niño ya ha sido recogido o no puede ser recogido',
                ],
            ], 409);
        }

        // Registrar recogida (transacción interna en el repositorio)
        $recogida = $this->recogidaRepo->registrarRecogida(
            ninoId: $nino->id,
            datosFamiliar: $request->validated(),
            registradoPor: $request->user()->id,
            fotoRecogida: $request->file('foto_recogida'),
            notas: $request->notas,
        );

        // Recargar niño para obtener estado actualizado
        $nino->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Recogida registrada exitosamente',
            'data' => [
                'recogida_id' => $recogida->id,
                'nino' => $nino->nombre . ' ' . $nino->apellido,
                'familiar' => $recogida->familiar_nombre . ' ' . $recogida->familiar_apellido,
                'parentesco' => $recogida->familiar_parentesco,
                'fecha' => $recogida->fecha_recogida->format('d/m/Y H:i'),
            ],
        ], 201);
    }

    /**
     * GET /api/recogidas
     * Listar todas las recogidas (solo admin)
     */
    public function index(Request $request): JsonResponse
    {
        $recogidas = $this->recogidaRepo->listarTodas();

        return response()->json([
            'success' => true,
            'data' => $recogidas->map(function ($recogida) {
                return [
                    'id' => $recogida->id,
                    'nino' => $recogida->nino->nombre . ' ' . $recogida->nino->apellido,
                    'centro' => $recogida->nino->centro->nombre,
                    'familiar' => $recogida->familiar_nombre . ' ' . $recogida->familiar_apellido,
                    'parentesco' => $recogida->familiar_parentesco,
                    'telefono' => $recogida->familiar_telefono,
                    'fecha' => $recogida->fecha_recogida->format('d/m/Y H:i'),
                    'registrado_por' => $recogida->registradoPor->nombre,
                ];
            }),
            'meta' => [
                'current_page' => $recogidas->currentPage(),
                'last_page' => $recogidas->lastPage(),
                'total' => $recogidas->total(),
            ],
        ]);
    }
}
