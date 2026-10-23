<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Persistencia\ConfiguracionRepository;
use App\Validaciones\UpdateConfiguracionValidacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    private ConfiguracionRepository $configRepo;

    public function __construct(ConfiguracionRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * GET /api/admin/configuracion/mailjet
     * Obtener configuración de Mailjet
     */
    public function mailjet(Request $request): JsonResponse
    {
        $config = $this->configRepo->obtenerMailjet();

        // Ocultar valores secretos si no están configurados
        $datos = $config->map(function ($item) {
            $valor = $item->valor;
            if ($item->es_secreto && empty($valor)) {
                $valor = null;
            }
            return [
                'clave' => $item->clave,
                'valor' => $valor,
                'descripcion' => $item->descripcion,
                'es_secreto' => $item->es_secreto,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $datos,
            'configurado' => $this->configRepo->mailjetConfigurado(),
        ]);
    }

    /**
     * PUT /api/admin/configuracion/mailjet
     * Actualizar configuración de Mailjet
     */
    public function updateMailjet(UpdateConfiguracionValidacion $request): JsonResponse
    {
        $datos = $request->validated();

        $this->configRepo->actualizarMasiva($datos, $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Configuración de Mailjet actualizada exitosamente',
            'configurado' => $this->configRepo->mailjetConfigurado(),
        ]);
    }

    /**
     * POST /api/admin/configuracion/mailjet/test
     * Enviar email de prueba para verificar configuración
     */
    public function testMailjet(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        if (!$this->configRepo->correoConfigurado()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MAIL_NO_CONFIGURADO',
                    'message' => 'El driver de correo no está configurado. Guarde las credenciales primero.',
                ],
            ], 422);
        }

        try {
            $email = $validated['test_email'];
            $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');

            if ($mailer === 'brevo') {
                // Usar API REST de Brevo directamente
                $brevoService = app(\App\Services\BrevoMailService::class);
                $brevoService->enviarPrueba($email);

                return response()->json([
                    'success' => true,
                    'message' => "Email de prueba enviado a {$email} via Brevo API.",
                ]);
            }

            // Para otros drivers (mailjet, smtp, log), usar Mail:: como antes
            // Apply runtime config from DB before sending
            app(\App\Services\MailConfigService::class)->aplicarConfiguracion();

            \Illuminate\Support\Facades\Mail::raw(
                'Este es un email de prueba desde Reencuentro. Si lo recibes, Mailjet está configurado correctamente.',
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('[Reencuentro] Email de prueba');
                }
            );

            return response()->json([
                'success' => true,
                'message' => "Email de prueba enviado a {$email}",
            ]);
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            $errorMsg = $e->getMessage();
            try {
                $response = $e->getResponse();
                if ($response) {
                    $errorMsg .= ' | Response: ' . $response->toString();
                }
            } catch (\Throwable $inner) {}
            \Log::error('Mailjet test email failed (API): ' . $errorMsg);
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SMTP_TRANSPORT_ERROR',
                    'message' => 'Error SMTP al enviar email: ' . $errorMsg,
                ],
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Mailjet test email failed (API): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MAIL_ERROR',
                    'message' => 'Error al enviar email: ' . $e->getMessage(),
                ],
            ], 500);
        }
    }
}
