<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CentroActivadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $centro;
    public $user;
    public string $nombreUsuario;
    public string $nombreCentro;
    public string $urlLogin;

    /**
     * Create a new message instance.
     */
    public function __construct($centro, $user)
    {
        $this->centro = $centro;
        $this->user = $user;
        $this->nombreUsuario = $user->nombre;
        $this->nombreCentro = $centro->nombre;
        $this->urlLogin = url('/login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Juntos de Nuevo] Tu centro ha sido activado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.centro.centro-activado',
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
