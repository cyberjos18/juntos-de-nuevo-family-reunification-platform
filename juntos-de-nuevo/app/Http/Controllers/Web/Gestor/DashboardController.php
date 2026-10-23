<?php

namespace App\Http\Controllers\Web\Gestor;

use App\Http\Controllers\Controller;
use App\Persistencia\NinoRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private NinoRepository $ninoRepo;

    public function __construct(NinoRepository $ninoRepo)
    {
        $this->ninoRepo = $ninoRepo;
    }

    /**
     * GET /gestor/dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $centro = $user->centro;
        $centroId = $user->centro_id;

        $stats = [
            'activos' => $this->ninoRepo->contarPorCentroYEstado($centroId, 'encontrado')
                        + $this->ninoRepo->contarPorCentroYEstado($centroId, 'familia_localizada'),
            'recogidos' => $this->ninoRepo->contarPorCentroYEstado($centroId, 'recogido'),
            'familia_localizada' => $this->ninoRepo->contarPorCentroYEstado($centroId, 'familia_localizada'),
            'total' => $this->ninoRepo->contarPorCentro($centroId),
        ];

        $ultimosNinos = $this->ninoRepo->ultimosDelCentro($centroId, 5);

        return view('gestor.dashboard.index', compact('centro', 'stats', 'ultimosNinos'));
    }
}
