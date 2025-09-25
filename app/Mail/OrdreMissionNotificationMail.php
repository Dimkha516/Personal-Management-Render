<?php

namespace App\Mail;

use App\Models\Employe;
use App\Models\OrdreMission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdreMissionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $demandeur;
    public $ordreMission;

    /**
     * Create a new message instance.
     */
    public function __construct(Employe $demandeur, OrdreMission $ordreMission)
    {
        $this->demandeur = $demandeur;
        $this->ordreMission = $ordreMission;
    }

    public function build()
    {

        $nomDemandeur = $this->demandeur->nom;
        $prenomDemandeur = $this->demandeur->prenom;
        $destination = $this->ordreMission->destination;
        $dateDepart = $this->ordreMission->date_depart;
        $dateDebut = $this->ordreMission->date_debut;
        $dateFin = $this->ordreMission->date_fin;
        $nombreJours = $this->ordreMission->nb_jours;



        return $this->subject("Nouvelle demande d'ordre de mission")
            ->html("
                <h2>Bonjour cher(e) chef de service, </h2>
                <p>Une nouvelle demande d'ordre de mission a été soumise.</p>
                <p>Demandeur: <strong>{$prenomDemandeur} {$nomDemandeur}</strong></p>
                <p>Destination: <strong>{$destination}</strong></p>
                <p>Date départ: <strong>{$dateDepart}</strong></p>
                <p>Date début mission: <strong>{$dateDebut}</strong></p>
                <p>Date fin de mission: <strong>{$dateFin}</strong></p>
                <p>Nombre de jours: <strong>{$nombreJours}</strong></p>

                <p>Merci de vous connecter pour approuver ou rejeter cette demande</p>
            ");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvelle demande d'ordre de mission",
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
