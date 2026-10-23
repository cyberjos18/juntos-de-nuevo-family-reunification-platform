<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Persistencia\UserRepository;

class ActivationController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * GET /activar/{token} — Activar cuenta por token
     */
    public function activate(string $token)
    {
        $success = $this->userRepo->activarPorToken($token);

        return view('auth.activate', compact('success'));
    }
}
