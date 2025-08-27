<?php

namespace App\Services;

use App\Repositories\FonctionRepository;

class FonctionService
{
    protected $fonctionRepository;

    public function __construct(FonctionRepository $fonctionRepository)
    {
        $this->fonctionRepository = $fonctionRepository;
    }

    public function getAllFonctions()
    {
        return $this->fonctionRepository->getAllFonctions();
    }


    public function getFonctionById(int $id)
    {
        return $this->fonctionRepository->getFonctionId($id);
    }


    public function createFonction(array $data)
    {
        return $this->fonctionRepository->createFonction($data);
    }
}
