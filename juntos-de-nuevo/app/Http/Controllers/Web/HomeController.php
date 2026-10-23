<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Persistencia\NinoRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private NinoRepository $ninoRepo;

    public function __construct(NinoRepository $ninoRepo)
    {
        $this->ninoRepo = $ninoRepo;
    }

    /**
     * GET / — Listado público de niños
     */
    public function index(Request $request)
    {
        $estado = $request->get('estado');

        // Validar que el filtro sea válido
        $estadosValidos = ['encontrado', 'familia_localizada'];
        if ($estado && !in_array($estado, $estadosValidos)) {
            $estado = null;
        }

        $ninos = $this->ninoRepo->listarPublicos(
            centroId: null,
            estado: $estado,
            porPagina: 20
        );

        return view('home.index', compact('ninos'));
    }
}
