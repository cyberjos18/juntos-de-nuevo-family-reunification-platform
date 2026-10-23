<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Persistencia\ConfiguracionRepository;
use App\Services\MailConfigService;
use App\Validaciones\UpdateConfiguracionValidacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ConfiguracionController extends Controller
{
    private MailConfigService $mailConfig;
    private ConfiguracionRepository $configRepo;

    public function __construct(MailConfigService $mailConfig, ConfiguracionRepository $configRepo)
    {
        $this->mailConfig = $mailConfig;
        $this->configRepo = $configRepo;
    }

    /**
     * GET /admin/configuracion/mailjet
     */
    public function mailjet()
    {
        $config = $this->configRepo->obtenerMailjetPorClave();
        $configurado = $this->configRepo->correoConfigurado();

        return view('admin.configuracion.mailjet', compact('config', 'configurado'));
    }

    /**
     * PUT /admin/configuracion/mailjet
     */
    public function updateMailjet(UpdateConfiguracionValidacion $request)
    {
        $this->configRepo->actualizarMasiva(
            $request->validated(),
            $request->user()->id
        );

        return redirect()->route('admin.configuracion.mailjet')
            ->with('success', 'Configuración de email actualizada correctamente.');
    }

    /**
     * POST /admin/configuracion/mailjet/test
     */
    public function testMailjet(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        if (!$this->configRepo->correoConfigurado()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => ['message' => 'El driver de correo no está configurado. Guarde las credenciales primero.'],
                ], 422);
            }
            return back()->with('error', 'El driver de correo no está configurado. Guarde las credenciales primero.');
        }

        try {
            $emailDestino = $validated['test_email'];
            $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');

            if ($mailer === 'brevo') {
                // Usar API REST de Brevo directamente
                $brevoService = app(\App\Services\BrevoMailService::class);
                $result = $brevoService->enviarPrueba($emailDestino);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Email de prueba enviado a {$emailDestino} via Brevo API.",
                    ]);
                }
                return back()->with('success', "Email de prueba enviado a {$emailDestino} via Brevo API.");
            }

            // Para otros drivers (mailjet, smtp, log), usar Mail:: como antes
            $this->mailConfig->aplicarConfiguracion();

            Mail::raw('Este es un email de prueba del sistema Juntos de Nuevo. Si lo recibió, la configuración de Mailjet es correcta.', function ($message) use ($emailDestino) {
                $message->to($emailDestino)
                    ->subject('Juntos de Nuevo - Email de prueba');
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Email de prueba enviado a {$emailDestino}.",
                ]);
            }
            return back()->with('success', "Email de prueba enviado a {$emailDestino}.");
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            $errorMsg = $e->getMessage();
            try {
                $response = $e->getResponse();
                if ($response) {
                    $errorMsg .= ' | Response: ' . $response->toString();
                }
            } catch (\Throwable $inner) {}
            \Log::error('Mailjet test email failed (Web): ' . $errorMsg);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => ['message' => 'Error al enviar email: ' . $errorMsg],
                ], 500);
            }
            return back()->with('error', 'Error al enviar email (SMTP): ' . $errorMsg);
        } catch (\Exception $e) {
            \Log::error('Mailjet test email failed (Web): ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => ['message' => 'Error al enviar email: ' . $e->getMessage()],
                ], 500);
            }
            return back()->with('error', 'Error al enviar email: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin/configuracion/registro-centros
     */
    public function registroCentros()
    {
        $habilitado = $this->configRepo->registroCentroHabilitado();

        return view('admin.configuracion.registro-centros', compact('habilitado'));
    }

    /**
     * PUT /admin/configuracion/registro-centros
     */
    public function actualizarRegistroCentros(Request $request)
    {
        $request->validate([
            'habilitado' => 'required|in:0,1',
        ]);

        $this->configRepo->establecerRegistroCentro(
            $request->habilitado === '1',
            $request->user()->id
        );

        return redirect()->route('admin.configuracion.registro-centros')
            ->with('success', $request->habilitado === '1'
                ? 'Registro de centros habilitado correctamente.'
                : 'Registro de centros deshabilitado correctamente.'
            );
    }
}
