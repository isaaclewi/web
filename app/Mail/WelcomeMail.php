<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{
     *   prenom:       string,
     *   nom:          string,
     *   email:        string,
     *   matricule:    string,
     *   role:         string,
     *   password_raw: string,
     *   institution:  string,
     *   app_url:      string,
     * } $payload
     */
    public function __construct(public readonly array $payload) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bienvenue — Vos identifiants de connexion ({$this->payload['institution']})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}