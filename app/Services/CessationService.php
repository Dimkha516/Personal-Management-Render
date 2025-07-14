<?php

namespace App\Services;

use App\Interfaces\CessationInterface;
use App\Models\Conge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CessationService
{
    protected $cessationRepository;

    public function __construct(CessationInterface $cessationRepository)
    {
        $this->cessationRepository = $cessationRepository;
    }

    public function list()
    {
        return $this->cessationRepository->all();
    }

    // public function connectedUserCessationsList()
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->employe) {
    //         return collect(); // ou throw une exception si besoin
    //     }
    //     $cessations = $this->cessationRepository->getById($user->employe->id);

    //     if (!$cessations) {
    //         return response()->json([
    //             'message' => 'Aucune cessation pour cet employé'
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'liste cessations employé',
    //         'cessations' => $cessations
    //     ]);
    //     // return $this->cessationRepository->getById($user->employe->id);
    // }
    public function connectedUserCessationsList()
    {
        $user = Auth::user();

        if (!$user || !$user->employe) {
            return collect(); // ou throw une exception si besoin
        }

        $cessations = $this->cessationRepository->getByEmployeId($user->employe->id);

        if ($cessations->isEmpty()) {
            return response()->json([
                'message' => 'Aucune cessation pour cet employé'
            ]);
        }

        return response()->json([
            'message' => 'Liste des cessations de l\'employé',
            'cessations' => $cessations
        ]);
    }


    public function find(int $id)
    {
        return $this->cessationRepository->getById($id);
    }


    public function create(array $data)
    {

        $user = Auth::user();

        $conge = Conge::findOrFail($data['conge_id']);

        if ($conge->statut !== 'approuve') {

            throw new \Exception('le congé doit être validé pour soumettre une cessation');
        }

        if (isset($data['piece_jointe'])) {
            $path = $data['piece_jointe']->store('cessations_pieces');
            $data['piece_jointe'] = $path;
        }


        return $this->cessationRepository->store([
            'conge_id' => $conge->id,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'motif' => $data['motif']
            // 'piece_jointe' => $this->uploadFichier($data['piece_jointe']),
        ]);
    }

    public function traiterCessation(int $id, array $data)
    {
        $cessation = $this->cessationRepository->findOrFail($id);

        if ($cessation->statut !== 'en_attente') {
            throw new \Exception('Cette cessation a déjà été traitée.');
        }

        $decision = $data['decision'];
        $employe = $cessation->conge->employe;

        if ($decision === 'valide') {
            $dateDebut = Carbon::parse($data['date_debut']);
            $dateFin = Carbon::parse($data['date_fin']);
            $nbJours = $this->calculJoursOuvrables($dateDebut, $dateFin);

            if ($employe->solde_conge_jours < $nbJours) {
                throw ValidationException::withMessages([
                    'solde' => 'Le solde de congé est insuffisant.'
                ]);
            }

            // Mise à jour
            $cessation->update([
                'statut' => 'valide',
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'nombre_jours' => $nbJours,
                'commentaire' => $data['commentaire'] ?? null,
                // 'fiche_cessation_pdf' => $this->uploadFichier($cessation)
            ]);

            // Déduction du solde
            $employe->decrement('solde_conge_jours', $nbJours);
        } elseif ($decision === 'rejete') {
            if (empty($data['motif'])) {
                throw ValidationException::withMessages([
                    'motif' => 'Le motif de rejet est requis.'
                ]);
            }

            $cessation->update([
                'statut' => 'rejete',
                'motif' => $data['motif'],
                'commentaire' => $data['commentaire'] ?? null,
            ]);
        } else {
            throw ValidationException::withMessages([
                'decision' => 'Valeur invalide pour la décision. (valide ou rejete)'
            ]);
        }

        return $cessation;
    }




    //----------------------------SPECIFIC METHODES-SERVICES----------------------------------
    protected function calculJoursOuvrables($debut, $fin): int
    {
        $joursFeries = [
            '2025-01-01',
            '2025-04-04',
        ]; // à définir dans une table prochainement

        $nb = 0;
        while ($debut->lte($fin)) {
            if (!in_array($debut->dayOfWeek, [0, 6]) && !in_array($debut->toDateString(), $joursFeries)) {
                $nb++;
            }
            $debut->addDay();
        }

        return $nb;
    }

    protected function uploadFichier($file): string
    {
        return $file->store('cessations', 'public');
    }
}
