<?php

namespace App\Services;

use App\Interfaces\DisponibiliteInterface as InterfacesDisponibiliteInterface;
use App\Models\Disponibilite;
use App\Models\Employe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class DisponibiliteService
{
    protected $repo;

    public function __construct(InterfacesDisponibiliteInterface $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        return $this->repo->all();
    }

    public function connectedDisponibiliteList()
    {
        $user = auth()->user();

        $employe = $user->employe;

        if (!$employe) {
            throw new \Exception("Aucun employé associé à l'utilisateur connecté.");
        }

        // Utilise l'id de l'employé pour récupérer ses disponibilités
        return Disponibilite::where('employe_id', $employe->id)->get();
    }


    // public function connectedDisponibiliteList()
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->employe) {
    //         return collect(); // ou throw une exception si besoin
    //     }

    //     $disponibilites = $this->repo->getByEmployeId($user->employe->id);

    //     if ($disponibilites->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Aucune disponibilité pour cet employé'
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Liste des disponibilites de l\'employé',
    //         'disponibilites' => $disponibilites
    //     ]);
    // }

    public function find(int $id)
    {
        return $this->repo->getById($id);
    }

    public function getByUser()
    {
        $user = Auth::user();

        if (!$user->employe) {
            return [];
        }

        return $this->repo->all()->where('employe_id', $user->employe->id)->values();
    }

    public function create(array $data)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $employe = $user->employe ?? Employe::find($data['employe_id'] ?? null);

        if (!$employe) {
            throw ValidationException::withMessages(['employe_id' => 'Employé introuvable.']);
        }

        // RH ne doit pas faire une demande pour lui-même via RH mode
        // if ($user->hasRole('rh') && isset($data['employe_id']) && $employe->user_id === $user->id) {
        //     throw ValidationException::withMessages(['employe_id' => 'Un RH ne peut pas se faire une demande via ce mode.']);
        // }

        $data['employe_id'] = $employe->id;
        $data['date_demande'] = now();
        $data['nombre_jours'] = $this->calculJoursOuvrables($data['date_debut'], $data['date_fin']);

        // LE BLOC SUIVANT EST A REVOIR POUR UNE DEDUCTION SUR LE SOLDE DE CONGE 
        // if ($data['avec_solde'] && $data['nombre_jours'] > 15) {
        //     throw ValidationException::withMessages(['avec_solde' => 'Vous ne pouvez pas dépasser 15 jours par an avec solde.']);
        // }

        if (isset($data['piece_jointe'])) {
            $data['piece_jointe'] = $data['piece_jointe']->store('disponibilites', 'public');
        }

        return $this->repo->store($data);
    }

    public function traiterDisponibilite(int $id, array $data)
    {
        $disponibilite = $this->repo->findOrFail($id);

        if ($disponibilite->statut !== 'en_attente') {
            throw new \Exception('Cette disponibilite a déjà été traitée.');
        }

        $decision = $data['decision'];
        $employe = $disponibilite->conge->employe;

        if ($decision === 'valide') {
            $dateDebut = Carbon::parse($data['date_debut']);
            $dateFin = Carbon::parse($data['date_fin']);
            $nbJours = $this->calculJoursOuvrables($dateDebut, $dateFin);

            // if ($employe->solde_conge_jours < $nbJours) {
            //     throw ValidationException::withMessages([
            //         'solde' => 'Le solde de congé est insuffisant.'
            //     ]);
            // }

            // Mise à jour
            $disponibilite->update([
                'statut' => 'valide',
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'nombre_jours' => $nbJours,
                'commentaire' => $data['commentaire'] ?? null,
                // 'fiche_cessation_pdf' => $this->uploadFichier($disponibilite)
            ]);

            // Déduction du solde
            // $employe->decrement('solde_conge_jours', $nbJours);
        } elseif ($decision === 'rejete') {
            if (empty($data['motif'])) {
                throw ValidationException::withMessages([
                    'motif' => 'Le motif de rejet est requis.'
                ]);
            }

            $disponibilite->update([
                'statut' => 'rejete',
                'motif' => $data['motif'],
                'commentaire' => $data['commentaire'] ?? null,
            ]);
        } else {
            throw ValidationException::withMessages([
                'decision' => 'Valeur invalide pour la décision. (valide ou rejete)'
            ]);
        }

        return $disponibilite;
    }

    //---------------------------------------- SPECIFICS METHODS------------------------------
    protected function calculJoursOuvrables($debut, $fin)
    {
        $debut = Carbon::parse($debut);
        $fin = Carbon::parse($fin);

        $joursFeries = []; // Jours fériés à créer en base prochainement.

        $nb = 0;
        while ($debut->lte($fin)) {
            if (!in_array($debut->dayOfWeek, [0, 6]) && !in_array($debut->toDateString(), $joursFeries)) {
                $nb++;
            }
            $debut->addDay();
        }

        return $nb;
    }
}
