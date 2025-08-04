<?php

namespace App\Services;

use App\Interfaces\CessationInterface;
use App\Models\Conge;
use App\Models\Employe;
use App\Models\TypeConge;
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

    public function connectedUserCessationsList()
    {
        $user = Auth::user();

        if (!$user || !$user->employe) {
            return response()->json([
                'message' => 'Employé non trouvé pour l’utilisateur connecté.'
            ], 404);
        }

        // Utiliser la nouvelle méthode du repository
        $cessations = $this->cessationRepository->getByConnectedUserEmployeId($user->employe->id);

        if ($cessations->isEmpty()) {
            return response()->json([
                'message' => 'Aucune cessation pour cet employé'
            ]);
        }

        return response()->json([
            'message' => 'Liste des cessations de l\'employé connecté',
            'cessations' => $cessations
        ]);
    }


    public function find(int $id)
    {
        return $this->cessationRepository->getById($id);
    }


    public function create(array $data)
    {
        $employe = Employe::where('user_id', Auth::id())->firstOrFail();
        $typeCongeId = TypeConge::findOrFail($data['type_conge_id']);
        
        $dateDebutStr = $data['date_debut'];
        $dateFinStr = $data['date_fin'];

        $dateDebut = Carbon::createFromFormat('Y-m-d', $dateDebutStr)->startOfDay();
        $dateFin = Carbon::createFromFormat('Y-m-d', $dateFinStr)->startOfDay();

        $nbJours = $dateDebut->diffInDays($dateFin) + 1;
    

        if (isset($data['piece_jointe'])) {
            $path = $data['piece_jointe']->store('cessations_pieces');
            $data['piece_jointe'] = $path;
        }

        return $this->cessationRepository->store([
            // 'conge_id' => $conge->id,
            'employe_id' => $employe->id,
            'type_conge_id' => $typeCongeId->id,
            // 'date_debut' => $data['date_debut'],
            // 'date_fin' => $data['date_fin'],
            'date_debut' => $dateDebutStr,
            'date_fin' => $dateFinStr,
            'motif' => $data['motif'],
            'nombre_jours' => $nbJours,
            // 'piece_jointe' => $this->uploadFichier($data['piece_jointe']),
        ]);
    }

    public function demandeForEmploye(array $data, int $employeId)
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $employe = Employe::findOrFail($employeId);
        $typeCongeId = TypeConge::findOrFail($data['type_conge_id']);

        $dateDebutStr = $data['date_debut'];
        $dateFinStr = $data['date_fin'];

        $dateDebut = Carbon::createFromFormat('Y-m-d', $dateDebutStr)->startOfDay();
        $dateFin = Carbon::createFromFormat('Y-m-d', $dateFinStr)->startOfDay();

        $nbJours = $dateDebut->diffInDays($dateFin) + 1;

        // Vérifier si l'utilisateur connecté est RH
        if (!$user->hasRole('rh')) {
            throw ValidationException::withMessages([
                'Erreur Profil' => 'Seul un RH peut faire une demande pour un employé'
            ]);
        }

        // Vérifier que le RH ne fait pas une demande pour lui-même
        if ($employe->user_id === $user->id) {
            throw ValidationException::withMessages([
                'Erreur Employé' => 'Un RH ne peut pas faire une demande pour lui-même.'
            ]);
        }
        if (isset($data['piece_jointe'])) {
            $path = $data['piece_jointe']->store('cessations_pieces');
            $data['piece_jointe'] = $path;
        }

        // $nbJours = $this->calculJoursOuvrables($data['dateDebut'], $data['dateFin']);

        return $this->cessationRepository->store([
            // 'conge_id' => $conge->id,
            'employe_id' => $employe->id,
            'type_conge_id' => $typeCongeId->id,
            'date_debut' => $dateDebutStr,
            'date_fin' => $dateFinStr,
            'motif' => $data['motif'],
            'nombre_jours' => $nbJours,
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
        $employe = $cessation->employe;

        if ($decision === 'valide') {
            // Garder les dates comme chaînes pour éviter les problèmes de conversion
            $dateDebutStr = $data['date_debut'];
            $dateFinStr = $data['date_fin'];

            // Créer des objets Carbon uniquement pour le calcul du nombre de jours
            $dateDebut = Carbon::createFromFormat('Y-m-d', $dateDebutStr)->startOfDay();
            $dateFin = Carbon::createFromFormat('Y-m-d', $dateFinStr)->startOfDay();

            // Calcul simple : différence en jours + 1 (pour inclure le jour de début et de fin)
            $nbJours = $dateDebut->diffInDays($dateFin) + 1;

            if ($employe->solde_conge_jours < $nbJours) {
                throw ValidationException::withMessages([
                    'solde' => 'Le solde de congé est insuffisant.'
                ]);
            }

            // Mise à jour avec les chaînes de caractères originales
            $cessation->update([
                'statut' => 'valide',
                'date_debut' => $dateDebutStr,
                'date_fin' => $dateFinStr,
                'nombre_jours' => $nbJours,
                'commentaire' => $data['commentaire'] ?? null,
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

    // public function traiterCessation(int $id, array $data)
    // {
    //     $cessation = $this->cessationRepository->findOrFail($id);

    //     if ($cessation->statut !== 'en_attente') {
    //         throw new \Exception('Cette cessation a déjà été traitée.');
    //     }

    //     $decision = $data['decision'];
    //     // $employe = $cessation->conge->employe;
    //     $employe = $cessation->employe;

    //     if ($decision === 'valide') {

    //         $dateDebut = Carbon::parse($data['date_debut']);
    //         $dateFin = Carbon::parse($data['date_fin']);

    //         $nbJours = $this->calculJoursOuvrables($dateDebut, $dateFin);

    //         if ($employe->solde_conge_jours < $nbJours) {
    //             throw ValidationException::withMessages([
    //                 'solde' => 'Le solde de congé est insuffisant.'
    //             ]);
    //         }

    //         // Mise à jour
    //         $cessation->update([
    //             'statut' => 'valide',
    //             'date_debut' => $dateDebut,
    //             'date_fin' => $dateFin,
    //             'nombre_jours' => $nbJours,
    //             'commentaire' => $data['commentaire'] ?? null,
    //             // 'fiche_cessation_pdf' => $this->uploadFichier($cessation)
    //         ]);

    //         // Déduction du solde
    //         $employe->decrement('solde_conge_jours', $nbJours);
    //     } elseif ($decision === 'rejete') {
    //         if (empty($data['motif'])) {
    //             throw ValidationException::withMessages([
    //                 'motif' => 'Le motif de rejet est requis.'
    //             ]);
    //         }

    //         $cessation->update([
    //             'statut' => 'rejete',
    //             'motif' => $data['motif'],
    //             'commentaire' => $data['commentaire'] ?? null,
    //         ]);
    //     } else {
    //         throw ValidationException::withMessages([
    //             'decision' => 'Valeur invalide pour la décision. (valide ou rejete)'
    //         ]);
    //     }

    //     return $cessation;
    // }




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
