<?php

namespace App\Http\Controllers\Web\Gestor;

use App\Http\Controllers\Controller;
use App\Persistencia\NinoRepository;
use App\Persistencia\RecogidaRepository;
use App\Validaciones\StoreRecogidaValidacion;
use Illuminate\Http\Request;

class RecogidaController extends Controller
{
    private NinoRepository $ninoRepo;
    private RecogidaRepository $recogidaRepo;

    public function __construct(NinoRepository $ninoRepo, RecogidaRepository $recogidaRepo)
    {
        $this->ninoRepo = $ninoRepo;
        $this->recogidaRepo = $recogidaRepo;
    }

    /**
     * GET /gestor/ninos/{nino}/recogida
     */
    public function create(int $ninoId)
    {
        $nino = $this->ninoRepo->buscarConDetalles($ninoId);

        if (!$nino || !$this->ninoRepo->perteneceACentro($ninoId, auth()->user()->centro_id)) {
            abort(403);
        }

        if (!$nino->puedeSerRecogido()) {
            return redirect()
                ->route('gestor.ninos.show', $ninoId)
                ->with('error', 'Este niño no puede ser recogido.');
        }

        return view('gestor.recogidas.create', compact('nino'));
    }

    /**
     * POST /gestor/ninos/{nino}/recogida
     */
    public function store(StoreRecogidaValidacion $request, int $ninoId)
    {
        $nino = $this->ninoRepo->buscarPorId($ninoId);

        if (!$nino || !$this->ninoRepo->perteneceACentro($ninoId, auth()->user()->centro_id)) {
            abort(403);
        }

        if (!$nino->puedeSerRecogido()) {
            return redirect()
                ->route('gestor.ninos.show', $ninoId)
                ->with('error', 'Este niño no puede ser recogido.');
        }

        $this->recogidaRepo->registrarRecogida(
            $ninoId,
            $request->validated(),
            auth()->id(),
            $request->file('foto_recogida'),
            $request->input('notas')
        );

        return redirect()
            ->route('gestor.ninos.index')
            ->with('success', 'Recogida registrada exitosamente. El niño ha sido marcado como recogido.');
    }
}
