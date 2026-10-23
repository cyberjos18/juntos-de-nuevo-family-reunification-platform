<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Centro;
use App\Persistencia\CentroRepository;
use App\Persistencia\NinoRepository;
use App\Persistencia\RecogidaRepository;
use App\Persistencia\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private CentroRepository $centroRepo;
    private NinoRepository $ninoRepo;
    private RecogidaRepository $recogidaRepo;
    private UserRepository $userRepo;

    public function __construct(
        CentroRepository $centroRepo,
        NinoRepository $ninoRepo,
        RecogidaRepository $recogidaRepo,
        UserRepository $userRepo,
    ) {
        $this->centroRepo = $centroRepo;
        $this->ninoRepo = $ninoRepo;
        $this->recogidaRepo = $recogidaRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * GET /api/admin/centros
     * Listar todos los centros (pendientes y activos)
     */
    public function centros(Request $request): JsonResponse
    {
        $centros = $this->centroRepo->listarConConteos();

        return response()->json([
            'success' => true,
            'data' => $centros->map(function ($centro) {
                return [
                    'id' => $centro->id,
                    'nombre' => $centro->nombre,
                    'tipo' => $centro->tipo,
                    'email' => $centro->email,
                    'telefono' => $centro->telefono,
                    'activo' => $centro->activo,
                    'total_ninos' => $centro->ninos_count,
                    'total_usuarios' => $centro->usuarios_count,
                    'fecha_registro' => $centro->created_at->format('d/m/Y'),
                ];
            }),
            'meta' => [
                'current_page' => $centros->currentPage(),
                'last_page' => $centros->lastPage(),
                'total' => $centros->total(),
            ],
        ]);
    }

    /**
     * PUT /api/admin/centros/{centro}/validar
     * Validar/activar un centro
     */
    public function validarCentro(Centro $centro): JsonResponse
    {
        $activado = $this->centroRepo->activar($centro);

        if (!$activado) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CENTRO_YA_ACTIVO',
                    'message' => 'Este centro ya está activo',
                ],
            ], 409);
        }

        // Activar todos los usuarios del centro
        $usuariosActivados = $this->userRepo->activarUsuariosDeCentro($centro->id);

        return response()->json([
            'success' => true,
            'message' => 'Centro validado y activado exitosamente',
            'data' => [
                'centro_id' => $centro->id,
                'nombre' => $centro->nombre,
                'usuarios_activados' => $usuariosActivados,
            ],
        ]);
    }

    /**
     * GET /api/admin/estadisticas
     * Estadísticas generales del sistema
     */
    public function estadisticas(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_centros' => $this->centroRepo->contarTotal(),
                'centros_activos' => $this->centroRepo->contarActivos(),
                'centros_pendientes' => $this->centroRepo->contarPendientes(),
                'total_ninos' => $this->ninoRepo->contarTotal(),
                'ninos_encontrados' => $this->ninoRepo->contarPorEstado('encontrado'),
                'ninos_recogidos' => $this->ninoRepo->contarPorEstado('recogido'),
                'ninos_familia_localizada' => $this->ninoRepo->contarPorEstado('familia_localizada'),
                'total_recogidas' => $this->recogidaRepo->contarTotal(),
                'total_usuarios' => $this->userRepo->contarTotal(),
                'usuarios_gestores' => $this->userRepo->contarPorRol('gestor'),
            ],
        ]);
    }
}
