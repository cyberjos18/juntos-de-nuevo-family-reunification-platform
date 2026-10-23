<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ActivarCentroMail;
use App\Persistencia\CentroRepository;
use App\Persistencia\ConfiguracionRepository;
use App\Persistencia\UserRepository;
use App\Services\MailConfigService;
use App\Services\BrevoMailService;
use App\Validaciones\RegisterCentroValidacion;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    private UserRepository $userRepo;
    private CentroRepository $centroRepo;
    private MailConfigService $mailConfig;
    private ConfiguracionRepository $configRepo;
    private BrevoMailService $brevoService;

    public function __construct(
        UserRepository $userRepo,
        CentroRepository $centroRepo,
        MailConfigService $mailConfig,
        ConfiguracionRepository $configRepo,
        BrevoMailService $brevoService,
    ) {
        $this->userRepo = $userRepo;
        $this->centroRepo = $centroRepo;
        $this->mailConfig = $mailConfig;
        $this->configRepo = $configRepo;
        $this->brevoService = $brevoService;
    }

    /**
     * GET /registro — Mostrar formulario de registro
     */
    public function showRegistrationForm()
    {
        if (!$this->configRepo->registroCentroHabilitado()) {
            return view('auth.register-disabled');
        }

        return view('auth.register');
    }

    /**
     * POST /registro — Procesar registro
     */
    public function register(RegisterCentroValidacion $request)
    {
        if (!$this->configRepo->registroCentroHabilitado()) {
            return redirect()->route('login')
                ->with('error', 'El registro de nuevos centros está temporalmente deshabilitado por el administrador.');
        }

        // Crear centro
        $centro = $this->centroRepo->crear([
            'nombre' => $request->nombre_centro,
            'tipo' => $request->tipo_centro,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        // Subir foto de identificación
        $fotoPath = null;
        if ($request->hasFile('foto_identificacion')) {
            $fotoPath = $request->file('foto_identificacion')->store('identificaciones', 'public');
        }

        // Crear usuario gestor
        $user = $this->userRepo->crearGestor(
            centroId: $centro->id,
            nombre: $request->nombre,
            email: $request->email,
            password: $request->password,
            cargo: $request->cargo,
            fotoIdentificacion: $fotoPath,
        );
        
        // Log para depuración del token
        \Log::info('Usuario gestor creado', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token_activacion' => $user->token_activacion,
            'token_length' => strlen($user->token_activacion),
        ]);

        // Enviar email de activación
        try {
            $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');
            
            \Log::info('Enviando email de activación', [
                'mailer' => $mailer,
                'email_destino' => $user->email,
                'token_enviado' => substr($user->token_activacion, 0, 10) . '...',
            ]);

            if ($mailer === 'brevo') {
                // Usar API REST de Brevo directamente
                $brevoService = app(\App\Services\BrevoMailService::class);
                $urlActivacion = url('/activar/' . $user->token_activacion);
                
                \Log::info('URL de activación para Brevo', ['url' => $urlActivacion]);
                
                $brevoService->enviarConVista(
                    $user->email,
                    '[Juntos de Nuevo] Activa tu cuenta - ' . $centro->nombre,
                    'emails.activar-centro',
                    [
                        'nombreCentro' => $centro->nombre,
                        'nombreUsuario' => $user->nombre,
                        'tokenActivacion' => $user->token_activacion,
                        'urlActivacion' => $urlActivacion,
                    ]
                );
            } else {
                $this->mailConfig->aplicarConfiguracion();
                Mail::to($user->email)->send(
                    new ActivarCentroMail($centro->nombre, $user->nombre, $user->token_activacion)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando email de activación: ' . $e->getMessage());
        }

        // Notificar a los admins sobre el nuevo centro pendiente
        $this->notificarAdminsNuevoCentro($centro, $user);

        return redirect()->route('login')
            ->with('success', 'Tu centro aún no ha sido activado. Espere mientras es aprobado.');
    }

    /**
     * Enviar notificación por email a todos los admins cuando se registra un nuevo centro
     */
    private function notificarAdminsNuevoCentro($centro, $user): void
    {
        $admins = $this->userRepo->obtenerAdmins();

        if ($admins->isEmpty()) {
            return;
        }

        try {
            $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');

            foreach ($admins as $admin) {
                if ($mailer === 'brevo') {
                    $this->brevoService->enviarNotificacionAdminCentroPendiente($centro, $admin);
                } else {
                    $this->mailConfig->aplicarConfiguracion();
                    Mail::to($admin->email)->send(
                        new \App\Mail\NuevoCentroPendienteMail($centro, $user, $admin)
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de nuevo centro a admins: ' . $e->getMessage());
        }
    }
}
