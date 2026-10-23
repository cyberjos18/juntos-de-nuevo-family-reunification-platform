<?php

namespace App\Persistencia;

use App\Models\Configuracion;
use Illuminate\Support\Collection;

class ConfiguracionRepository
{
    /**
     * Listar configuración por grupo
     */
    public function listarPorGrupo(string $grupo): Collection
    {
        return Configuracion::grupo($grupo)->get();
    }

    /**
     * Obtener configuración de Mailjet
     */
    public function obtenerMailjet(): Collection
    {
        return Configuracion::mailjet()->get();
    }

    /**
     * Obtener valor por clave
     */
    public function obtenerValor(string $clave, $default = null)
    {
        return Configuracion::obtener($clave, $default);
    }

    /**
     * Actualizar configuración masiva
     */
    public function actualizarMasiva(array $datos, int $usuarioId): void
    {
        foreach ($datos as $clave => $valor) {
            Configuracion::establecer($clave, $valor, $usuarioId);
        }
    }

    /**
     * Obtener configuración de Mailjet indexada por clave
     */
    public function obtenerMailjetPorClave(): Collection
    {
        return Configuracion::mailjet()->get()->keyBy('clave');
    }

    /**
     * Verificar si Mailjet está configurado
     */
    public function mailjetConfigurado(): bool
    {
        $apiKey = $this->obtenerValor('MAILJET_API_KEY');
        $secretKey = $this->obtenerValor('MAILJET_SECRET_KEY');

        return !empty($apiKey) && !empty($secretKey);
    }

    /**
     * Verificar si el driver de correo activo está configurado
     */
    public function correoConfigurado(): bool
    {
        $mailer = $this->obtenerValor('MAIL_MAILER', 'log');

        return match ($mailer) {
            'mailjet' => $this->mailjetConfigurado(),
            'brevo' => !empty($this->obtenerValor('BREVO_API_KEY')),
            'smtp' => !empty($this->obtenerValor('MAIL_HOST')),
            default => true, // 'log' siempre está configurado
        };
    }

    /**
     * Verificar si el registro de centros está habilitado
     */
    public function registroCentroHabilitado(): bool
    {
        $valor = $this->obtenerValor('HABILITAR_REGISTRO_CENTRO', '1');
        return (int) $valor === 1;
    }

    /**
     * Establecer si el registro de centros está habilitado
     */
    public function establecerRegistroCentro(bool $habilitado, int $usuarioId): void
    {
        Configuracion::establecer(
            'HABILITAR_REGISTRO_CENTRO',
            $habilitado ? '1' : '0',
            $usuarioId
        );
    }
}
