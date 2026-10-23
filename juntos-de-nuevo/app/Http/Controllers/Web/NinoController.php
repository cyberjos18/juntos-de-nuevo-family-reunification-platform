<?php

namespace App\Http\Controllers\Web;

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
     * GET /ninos/{nino} — Ficha pública del niño
     */
    public function show(int $id)
    {
        $nino = $this->ninoRepo->buscarConDetalles($id);

        if (!$nino) {
            abort(404);
        }

        return view('ninos.show', compact('nino'));
    }
}
