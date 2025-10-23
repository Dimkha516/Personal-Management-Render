<?php

namespace App\Services;

use App\Mail\OrdreMissionChefParcMail;
use App\Mail\OrdreMissionNotificationMail;
use App\Mail\OrdreMissionValidationChefMail;
use App\Models\Employe;
use App\Models\OrdreMission;
use App\Models\User;
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
            'motif_demande' => $data['motif_demande'],
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
        
        // Envoi de l’email au chef de service: SERVICE INDISPONIBLE SUR RENDER
        // Mail::to($chefServiceEmploye->user->email) // supposons que chaque employé a un user avec email
        // ->send(new OrdreMissionNotificationMail($employe, $ordreMission));
        
        return $ordreMission;
    }

    public function deleteOM(int $id)
    {
        return $this->ordreMissionRepository->destroy($id);
    }

    public function traiterParChefService(int $ordreMissionId, string $action, ?string $motifRejet = null)
    {
        $ordreMission = $this->ordreMissionRepository->getById($ordreMissionId);
        if (!$ordreMission) {
            throw new \Exception("Ordre de mission introuvable");
        }

        // Employé connecté (chef de service)
        $chef = Employe::where('user_id', Auth::id())->firstOrFail();

        // Vérifier que le chef est bien celui du service du demandeur
        if ($ordreMission->demandeur->service->chef->id !== $chef->id) {
            throw new \Exception("Vous n'êtes pas autorisé à traiter cet ordre de mission.");
        }

        if ($ordreMission->chef_service_validation) {
            throw new \Exception("Cet ordre de mission a déjà été traité par le chef service concerné");
        }

        if ($action === 'approuver') {
            $ordreMission->update([
                'chef_service_validation' => true,
                'motif_rejet' => null
            ]);

            // Notification DG & Secrétaire:---------------SERVICE MAILING INDISPONIBLE SUR RENDER
            // $destinataires = User::whereIn('role', ['DG', 'secretaire'])->get();
            // foreach ($destinataires as $user) {
            //     Mail::to($user->email)->send(
            //         new OrdreMissionValidationChefMail($ordreMission, $chef)
            //     );
            // }
        } elseif ($action === 'rejeter') {
            if (!$motifRejet) {
                throw new \Exception("Un motif de rejet est obligatoire.");
            }
            $ordreMission->update([
                'statut' => 'rejete',
                'motif_rejet' => $motifRejet
            ]);
        } else {
            throw new \Exception("Action invalide. Utiliser 'approuver' ou 'rejeter'.");
        }

        return $ordreMission;
    }

    public function traiterParDirection(array $data, $ordreMissionId)
    {
        // Récupérer l'ordre de mission
        $ordreMission = OrdreMission::findOrFail($ordreMissionId);

        // Vérifier si l'utilisateur connecté est DG ou secrétaire

        //---------------------- VERIFICATION A FAIRE SUR LE CONTROLLER AVEC LA GESTION DES PERMISSIONS
        // $employe = Employe::where('user_id', Auth::id())->firstOrFail();
        // $role = $employe->user->role ?? null;

        // if (!in_array($role, ['DG', 'secretaire'])) {
        //     abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        // }

        // Vérifier la décision
        if ($data['decision'] === 'rejete') {
            $ordreMission->update([
                'statut' => 'rejete',
                'motif_rejet' => $data['motif_rejet'] ?? 'Aucun motif fourni',
            ]);
        }

        // En cas d'approbation
        $ordreMission->update([
            'statut' => 'approuve',
            'qte_carburant' => $data['qte_carburant'],
            'carburant_valide' => true,
        ]);

        // Notification du chef de parc: ------------------PARTIE A REVOIR SI NECESSAIRE OU PAS
        // $chefParc = Employe::whereHas('service', fn($q) => $q->where('name', 'parc'))
        //     ->whereHas('service.chef')
        //     ->with('service.chef.user')
        //     ->first()?->service?->chef;

        // if ($chefParc && $chefParc->user) {
        //     Mail::to($chefParc->user->email)
        //         ->send(new OrdreMissionChefParcMail($ordreMission));
        // }

        return $ordreMission;
    }

    public function traiterParChefParc(array $data, $ordreMissionId)
    {
        // Récupération de l’ordre de mission
        $ordreMission = OrdreMission::findOrFail($ordreMissionId);

        // Vérification du rôle (Chef de Parc)
        $employe = Employe::where('user_id', Auth::id())->firstOrFail();
        $role = $employe->user->role ?? null;

        //------------------Vérification a faire sur le controller
        // if ($role !== 'chef_parc') {
        //     abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        // }

        // Chauffeur et véhicule fournis ou non
        $vehiculeId = $data['vehicule_id'] ?? $ordreMission->vehicule_id;
        $chauffeurId = $data['chauffeur_id'] ?? $ordreMission->chauffeur_id;

        if (!$vehiculeId || !$chauffeurId) {
            abort(400, "Veuillez renseigner le chauffeur et le véhicule.");
        }

        // Mise à jour de l’ordre de mission
        $ordreMission->update([
            'vehicule_id' => $vehiculeId,
            'chauffeur_id' => $chauffeurId,
            // 'statut' => 'pret', // ou "assigné"
        ]);

        // NOTIFIER CHEUFFEUR: SERVICE MAILING INDISPON SUR RENDER

        // Notification du demandeur --------------------- PARTIE A REVOIR SI NECESSAIRE OU PAS
        // $demandeur = $ordreMission->demandeur;
        // if ($demandeur && $demandeur->user) {
        //     Mail::to($demandeur->user->email)
        //         ->send(new \App\Mail\OrdreMissionPretMail($ordreMission));
        // }

        return $ordreMission;
    }
}
