<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nino;
use App\Models\User;
use App\Persistencia\NinoRepository;
use App\Validaciones\StoreNinoValidacion;
use App\Validaciones\UpdateNinoValidacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NinoController extends Controller
{
    private NinoRepository $ninoRepo;

    public function __construct(NinoRepository $ninoRepo)
    {
        $this->ninoRepo = $ninoRepo;
    }

    /**
     * Intenta resolver el usuario autenticado desde el token Bearer
     * Funciona tanto en rutas públicas como protegidas
     */
    private function usuarioAutenticado(Request $request): ?User
    {
        if ($request->user()) {
            return $request->user();
        }

        // Intentar resolver desde el header Authorization
        $token = $request->bearerToken();
        if ($token) {
            return Auth::guard('sanctum')->user();
        }

        return null;
    }

    /**
     * GET /api/ninos
     * Listado público de niños encontrados
     */
    public function index(Request $request): JsonResponse
    {
        $ninos = $this->ninoRepo->listarPublicos(
            centroId: $request->query('centro_id') ? (int) $request->query('centro_id') : null,
            estado: $request->query('estado'),
        );

        return response()->json([
            'success' => true,
            'data' => $ninos->map(function ($nino) use ($request) {
                $data = [
                    'id' => $nino->id,
                    'nombre' => $nino->nombre,
                    'apellido' => $nino->apellido,
                    'edad' => $nino->edad,
                    'foto_url' => $nino->foto_url ? asset('storage/' . $nino->foto_url) : null,
                    'centro' => $nino->centro->nombre,
                    'estado' => $nino->estado,
                    'fecha_registro' => $nino->created_at->format('d/m/Y'),
                ];

                // Datos sensibles solo para usuarios autenticados
                if ($this->usuarioAutenticado($request)) {
                    $data['sexo'] = $nino->sexo;
                    $data['direccion_previa'] = $nino->direccion_previa;
                    $data['nombre_madre'] = $nino->nombre_madre;
                    $data['nombre_padre'] = $nino->nombre_padre;
                }

                return $data;
            }),
            'meta' => [
                'current_page' => $ninos->currentPage(),
                'last_page' => $ninos->lastPage(),
                'per_page' => $ninos->perPage(),
                'total' => $ninos->total(),
            ],
        ]);
    }

    /**
     * GET /api/ninos/{id}
     * Ficha detallada de un niño
     */
    public function show(Request $request, Nino $nino): JsonResponse
    {
        $ninoCompleto = $this->ninoRepo->buscarConDetalles($nino->id);

        if (!$ninoCompleto) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'NOT_FOUND', 'message' => 'Niño no encontrado'],
            ], 404);
        }

        $data = [
            'id' => $ninoCompleto->id,
            'nombre' => $ninoCompleto->nombre,
            'apellido' => $ninoCompleto->apellido,
            'edad' => $ninoCompleto->edad,
            'fecha_nacimiento' => $ninoCompleto->fecha_nacimiento?->format('d/m/Y'),
            'foto_url' => $ninoCompleto->foto_url ? asset('storage/' . $ninoCompleto->foto_url) : null,
            'centro' => [
                'id' => $ninoCompleto->centro->id,
                'nombre' => $ninoCompleto->centro->nombre,
                'tipo' => $ninoCompleto->centro->tipo,
            ],
            'estado' => $ninoCompleto->estado,
            'fecha_registro' => $ninoCompleto->created_at->format('d/m/Y H:i'),
            'registrado_por' => $ninoCompleto->creadoPor->nombre,
        ];

        // Datos sensibles solo para usuarios autenticados
        if ($this->usuarioAutenticado($request)) {
            $data['sexo'] = $ninoCompleto->sexo;
            $data['direccion_previa'] = $ninoCompleto->direccion_previa;
            $data['nombre_madre'] = $ninoCompleto->nombre_madre;
            $data['nombre_padre'] = $ninoCompleto->nombre_padre;
            $data['notas'] = $ninoCompleto->notas;
            $data['centro']['telefono'] = $ninoCompleto->centro->telefono;
            $data['centro']['email'] = $ninoCompleto->centro->email;
            $data['familiares'] = $ninoCompleto->familiares->map(fn ($f) => [
                'parentesco' => $f->parentesco,
                'nombre' => $f->nombre . ' ' . $f->apellido,
                'telefono' => $f->telefono,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * POST /api/ninos
     * Registrar un nuevo niño (solo gestores)
     */
    public function store(StoreNinoValidacion $request): JsonResponse
    {
        $nino = $this->ninoRepo->crear(
            datos: $request->validated(),
            centroId: $request->user()->centro_id,
            creadoPor: $request->user()->id,
            foto: $request->file('foto'),
        );

        // Agregar familiares si existen
        if ($request->has('familiares')) {
            $this->ninoRepo->agregarFamiliares($nino, $request->familiares);
        }

        return response()->json([
            'success' => true,
            'message' => 'Niño registrado exitosamente',
            'data' => [
                'id' => $nino->id,
                'nombre' => $nino->nombre . ' ' . $nino->apellido,
                'foto_url' => $nino->foto_url ? asset('storage/' . $nino->foto_url) : null,
            ],
        ], 201);
    }

    /**
     * PUT /api/ninos/{id}
     * Actualizar datos de un niño (solo gestor del mismo centro)
     */
    public function update(UpdateNinoValidacion $request, Nino $nino): JsonResponse
    {
        // Admin puede actualizar cualquier niño
        if (!$request->user()->esAdmin()) {
            // Gestor solo puede actualizar niños de su centro
            if (!$this->ninoRepo->perteneceACentro($nino->id, $request->user()->centro_id)) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'FORBIDDEN',
                        'message' => 'No puedes modificar niños de otros centros',
                    ],
                ], 403);
            }
        }

        $this->ninoRepo->actualizar($nino, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Niño actualizado exitosamente',
            'data' => [
                'id' => $nino->id,
                'nombre' => $nino->nombre . ' ' . $nino->apellido,
                'estado' => $nino->estado,
            ],
        ]);
    }

    /**
     * GET /api/gestor/ninos
     * Listar niños del centro del gestor autenticado
     */
    public function misNinos(Request $request): JsonResponse
    {
        $ninos = $this->ninoRepo->listarPorCentro($request->user()->centro_id);

        return response()->json([
            'success' => true,
            'data' => $ninos->map(function ($nino) {
                return [
                    'id' => $nino->id,
                    'nombre' => $nino->nombre . ' ' . $nino->apellido,
                    'edad' => $nino->edad,
                    'estado' => $nino->estado,
                    'foto_url' => $nino->foto_url ? asset('storage/' . $nino->foto_url) : null,
                    'fecha_registro' => $nino->created_at->format('d/m/Y'),
                    'recogido' => $nino->estaRecogido(),
                ];
            }),
            'meta' => [
                'current_page' => $ninos->currentPage(),
                'last_page' => $ninos->lastPage(),
                'total' => $ninos->total(),
            ],
        ]);
    }
}
