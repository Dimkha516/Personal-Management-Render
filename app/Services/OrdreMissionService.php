<?php

namespace App\Services;

use App\Models\Employe;
use App\Repositories\OrdreMissionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrdreMissionService
{

    protected $ordreMissionRepository;

    public function __construct(OrdreMissionRepository $ordreMissionRepository)
    {
        $this->ordreMissionRepository = $ordreMissionRepository;
    }

    public function getAllOM()
    {
        return $this->ordreMissionRepository->getAll();
    }

    public function getOMById(int $id)
    {
        return $this->ordreMissionRepository->getById($id);
    }

    public function createOM(array $data)
    {
        $employe = Employe::where('user_id', Auth::id())->firstOrFail();

        // Récupération du chef de service de l'employé demandeur
        $chefServiceEmploye = $employe->service?->chef;

        dd($chefServiceEmploye); // Pour debug
        // dd($employe);   


        $destination = $data['destination'];
        $kilometrage = $data['kilometrage'];
        $VehiculeID = $data['vehicule_id'] ?? null;
        $chauffeurID = $data['chauffeur_id'] ?? null;
        $dateDepart = $data['date_depart'];
        $dateDebut = $data['date_debut'];
        $dateFin = $data['date_fin'];
        $nombreJours = (new \Carbon\Carbon($data['date_debut']))->diffInDays(new \Carbon\Carbon($data['date_fin'])) + 1;

        return $this->ordreMissionRepository->create([
            'demandeur_id' => $employe->id,
            'destination' => $destination,
            'kilometrage' => $kilometrage,
            'vehicule_id' => $VehiculeID,
            'chauffeur_id' => $chauffeurID,
            'date_depart' => $dateDepart,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nombreJours
        ]);
    }

    public function deleteOM(int $id)
    {
        return $this->ordreMissionRepository->destroy($id);
    }
}
