<?php

namespace App\Services;

use App\Mail\OrdreMissionNotificationMail;
use App\Models\Employe;
use App\Repositories\OrdreMissionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


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

    public function connectedUserOM()
    {
        $user = Auth::user();

        if (!$user || !$user->employe) {
            return response()->json([
                'message' => 'Employé non trouvé pour l’utilisateur connecté.'
            ], 404);
        }

        $ordresMission = $this->ordreMissionRepository->getByDemandeurId($user->employe->id);

        if ($ordresMission->isEmpty()) {
            return response()->json([
                'message' => 'Aucun ordre de mission pour cet employé'
            ]);
        }

        return response()->json([
            'message' => 'Liste des ordres de mission de l\'employé connecté',
            'ordres_mission' => $ordresMission
        ]);
    }


    public function createOM(array $data)
    {
        $employe = Employe::where('user_id', Auth::id())->firstOrFail();

        // Récupération du chef de service de l'employé demandeur
        $chefServiceEmploye = $employe->service?->chef;

        // Pour debug
        //dd($chefServiceEmploye);
        // dd($employe);   


        $ordreMission = $this->ordreMissionRepository->create([
            'demandeur_id' => $employe->id,
            'destination'  => $data['destination'],
            'kilometrage'  => $data['kilometrage'],
            'vehicule_id'  => $data['vehicule_id'] ?? null,
            'chauffeur_id' => $data['chauffeur_id'] ?? null,
            'date_depart'  => $data['date_depart'],
            'date_debut'   => $data['date_debut'],
            'date_fin'     => $data['date_fin'],
            'nb_jours'     => (new \Carbon\Carbon($data['date_debut']))
                ->diffInDays(new \Carbon\Carbon($data['date_fin'])) + 1
        ]);

        // dd($ordreMission);

        // Envoi de l’email au chef de service
        Mail::to($chefServiceEmploye->user->email) // supposons que chaque employé a un user avec email
            ->send(new OrdreMissionNotificationMail($employe, $ordreMission));

        return $ordreMission;
    }

    public function deleteOM(int $id)
    {
        return $this->ordreMissionRepository->destroy($id);
    }
}
