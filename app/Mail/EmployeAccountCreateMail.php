<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeAccountCreateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetLink;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $resetLink)
    {
        $this->user = $user;
        $this->resetLink = $resetLink;
    }


    public function build()
    {
        return $this->subject('Activation de votre compte utilisateur')
            ->html("
            <h2>Bonjour cher(e) <strong>{$this->user->nom}</strong></h2>
            <p>Votre compte a été crée avec l'email : <strong>{$this->user->email}</strong></p>
            <p>Veuillez cliquer sur le lien suivant pour définir votre mot de passe pour pouvoir accèder à la plateforme RH Management : </p>
            <a href='{$this->resetLink}'>Modifier mon mot de passe</a>
            <p>Ce lien expirera dans 60 minutes.</p>
        ");
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Employe Account Create Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
