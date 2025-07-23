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
}
