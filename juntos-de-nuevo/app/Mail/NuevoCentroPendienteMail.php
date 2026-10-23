<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevoCentroPendienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $centro;
    public $user;
    public string $nombreAdmin;
    public string $nombreCentro;
    public string $tipoCentro;
    public string $emailResponsable;
    public string $telefonoCentro;
    public string $direccionCentro;
    public string $fechaRegistro;
    public string $urlPendientes;

    /**
     * Create a new message instance.
     */
    public function __construct($centro, $user, $admin = null)
    {
        $this->centro = $centro;
        $this->user = $user;
        $this->nombreAdmin = $admin?->nombre ?? 'Administrador';
        $this->nombreCentro = $centro->nombre;
        $this->tipoCentro = $centro->tipo;
        $this->emailResponsable = $centro->email;
        $this->telefonoCentro = $centro->telefono ?? 'No proporcionado';
        $this->direccionCentro = $centro->direccion ?? 'No proporcionada';
        $this->fechaRegistro = $centro->created_at->format('d/m/Y H:i');
        $this->urlPendientes = url('/admin/centros?estado=pendientes');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Juntos de Nuevo] Nuevo centro pendiente de aprobación: ' . $this->nombreCentro,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.nuevo-centro-pendiente',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
