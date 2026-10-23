<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivarCentroMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombreCentro;
    public string $nombreUsuario;
    public string $tokenActivacion;
    public string $urlActivacion;

    /**
     * Create a new message instance.
     */
    public function __construct(string $nombreCentro, string $nombreUsuario, string $tokenActivacion)
    {
        $this->nombreCentro = $nombreCentro;
        $this->nombreUsuario = $nombreUsuario;
        $this->tokenActivacion = $tokenActivacion;
        $this->urlActivacion = url('/activar/' . $tokenActivacion);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Juntos de Nuevo] Activa tu cuenta - ' . $this->nombreCentro,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activar-centro',
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
