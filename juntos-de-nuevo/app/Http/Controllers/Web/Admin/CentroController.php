<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Persistencia\CentroRepository;
use App\Persistencia\ConfiguracionRepository;
use App\Persistencia\UserRepository;
use App\Services\BrevoMailService;
use App\Services\MailConfigService;
use App\Models\Centro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CentroController extends Controller
{
    private CentroRepository $centroRepo;
    private UserRepository $userRepo;
    private BrevoMailService $brevoService;
    private MailConfigService $mailConfig;
    private ConfiguracionRepository $configRepo;

    public function __construct(
        CentroRepository $centroRepo,
        UserRepository $userRepo,
        BrevoMailService $brevoService,
        MailConfigService $mailConfig,
        ConfiguracionRepository $configRepo,
    ) {
        $this->centroRepo = $centroRepo;
        $this->userRepo = $userRepo;
        $this->brevoService = $brevoService;
        $this->mailConfig = $mailConfig;
        $this->configRepo = $configRepo;
    }

    /**
     * GET /admin/centros
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado', 'pendientes');
        $centros = $this->centroRepo->listarConConteos(20, $estado);
        $totalPendientes = $this->centroRepo->contarPendientes();
        $totalActivos = $this->centroRepo->contarActivos();

        return view('admin.centros.index', compact('centros', 'estado', 'totalPendientes', 'totalActivos'));
    }

    /**
     * PUT /admin/centros/{centro}/validar
     */
    public function validar(Centro $centro)
    {
        $activado = $this->centroRepo->activar($centro);

        if (!$activado) {
            return back()->with('error', 'Este centro ya está activo.');
        }

        // Activar usuarios del centro
        $this->userRepo->activarUsuariosDeCentro($centro->id);

        // Notificar al centro que ha sido activado
        $this->notificarCentroActivado($centro);

        return redirect()->route('admin.centros.index')
            ->with('success', "Centro \"{$centro->nombre}\" validado exitosamente.");
    }

    /**
     * Enviar notificación por email al centro cuando es activado
     */
    private function notificarCentroActivado(Centro $centro): void
    {
        $userResponsable = $centro->usuarioResponsable;

        if (!$userResponsable || !$userResponsable->email) {
            return;
        }

        try {
            $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');

            if ($mailer === 'brevo') {
                $this->brevoService->enviarNotificacionCentroActivado($centro, $userResponsable);
            } else {
                $this->mailConfig->aplicarConfiguracion();
                Mail::to($userResponsable->email)->send(
                    new \App\Mail\CentroActivadoMail($centro, $userResponsable)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de centro activado: ' . $e->getMessage());
        }
    }
}
