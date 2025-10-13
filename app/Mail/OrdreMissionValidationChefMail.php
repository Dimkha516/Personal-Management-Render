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

class OrdreMissionValidationChefMail extends Mailable
{
    use Queueable, SerializesModels;

    public $demandeur;
    public $ordreMission;
    public $chefService;

    /**
     * Create a new message instance.
     */
    public function __construct(OrdreMission $ordreMission, Employe $chefService)
    {
        $this->ordreMission = $ordreMission;
        $this->demandeur = $ordreMission->demandeur;
        $this->chefService = $chefService;
    }

    public function build()
    {
        $nomChefService = $this->chefService->nom;
        $prenomChefService = $this->chefService->prenom;
        $nomDemandeur = $this->demandeur->nom;
        $prenomDemandeur = $this->demandeur->prenom;
        $destination = $this->ordreMission->destination;
        $dateDepart = $this->ordreMission->date_depart;
        $dateDebut = $this->ordreMission->date_debut;
        $dateFin = $this->ordreMission->date_fin;
        $nombreJours = $this->ordreMission->nb_jours;


        return $this->subject("Ordre de mission approuvé par le Chef de Service")
            ->html("
                <h2>Bonjour, </h2>
                <p>Le chef de service <strong>{$nomChefService} {$prenomChefService}</strong></p>
                <p>vient d'approuver une demande d'ordre de mission par l'agent</p>
                <p>{$prenomDemandeur} {$nomDemandeur}</p>
                <p>Destination: {$destination}</p>
                <p>date départ: {$dateDepart}</p>
                <p>date début: {$dateDebut}</p>
                <p>date de fin: {$dateFin}</p>
                <p>Nombre de jours: {$nombreJours}</p>
                <p><strong>Veuillez vous connecter et procéder au traitement de l'OM</strong></p>
                <p>Merci.</p>
            ");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ordre de mission approuvé par le Chef de Service',
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
