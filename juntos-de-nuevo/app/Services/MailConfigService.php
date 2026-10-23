<?php

namespace App\Services;

use App\Persistencia\ConfiguracionRepository;
use Illuminate\Support\Facades\Config;

class MailConfigService
{
    private ConfiguracionRepository $configRepo;

    public function __construct(ConfiguracionRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * Aplicar configuración de mail desde la base de datos
     * Sobrescribe la configuración de Laravel en runtime
     */
    public function aplicarConfiguracion(): void
    {
        $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');
        $host = $this->configRepo->obtenerValor('MAIL_HOST', 'in-v3.mailjet.com');
        $port = $this->configRepo->obtenerValor('MAIL_PORT', '587');
        $fromAddress = $this->configRepo->obtenerValor('MAIL_FROM_ADDRESS', 'noreply@findchild.org');
        $fromName = $this->configRepo->obtenerValor('MAIL_FROM_NAME', 'Reencuentro');

        Config::set('mail.default', $mailer);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);

        if ($mailer === 'smtp') {
            $apiKey = $this->configRepo->obtenerValor('MAILJET_API_KEY');
            $secretKey = $this->configRepo->obtenerValor('MAILJET_SECRET_KEY');

            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $host,
                'port' => (int) $port,
                'encryption' => 'tls',
                'username' => $apiKey,
                'password' => $secretKey,
                'timeout' => null,
            ]);
        } elseif ($mailer === 'mailjet') {
            $apiKey = $this->configRepo->obtenerValor('MAILJET_API_KEY');
            $secretKey = $this->configRepo->obtenerValor('MAILJET_SECRET_KEY');

            // Usar SMTP con credenciales de Mailjet (API Key = username, Secret Key = password)
            // Esto evita necesitar el paquete symfony/mailjet-mailer
            Config::set('mail.mailers.mailjet', [
                'transport'  => 'smtp',
                'host'       => 'in-v3.mailjet.com',
                'port'       => 587,
                'encryption' => 'tls',
                'username'   => $apiKey,
                'password'   => $secretKey,
                'timeout'    => null,
            ]);
        } elseif ($mailer === 'brevo') {
            // Brevo se maneja via BrevoMailService (API REST directa)
            // No configurar SMTP - usar log como fallback para Mail::
            Config::set('mail.default', 'log');
        }
    }
}
