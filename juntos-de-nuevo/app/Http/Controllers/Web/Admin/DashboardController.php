<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Persistencia\CentroRepository;
use App\Persistencia\NinoRepository;
use App\Persistencia\RecogidaRepository;
use App\Persistencia\UserRepository;

class DashboardController extends Controller
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
     * GET /admin/dashboard
     */
    public function index()
    {
        $stats = [
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
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
