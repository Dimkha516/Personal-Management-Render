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
            return response()->json([
                'message' => 'Employé non trouvé pour l’utilisateur connecté.'
            ], 404);
        }

        $conges = $this->congeRepo->getByEmployeId($user->employe->id);

        return $conges;
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
        $employe = $user->employe;

        // $employe = Employe::findOrFail($data['employe_id']);

        $ancienneteMois = now()->diffInMonths($employe->date_prise_service);
        if ($ancienneteMois < 12) {
            throw ValidationException::withMessages([
                'employe_id' => 'Vous devez avoir au moins 1 an de service pour faire une demande. Sinon, demandez au RH.'
            ]);
        }

        $data['employe_id'] = $employe->id;
        $data['date_demande'] = now();

        return $this->congeRepo->store($data);

        // // RH → demande pour un autre employé
        // if ($user->hasRole('rh')) {
        //     // Vérification de la clé fournie
        //     if (!isset($data['employe_id'])) {
        //         throw ValidationException::withMessages([
        //             'employe_id' => 'L\'employé concerné doit être spécifié pour une demande RH.'
        //         ]);
        //     }

        //     $employe = Employe::findOrFail($data['employe_id']);

        //     // S'assurer que RH ne demande pas pour lui-même
        //     if ($employe->user_id === $user->id) {
        //         throw ValidationException::withMessages([
        //             'employe_id' => 'Un RH ne peut pas faire une demande pour lui-même via ce mode.'
        //         ]);
        //     }
        // } else {
        //     // Employé → on prend automatiquement son employe_id
        //     $employe = $user->employe;
        //     // dd($employe);

        //     // Ajoute automatiquement l'employé connecté dans les données
        //     $data['employe_id'] = $employe->id;
        // }

        // Vérifier ancienneté

        // if ($ancienneteMois < 12 && !$user->hasRole('rh')) {
        //     throw ValidationException::withMessages([
        //         'employe_id' => 'Vous devez avoir au moins 1 an de service pour faire une demande. Sinon, demandez au RH.'
        //     ]);
        // }
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

    //------------------ TRAITER DEMANDE CONGE: VALIDER OU REJETER----------------------------
    public function traiterDemande(int $id, array $data)
    {
        $conge = $this->congeRepo->getById($id);

        if ($conge->statut !== 'en_attente') {
            throw ValidationException::withMessages([
                'statut' => 'Cette demande a déjà été traitée.'
            ]);
        }

        if ($data['decision'] === 'valide') {
            // Mise à jour du statut uniquement
            $conge->update([
                'statut' => 'approuve',
                'commentaire' => $data['commentaire'] ?? null,
            ]);
        } elseif ($data['decision'] === 'rejete') {
            if (empty($data['motif'])) {
                throw ValidationException::withMessages([
                    'motif' => 'Le motif du rejet est requis.'
                ]);
            }

            $conge->update([
                'statut' => 'refuse',
                'motif' => $data['motif'],
                'commentaire' => $data['commentaire'] ?? null,
            ]);
        } else {
            throw ValidationException::withMessages([
                'decision' => 'La décision doit être "valide" ou "rejete".'
            ]);
        }

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
