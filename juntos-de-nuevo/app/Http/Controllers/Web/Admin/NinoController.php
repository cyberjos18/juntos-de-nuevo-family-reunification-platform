<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Persistencia\NinoRepository;
use Illuminate\Http\Request;

class NinoController extends Controller
{
    private NinoRepository $ninoRepo;

    public function __construct(NinoRepository $ninoRepo)
    {
        $this->ninoRepo = $ninoRepo;
    }

    /**
     * GET /admin/ninos
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado');

        if ($estado) {
            $ninos = $this->ninoRepo->listarTodos(estado: $estado);
        } else {
            $ninos = $this->ninoRepo->listarTodos();
        }

        return view('admin.ninos.index', compact('ninos', 'estado'));
    }

    /**
     * GET /admin/ninos/{nino}
     */
    public function show(int $id)
    {
        $nino = $this->ninoRepo->buscarConDetalles($id);

        if (!$nino) {
            abort(404);
        }

        return view('admin.ninos.show', compact('nino'));
    }
}
