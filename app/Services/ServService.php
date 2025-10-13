<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\Request;

class ServService
{
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices()
    {
        return $this->serviceRepository->getAllServices();
    }


    public function getServiceById(int $id)
    {
        return $this->serviceRepository->getServiceById($id);
    }


    public function createService(array $data)
    {
        return $this->serviceRepository->createService($data);
    }

    public function addChefService(int $id)
    {

        $service = $this->serviceRepository->getServiceById($id);
        if (!$service) {
            return response()->json(['message' => 'service non trouvé']);
        }

        return response()->json([
            'message' => 'service trouvé',
            'employe' => $service
        ]);
    }


    /**
     * Récupérer tous les chefs de service
     */
    public function getChefsService()
    {
        // On récupère tous les services avec leurs chefs
        return Service::with('chef')
            ->whereNotNull('chef_service_id')
            ->get()
            ->map(function ($service) {
                return [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'chef' => [
                        'id' => $service->chef->id ?? null,
                        'nom' => $service->chef->nom ?? null,
                        'prenom' => $service->chef->prenom ?? null,
                        'email' => $service->chef->email ?? null,
                        'telephone' => $service->chef->telephone ?? null,
                    ]
                ];
            });
    }
}
