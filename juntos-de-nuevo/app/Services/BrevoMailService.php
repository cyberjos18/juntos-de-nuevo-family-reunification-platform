<?php

namespace App\Services;

use App\Persistencia\ConfiguracionRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class BrevoMailService
{
    private ConfiguracionRepository $configRepo;

    public function __construct(ConfiguracionRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * Enviar correo via API REST de Brevo
     */
    public function enviar(string $toEmail, string $subject, string $htmlContent, ?string $textContent = null): array
    {
        $apiKey = $this->configRepo->obtenerValor('BREVO_API_KEY');
        $fromAddress = $this->configRepo->obtenerValor('MAIL_FROM_ADDRESS', 'noreply@juntosdenuevo.org');
        $fromName = $this->configRepo->obtenerValor('MAIL_FROM_NAME', 'Juntos de Nuevo');

        if (empty($apiKey)) {
            throw new \Exception('Brevo API Key no configurada');
        }

        $payload = [
            'sender' => [
                'name' => $fromName,
                'email' => $fromAddress,
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toEmail],
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ];

        if ($textContent) {
            $payload['textContent'] = $textContent;
        }

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withOptions([
            'verify' => false,
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'messageId' => $response->json('messageId'),
            ];
        }

        $error = $response->json();
        Log::error('Brevo API error: ' . json_encode($error));

        throw new \Exception(
            'Error Brevo API: ' . ($error['message'] ?? $response->body())
        );
    }

    /**
     * Enviar correo usando una vista Blade renderizada como HTML
     */
    public function enviarConVista(string $toEmail, string $subject, string $viewName, array $viewData = []): array
    {
        $htmlContent = View::make($viewName, $viewData)->render();

        return $this->enviar($toEmail, $subject, $htmlContent);
    }

    /**
     * Enviar correo de prueba
     */
    public function enviarPrueba(string $toEmail): array
    {
        return $this->enviar(
            $toEmail,
            'Juntos de Nuevo - Email de prueba',
            '<h2>Juntos de Nuevo</h2><p>Este es un email de prueba desde el sistema Juntos de Nuevo. Si lo recibes, la configuración de Brevo es correcta.</p>',
            'Este es un email de prueba desde Juntos de Nuevo. Si lo recibes, la configuración de Brevo es correcta.'
        );
    }

    /**
     * Enviar notificación a admin sobre nuevo centro pendiente de aprobación
     */
    public function enviarNotificacionAdminCentroPendiente($centro, $admin): array
    {
        $urlPendientes = url('/admin/centros?estado=pendientes');

        return $this->enviarConVista(
            $admin->email,
            '[Juntos de Nuevo] Nuevo centro pendiente de aprobación: ' . $centro->nombre,
            'emails.admin.nuevo-centro-pendiente',
            [
                'nombreAdmin' => $admin->nombre,
                'nombreCentro' => $centro->nombre,
                'tipoCentro' => $centro->tipo,
                'emailResponsable' => $centro->email,
                'telefonoCentro' => $centro->telefono ?? 'No proporcionado',
                'direccionCentro' => $centro->direccion ?? 'No proporcionada',
                'fechaRegistro' => $centro->created_at->format('d/m/Y H:i'),
                'urlPendientes' => $urlPendientes,
            ]
        );
    }

    /**
     * Enviar notificación al centro cuando ha sido activado
     */
    public function enviarNotificacionCentroActivado($centro, $user): array
    {
        $urlLogin = url('/login');

        return $this->enviarConVista(
            $user->email,
            '[Juntos de Nuevo] Tu centro ha sido activado',
            'emails.centro.centro-activado',
            [
                'nombreUsuario' => $user->nombre,
                'nombreCentro' => $centro->nombre,
                'urlLogin' => $urlLogin,
            ]
        );
    }
}
