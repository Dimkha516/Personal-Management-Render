<?php
namespace App\Services;

use App\Repositories\ServiceRepository;

class ServService {
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices() {
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

}