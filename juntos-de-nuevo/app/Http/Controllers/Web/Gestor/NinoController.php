<?php

namespace App\Http\Controllers\Web\Gestor;

use App\Http\Controllers\Controller;
use App\Persistencia\NinoRepository;
use App\Validaciones\StoreNinoValidacion;
use Illuminate\Http\Request;

class NinoController extends Controller
{
    private NinoRepository $ninoRepo;

    public function __construct(NinoRepository $ninoRepo)
    {
        $this->ninoRepo = $ninoRepo;
    }

    /**
     * GET /gestor/ninos
     */
    public function index(Request $request)
    {
        $centroId = auth()->user()->centro_id;
        $estado = $request->query('estado');
        $ninos = $this->ninoRepo->listarPorCentroConFiltro($centroId, $estado);

        return view('gestor.ninos.index', compact('ninos', 'estado'));
    }

    /**
     * GET /gestor/ninos/crear
     */
    public function create()
    {
        return view('gestor.ninos.create');
    }

    /**
     * POST /gestor/ninos
     */
    public function store(StoreNinoValidacion $request)
    {
        $centroId = auth()->user()->centro_id;
        $creadoPor = auth()->id();

        $nino = $this->ninoRepo->crear(
            $request->validated(),
            $centroId,
            $creadoPor,
            $request->file('foto')
        );

        // Agregar familiares si existen
        if ($request->has('familiares') && is_array($request->familiares)) {
            $this->ninoRepo->agregarFamiliares($nino, $request->familiares);
        }

        return redirect()
            ->route('gestor.ninos.index')
            ->with('success', 'Niño registrado exitosamente.');
    }

    /**
     * GET /gestor/ninos/{nino}
     */
    public function show(int $id)
    {
        $nino = $this->ninoRepo->buscarConDetalles($id);

        if (!$nino || !$this->ninoRepo->perteneceACentro($id, auth()->user()->centro_id)) {
            abort(403);
        }

        return view('gestor.ninos.show', compact('nino'));
    }
}
