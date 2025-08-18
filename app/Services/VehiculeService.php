<?php
namespace App\Services;

use App\Repositories\VehiculeRepository;

class VehiculeService {
    protected $vehiculeRepository;

    public function __construct(VehiculeRepository $vehiculeRepository)
    {
        $this->vehiculeRepository = $vehiculeRepository;
    }


    public function getAll()
    {
        return $this->vehiculeRepository->getAll();
    }

    public function getById(int $id) {
        return $this->vehiculeRepository->getById($id);
    }

    public function createVehicule(array $data) {
        return $this->vehiculeRepository->store($data);
    }

}