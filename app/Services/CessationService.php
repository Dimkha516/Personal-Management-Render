<?php

namespace App\Services;

use App\Interfaces\CessationInterface;
use App\Models\Conge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CessationService
{
    protected $cessationRepository;

    public function __construct(CessationInterface $cessationRepository)
    {
        $this->cessationRepository = $cessationRepository;
    }


    public function create(array $data)
    {

        $user = Auth::user();

        $conge = Conge::findOrFail($data['conge_id']);

        if ($conge->statut !== 'approuve') {

            throw new \Exception('le congé doit être validé pour soumettre une cessation');
        }
 
        return $this->cessationRepository->store([
            'conge_id' => $conge->id,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'motif' => $data['motif']
            // 'piece_jointe' => $this->uploadFichier($data['piece_jointe']),
        ]);
    }

    public function valider(int $id, array $data)
    {
        $cessation = $this->cessationRepository->findOrFail($id);

        if ($cessation->statut !== 'en_attente') {
            throw new \Exception('Déjà traité');
        }

        $dateDebut = Carbon::parse($data['date_debut']);
        $dateFin = Carbon::parse($data['date_fin']);
        $nbJours = $this->calculJoursOuvrables($dateDebut, $dateFin);


        $cessation->update([
            'statut' => 'valide',
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nombre_jours' => $nbJours,
            'commentaire' => $data['commentaire'],
            // 'fiche_cessation_pdf' => $this->uploadFichier($cessation), 
        ]);

        // Déduire le solde:
        $employe = $cessation->conge->employe;
        $employe->decrement('solde_conge_jours', $nbJours);

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
