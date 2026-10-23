<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Persistencia\RecogidaRepository;

class RecogidaController extends Controller
{
    private RecogidaRepository $recogidaRepo;

    public function __construct(RecogidaRepository $recogidaRepo)
    {
        $this->recogidaRepo = $recogidaRepo;
    }

    /**
     * GET /admin/recogidas
     */
    public function index()
    {
        $recogidas = $this->recogidaRepo->listarTodas();
        return view('admin.recogidas.index', compact('recogidas'));
    }

    /**
     * GET /admin/recogidas/{recogida}
     */
    public function show(int $id)
    {
        $recogida = $this->recogidaRepo->buscarPorIdConDetalles($id);

        if (!$recogida) {
            abort(404, 'Recogida no encontrada');
        }

        return view('admin.recogidas.show', compact('recogida'));
    }
}
