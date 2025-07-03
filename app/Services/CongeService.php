<?php

namespace App\Services;

use App\Interfaces\CongesInterface;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;



class CongeService
{
    protected $congeRepo;

    public function __construct(CongesInterface $congeRepo)
    {
        $this->congeRepo = $congeRepo;
    }

    //--------------------------------------- TOUTS LES CONGES
    public function list()
    {
        return $this->congeRepo->getAll();
    }
    //--------------------------------------- CONGES DE L'UTILISATEUR CONNECTE

    public function connectedUserCongeList()
    {
        $user = Auth::user();

        if (!$user || !$user->employe) {
            return collect(); // ou throw une exception si besoin
        }

        return $this->congeRepo->getById($user->employe->id);
    }


    //--------------------------------------- UN CONGE AVEC SON ID

    public function find(int $id)
    {
        return $this->congeRepo->getById($id);
    }

    //--------------------------------------- AJOUTER UNE NOUVELLE DEMANDE CONGE
    public function create(array $data)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // RH → demande pour un autre employé
        if ($user->hasRole('rh')) {
            // Vérification de la clé fournie
            if (!isset($data['employe_id'])) {
                throw ValidationException::withMessages([
                    'employe_id' => 'L\'employé concerné doit être spécifié pour une demande RH.'
                ]);
            }

            $employe = Employe::findOrFail($data['employe_id']);

            // S'assurer que RH ne demande pas pour lui-même
            if ($employe->user_id === $user->id) {
                throw ValidationException::withMessages([
                    'employe_id' => 'Un RH ne peut pas faire une demande pour lui-même via ce mode.'
                ]);
            }
        } else {
            // Employé → on prend automatiquement son employe_id
            $employe = $user->employe;
            // dd($employe);

            // Ajoute automatiquement l'employé connecté dans les données
            $data['employe_id'] = $employe->id;
        }

        // Vérifier ancienneté
        $ancienneteMois = now()->diffInMonths($employe->date_prise_service);
        if ($ancienneteMois < 12 && !$user->hasRole('rh')) {
            throw ValidationException::withMessages([
                'employe_id' => 'Vous devez avoir au moins 1 an de service pour faire une demande. Sinon, demandez au RH.'
            ]);
        }

        $data['date_demande'] = now();

        return $this->congeRepo->store($data);
    }


    //--------------------------------------- METTRE A JOUR UN CONGE
    public function update(int $id, array $data)
    {
        return $this->congeRepo->update($id, $data);
    }


    //--------------------------------------- SUPPRIMER UN CONGE
    public function delete(int $id)
    {
        return $this->congeRepo->destroy($id);
    }

    //--------------------------------------- VALIDER UN CONGE

    public function valider(int $id, array $data)
    {
        $conge = $this->congeRepo->getById($id);
        // $conge = $this->congeRepo->findOrFail($id); // retourne un seul modèle


        if ($conge->statut !== 'en_attente') {
            throw ValidationException::withMessages(['statut' => 'Cette demande a déjà été traitée.']);
        }

        // Calcul des jours ouvrables réels (hors jours fériés)
        // $dateDebut = Carbon::parse($data['date_debut']);
        // $dateFin = Carbon::parse($data['date_fin']);

        // $joursOuvrables = $this->calculJoursOuvrables($dateDebut, $dateFin);

        $employe = $conge->employe;

        // Vérification du solde:
        // if ($employe->solde_conge_jours < $joursOuvrables) {
        //     throw ValidationException::withMessages([
        //         'solde' => 'Le solde de congé de cet employé est insiffisant pour cette demande'
        //     ]);
        // }

        // Mise à jour de la demande:
        $conge->update([
            // 'date_debut' => $dateDebut,
            // 'date_fin' => $dateFin,
            // 'nb_jours' => $joursOuvrables,
            'statut' => 'approuve',
            'commentaire' => $data['commentaire'] ?? null,
            // 'fichier_validation' => $this->genererPdfNote($conge),
        ]);

        // Déduction du solde employé:
        // $employe->decrement('solde_conge_jours', $joursOuvrables);

        return $conge;
    }

    public function rejectDemande(int $id, array $data)
    {
        $conge = $this->congeRepo->getById($id);
  
        if ($conge->statut !== 'en_attente') {
            throw ValidationException::withMessages([
                'statut' => 'Seules les demandes en attente peuvent être rejetées.'
            ]);
        }

        $conge->update([
            'statut' => 'refuse',
            'motif' => $data['motif']
        ]);

        return $conge;
    }



    //----------------------------SPECIFIC METHODES-SERVICES----------------------------------
    public function calculJoursOuvrables(Carbon $debut, Carbon $fin): int
    {
        $joursFeries = [
            '2025-01-01',
            '2025-04-04',
            // + jours fériés mobiles gérés ailleurs
        ];

        $jours = 0;
        while ($debut->lte($fin)) {
            // Exclure samedi(6), dimanche(0) et jours fériés
            if (!in_array($debut->dayOfWeek, [0, 6]) && !in_array($debut->toDateString(), $joursFeries)) {
                $jours++;
            }
            $debut->addDay();
        }

        return $jours;
    }

    // public function genererPdfNote($conge): string
    // {
    //     $pdf = PDF::loadView('pdfs.note_conge', ['conge' => $conge]);

    //     $filename = 'note_conge_' . $conge->id . '.pdf';

    //     Storage::put("notes_conges/{$filename}", $pdf->output());

    //     return "notes_conges/{$filename}";
    // }
}
