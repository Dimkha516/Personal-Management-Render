<?php
namespace App\Services;

use App\Repositories\ChauffeurRepository;

class ChauffeurService {

    protected $chauffeurRepository;

    public function __construct(ChauffeurRepository $chauffeurRepository)
    {
        $this->chauffeurRepository = $chauffeurRepository;
    }


    public function getAll()
    {
        return $this->chauffeurRepository->getAll();
    }

    public function getById(int $id) {
        return $this->chauffeurRepository->getById($id);
    }

    public function createChauffeur(array $data) {
        return $this->chauffeurRepository->store($data);
    }
}